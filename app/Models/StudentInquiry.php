<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentInquiry extends Model
{
    protected $fillable = [
        'SessionId', 'Date', 'ClassId', 'Name', 'LastName', 'BirthDate', 'Gender',
        'PreviousInstitute', 'Address', 'FatherName', 'FatherPhoneNo', 'MotherName',
        'MotherPhoneNo', 'SourceId', 'ReferenceId', 'IsSmsSent', 'tenant_id',
        'CreatedBy', 'ModifiedBy', 'guardian_id', 'guardian_relation_id', 'status', 'imported_inquiry_id',
    ];

    public function scopeTenant($query)
    {
        return $query->where('tenant_id', tenant('id'));
    }

    public function scopeById($query, $id)
    {
        return $query->where('id', $id);
    }

    protected $casts = [
        'Date' => 'date:Y-m-d',
        'BirthDate' => 'date:Y-m-d',
        'created_at' => 'date:Y-m-d',
        'status' => 'boolean',
        'IsSmsSent' => 'boolean',
    ];

    public function source()
    {
        return $this->hasOne(Source::class, 'id', 'SourceId');
    }

    public function guardian()
    {
        return $this->hasOne(GuardianInfo::class, 'id', 'guardian_id');
    }

    public function class()
    {
        return $this->hasOne(Classes::class, 'id', 'ClassId');
    }

    public function inqSession()
    {
        return $this->hasOne(LmsSession::class, 'id', 'SessionId')->select('start_date', 'end_date', 'id');
    }

    public function guardianRelation()
    {
        return $this->belongsTo(GuardianRelation::class, 'guardian_relation_id', 'id');
    }

    public function studentInquiryRelation()
    {
        return $this->belongsTo(Student::class, 'id', 'AdmissionEnquiryId')->select('id', 'AdmissionEnquiryId');
    }
}
