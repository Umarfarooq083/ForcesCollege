<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\ClassTimeTable;
use App\Models\HumanResourceLog;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Subject;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClassTimeTableService
{
    public function index($request)
    {
        $timetables = ClassTimeTable::query();

        // Check if no filter is applied → return empty result
        if (! $request->filled('StaffId') && ! $request->filled('month') && ! $request->filled('date')) {
            return $timetables->whereRaw('1=0')->paginate(25)->through(fn ($timetable) => []);
        }

        // Staff Filter
        if ($request->filled('StaffId')) {
            $timetables->where('StaffId', $request->StaffId);
        }

        // Month Filter (based on date column)
        if ($request->filled('month')) {
            $timetables->whereMonth('date', $request->month);
        }

        // Date Filter
        if ($request->filled('date')) {
            $timetables->whereDate('date', $request->date);
        }

        return $timetablesService = $timetables->with('class', 'section', 'subject', 'staff')
            ->orderBy('id', 'desc')
            ->paginate(25)->withQueryString()
            ->appends($request->all())
            ->through(fn ($timetable) => [
                'id' => $timetable->id,
                'ClassName' => $timetable->class?->ClassName,
                'SectionName' => $timetable->section?->SectionName,
                'SubjectName' => $timetable->subject?->SubjectName,
                'StaffName' => $timetable->staff?->FirstName.' '.$timetable->staff?->LastName,
                'Day' => $timetable->Day,
                'Date' => $timetable->date,
                'TimeFrom' => $timetable->TimeFromFormatted,
                'TimeTo' => $timetable->TimeToFormatted,
            ]);
    }

    public function getStaffList()
    {
        return Staff::select('id', 'FirstName', 'LastName')->where('tenant_id', tenant('id'))->get();
    }

    public function getClassesList()
    {
        return Classes::select('id', 'ClassName')->get();
    }

    public function getSectionsAndSubjects($classId)
    {
        return [
            'sections' => Section::where('ClassId', $classId)->where('tenant_id', tenant('id'))->get(),
            'subjects' => Subject::where('ClassId', $classId)->where('tenant_id', tenant('id'))->get(),
        ];
    }

    // public function createTimetableGroup($request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $groupId = (DB::table('class_time_tables')->max('ClassTimeTableGroupId') ?? 0) + 1;

    //         foreach ($request->rows as $row) {
    //             $timeFrom = $this->formatTime($row['TimeFrom']);
    //             $timeTo = $this->formatTime($row['TimeTo']);
    //             $day = ucfirst($request->Day);

    //             foreach ($request->DatesArray as $date) {

    //                 if ($this->hasStaffTimeConflict($request->StaffId, $request->Day, $row, $date)) {
    //                     throw ValidationException::withMessages([
    //                         'time_conflict' => "The selected staff already has a class scheduled on {$day} from {$timeFrom} to {$timeTo}."
    //                     ]);
    //                 }

    //                 if ($this->hasClassConflict($request->Day, $row, $date)) {
    //                     throw ValidationException::withMessages([
    //                         'class_conflict' => "A class/section/subject conflict exists on {$day} from {$timeFrom} to {$timeTo}."
    //                     ]);
    //                 }

    //                 ClassTimeTable::create([
    //                     'tenant_id' => auth()->user()->tenant_id,
    //                     'ClassId' => $row['ClassId'],
    //                     'SectionId' => $row['SectionId'],
    //                     'SubjectId' => $row['SubjectId'],
    //                     'StaffId' => $request->StaffId,
    //                     'Day' => $request->Day,
    //                     'Date' => $date,
    //                     'TimeFrom' => $row['TimeFrom'],
    //                     'TimeTo' => $row['TimeTo'],
    //                     'CreatedBy' => auth()->id(),
    //                     'ClassTimeTableGroupId' => $groupId,
    //                 ]);
    //             }
    //         }

    //         DB::commit();

    //     }
    //     catch (\Throwable $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

    public function createTimetableGroup($request)
    {
        DB::beginTransaction();

        try {
            $groupId = (DB::table('class_time_tables')->max('ClassTimeTableGroupId') ?? 0) + 1;
            $dates = $request->DatesArray;

            foreach ($request->rows as $row) {
                $timeFrom = $this->formatTime($row['TimeFrom']);
                $timeTo = $this->formatTime($row['TimeTo']);
                $day = ucfirst($request->Day);

                if ($this->hasStaffTimeConflict($request->StaffId, $day, $row, $dates)) {
                    throw ValidationException::withMessages([
                        'time_conflict' => "The selected staff already has a class scheduled on {$day} from {$timeFrom} to {$timeTo}.",
                    ]);
                }

                if ($this->hasClassConflict($day, $row, $dates)) {
                    throw ValidationException::withMessages([
                        'class_conflict' => "A class/section/subject conflict exists on {$day} from {$timeFrom} to {$timeTo}.",
                    ]);
                }
                $currentSession = fetchCurrentSession();
                $insertData = [];
                foreach ($dates as $date) {
                    $insertData[] = [
                        'tenant_id' => tenant('id'),
                        'ClassId' => $row['ClassId'],
                        'SectionId' => $row['SectionId'],
                        'SubjectId' => $row['SubjectId'],
                        'SessionId' => $currentSession?->id,
                        'StaffId' => $request->StaffId,
                        'Day' => $request->Day,
                        'Date' => $date,
                        'TimeFrom' => $row['TimeFrom'],
                        'TimeTo' => $row['TimeTo'],
                        'CreatedBy' => auth()->id(),
                        'ClassTimeTableGroupId' => $groupId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $created = ClassTimeTable::insert($insertData);
                if ($created) {
                    userActivityLogs('Class Time Table Creation and By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function hasStaffTimeConflict($staffId, $day, $row, $dates): bool
    {
        return ClassTimeTable::where('StaffId', $staffId)
            ->whereIn('Date', $dates)
            // ->where('Day', $day)
            ->where('TimeFrom', '<', $row['TimeTo'])
            ->where('TimeTo', '>', $row['TimeFrom'])
            ->exists();
    }

    private function hasClassConflict($day, $row, $dates): bool
    {
        return ClassTimeTable::where('ClassId', $row['ClassId'])
            ->where('SectionId', $row['SectionId'])
            ->where('SubjectId', $row['SubjectId'])
            ->whereIn('Date', $dates)
            // ->where('Day', $day)
            ->where('TimeFrom', '<', $row['TimeTo'])
            ->where('TimeTo', '>', $row['TimeFrom'])
            ->exists();
    }

    private function rollbackWithError(string $key, string $message)
    {
        DB::rollBack();

        return back()->withErrors([$key => $message]);
    }

    private function formatTime($time)
    {
        return Carbon::createFromFormat('H:i', $time)->format('h:i A');
    }
}
