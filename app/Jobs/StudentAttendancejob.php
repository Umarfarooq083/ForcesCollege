<?php

namespace App\Jobs;

use App\Models\StudentAttendance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class StudentAttendancejob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $chunk;

    public int $tries = 3;

    public int $timeout = 120;

    protected $ImportHelper;

    public function __construct(array $chunk, $ImportHelper)
    {
        $this->ImportHelper = $ImportHelper;
        $this->chunk = $chunk;
    }

    public function handle()
    {
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $rows = array_map(function ($item) {
            return [
                'studentId' => $item['studentId'],
                'attendanceType' => $item['attendanceType'],
                'attendanceDate' => $item['attendanceDate'],
                'imported_attendance_id' => $item['id'],
                'sessionId' => $item['sessionId'],
                'isFromMachine' => $item['isFromMachine'],
                'isActive' => $item['isActive'],
                'createdBy' => $item['createdBy'],
                'createdDate' => $item['createdDate'],
            ];
        }, $this->chunk);

        DB::table('imported_student_attendance')->insertOrIgnore($rows);
        $importedIds = array_column($this->chunk, 'id');
        $existingAttendances = StudentAttendance::where('tenant_id', $current_tenant_id)
            ->whereIn('imported_student_attendance_id', $importedIds)
            ->get()
            ->keyBy('imported_student_attendance_id');

        $newAttendances = [];
        $updates = [];

        foreach ($this->chunk as $attendance) {
            if (isset($existingAttendances[$attendance['id']])) {
                $updates[] = [
                    'imported_attendance_id' => $attendance['id'],
                    'internal_attendance_id' => $existingAttendances[$attendance['id']]->id,
                    'internal_student_id' => $existingAttendances[$attendance['id']]->StudentId,
                ];
            } else {
                $currentSessionId = $this->ImportHelper->getSessionId($attendance);
                $currentStudentId = $this->ImportHelper->getStudentId($attendance);
                $newAttendances[] = [
                    'tenant_id' => $current_tenant_id,
                    'IsActive' => $attendance['isActive'],
                    'CreatedBy' => $attendance['createdBy'],
                    'SessionId' => $currentSessionId,
                    'ClassId' => $currentStudentId->ClassId,
                    'SectionId' => $currentStudentId->SectionId,
                    'StudentId' => $currentStudentId->id,
                    'AttendanceType' => $attendance['attendanceType'],
                    'AttendanceDate' => $attendance['attendanceDate'],
                    'IsFromMachine' => 0,
                    'imported_student_attendance_id' => $attendance['id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (! empty($newAttendances)) {
            DB::table('student_attendances')->insert($newAttendances);
            $insertedAttendances = StudentAttendance::where('tenant_id', $current_tenant_id)
                ->whereIn('imported_student_attendance_id', array_column($newAttendances, 'imported_student_attendance_id'))
                ->get()
                ->keyBy('imported_student_attendance_id');
            foreach ($insertedAttendances as $importedId => $attendanceModel) {
                $updates[] = [
                    'imported_attendance_id' => $importedId,
                    'internal_attendance_id' => $attendanceModel->id,
                    'internal_student_id' => $attendanceModel->StudentId,
                ];
            }
        }

        if (! empty($updates)) {
            $updateQuery = 'UPDATE imported_student_attendance SET 
            internal_attendance_id = CASE imported_attendance_id '.
                implode(' ', array_map(fn ($u) => "WHEN {$u['imported_attendance_id']} THEN {$u['internal_attendance_id']}", $updates)).
                ' END,
            internal_student_id = CASE imported_attendance_id '.
                implode(' ', array_map(fn ($u) => "WHEN {$u['imported_attendance_id']} THEN {$u['internal_student_id']}", $updates)).
                ' END
            WHERE imported_attendance_id IN ('.implode(',', array_column($updates, 'imported_attendance_id')).')';

            DB::statement($updateQuery);
        }
    }
}
