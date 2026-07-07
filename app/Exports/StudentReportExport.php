<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentReportExport implements FromCollection, WithHeadings
{
    public $class_id;
    public $section_id;
    public $gender;
    public $roll_no;

    public function __construct($class_id, $section_id, $gender, $roll_no)
    {
        $this->class_id = $class_id;
        $this->section_id = $section_id;
        $this->gender = $gender;
        $this->roll_no = $roll_no;
    }

    public function collection()
    {
        $students = Student::query()
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->select('id','RollNumber','ClassId','SectionId','Gender','FirstName',
                'LastName','FatherName','FatherPhone','GuardianPhone','DateOfBirth',
                'Religion','Email','AdmissionDate','BloodGroup')
            ->with(['class', 'section'])
            ->when($this->class_id, fn($q) => $q->where('ClassId', $this->class_id))
            ->when($this->section_id, fn($q) => $q->where('SectionId', $this->section_id))
            ->when($this->gender, fn($q) => $q->where('Gender', $this->gender))
            ->when($this->roll_no, fn($q) => $q->where('RollNumber', $this->roll_no))
            ->get();

            return $students->map(function ($student) {
            return [
                'RollNumber'        => $student->RollNumber,
                'ClassName'         => $student->class->ClassName ?? '',
                'SectionName'       => $student->section->SectionName ?? '',
                'Name'              => $student->FirstName . ' ' . $student->LastName,
                'FatherName'        => $student->FatherName,
                'FatherPhone'       => $student->FatherPhone,
                'GuardianPhone'     => $student->GuardianPhone ?? '',
                'Gender'            => $student->Gender,
                'DateOfBirth'       => $student->DateOfBirth,
                'Religion'          => $student->Religion ?? '',
                'Email'             => $student->Email ?? '',
                'AdmissionDate'     => $student->AdmissionDate,
                'BloodGroup'        => $student->BloodGroup ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Roll Number',
            'Class Name',
            'Section Name',
            'Name',
            'Father Name',
            'Father Phone',
            'Guardian Phone',
            'Gender',
            'Date Of Birth',
            'Religion',
            'Email',
            'Admission Date',
            'Blood Group',
        ];
    }
}
