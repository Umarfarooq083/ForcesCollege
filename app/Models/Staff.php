<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'tenant_id',
        'SchoolId',
        'IsActive',
        'DisableReasonId',
        'CreatedBy',
        'ModifiedBy',
        'SessionId',
        'StaffCode',
        'RolesId',
        'DesignationId',
        'DepartmentId',
        'FirstName',
        'LastName',
        'FatherName',
        'MotherName',
        'Email',
        'Gender',
        'DateOfBirth',
        'DateOfJoining',
        'Phone',
        'EmergencyContactNumber',
        'MaritalStatus',
        'PhotoPath',
        'CurrentAddress',
        'PermanentAddress',
        'Qualification',
        'WorkExperience',
        'Note',
        'PANNumber',
        'EPFNo',
        'BasicSalary',
        'employerAmount',
        'TransportAllowance',
        'ComputerAllowance',
        'MobileAllowance',
        'RecreationAllowance',
        'HasProvidentFund',
        'ProvidentFundAmount',
        'HasEOBI',
        'EOBIAmount',
        'ContractType',
        'WorkShift',
        'Location',
        'MedicalLeave',
        'CasualLeave',
        'MaternityLeave',
        'AccountTitle',
        'BankAccountNumber',
        'BankName',
        'IFSCCode',
        'BankBranchName',
        'FacebookURL',
        'TwitterURL',
        'LinkedinURL',
        'InstagramURL',
        'ResumeFilePath',
        'JoiningLetterPath',
        'OtherDocumentsPath',
        'CreateUser',
        'imported_staff_id',
    ];

    protected $appends = ['PhotoPathUrl'];

    protected $casts = [
        'DateOfBirth' => 'date:Y-m-d',
        'DateOfJoining' => 'date:Y-m-d',
        'CreateUser' => 'boolean',
        'HasProvidentFund' => 'boolean',
        'HasEOBI' => 'boolean',
    ];

    public function scopeTenant($query)
    {
        return $query->where('tenant_id', tenant('id'));
    }

    public function StaffRole()
    {
        return $this->hasOne(Roles::class, 'id', 'RolesId')->select('id', 'name');
    }

    public function DesignationRel()
    {
        return $this->hasOne(Designation::class, 'id', 'DesignationId')->select('id', 'DesignationName');
    }

    public function DepartmentRel()
    {
        return $this->hasOne(Department::class, 'id', 'DepartmentId')->select('id', 'DepartmentName', 'Code');
    }

    public function getPhotoPathUrlAttribute()
    {
        if (empty($this->PhotoPath)) {
            return url('assets/images/staff_profile.jpg');
        }

        return url('storage/'.$this->PhotoPath);
    }

    public function attendance()
    {
        return $this->hasMany(StaffAttendance::class, 'StaffId', 'id');
    }

    public function disabledReason()
    {
        return $this->hasOne(StaffDisableReason::class, 'id', 'DisableReasonId');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'SchoolId', 'id');
    }
}
