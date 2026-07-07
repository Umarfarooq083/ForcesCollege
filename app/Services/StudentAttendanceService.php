<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentAttendanceService
{   
    public function attendanceList($request): array
    {
        $data['classesList'] = $this->getClasses();
        $data['sections'] = $this->getClassesSections($data['classesList']);
        $data['students'] = $this->createStudentAttendance($request);
        $data['existingAttendance'] = getExistingAttendance($request, $data['students'], StudentAttendance::class, 'StudentId', 'AttendanceType');
        return $data;
    }

    public function getClasses(): Collection
    {
        return Classes::whereIn('class_type_id', campusClassList())->where('IsActive', 1)->get(['id', 'ClassName', 'tenant_id']);
    }

    public function getClassesSections($classes): Collection
    {
        return Section::whereIn('ClassId', $classes->pluck('id'))->where('IsActive', 1)->where('tenant_id', tenant('id'))->get();
    }

    public function createStudentAttendance($request): Collection
    {
        $students_query = Student::where('IsActive',1)->where('tenant_id', tenant('id'))->with('class:id,tenant_id,ClassName','section.sectionType');

        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');

        if(!empty($request->all())){
            if ($classId) {
                $students_query->where('ClassId', $classId);
            }
    
            if ($sectionId) {
                $students_query->where('SectionId', $sectionId);
            }
    
            return $students = $students_query->get(['id','tenant_id','ClassId','SectionId','IsActive','FirstName', 'LastName']);
        }else{
           return $students = collect([]);
        }
    }

    public function submitStudentAttendance($request): void
    {
        $attendance = $request->input('attendance');
        $studentIds = array_keys($attendance);
        $currentSession = fetchCurrentSession();
        $existingAttendances = StudentAttendance::whereIn('StudentId', $studentIds)
            ->where('AttendanceDate', $request->input('date'))
            ->get()
            ->keyBy('StudentId');

        $insertData = [];
        $updateData = [];

        foreach ($attendance as $studentId => $status) {
            $attendanceType = match($status) {
                '0' => 'Absent',
                '1' => 'Present', 
                '2' => 'Leave',
                default => 'Present'
            };

            if (isset($existingAttendances[$studentId])) {
                $updateData[] = [
                    'id' => $existingAttendances[$studentId]->id,
                    'AttendanceType' => $attendanceType,
                    'ModifiedBy' => auth()->id(),
                    'updated_at' => now(),
                ];
            } else {
                $insertData[] = [
                    'SessionId' => $currentSession->id,
                    'tenant_id' => tenant('id'),
                    'ClassId' => (int) $request->input('class_id'),
                    'SectionId' => (int) $request->input('section_id'),
                    'StudentId' => $studentId,
                    'AttendanceDate' => $request->input('date'),
                    'AttendanceType' => $attendanceType,
                    'IsActive' => true,
                    'CreatedBy' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Step 2: Insert all new records at once
        if (!empty($insertData)) {
            $created = StudentAttendance::insert($insertData);

            if($created){
                userActivityLogs('Student Attendance Created and By User ID: '.auth()->user()->id.'', StudentLog::class);
            }
        }

        // Step 3: Update all existing records (batch update)
        if (!empty($updateData)) {
            foreach ($updateData as $row) {
                DB::table('student_attendances')
                    ->where('id', $row['id'])
                    ->update([
                        'AttendanceType' => $row['AttendanceType'],
                        'ModifiedBy' => $row['ModifiedBy'],
                        'updated_at' => $row['updated_at'],
                    ]);
            }

            userActivityLogs('Student Attendance Updated and By User ID: '.auth()->user()->id.'', StudentLog::class);
        }
    }

}