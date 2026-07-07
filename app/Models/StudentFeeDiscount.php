<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentFeeDiscount extends Model
{
    use SoftDeletes;
    protected $table = 'student_fee_discounts';
    protected $fillable = [
        'tenant_id',
        'SchoolId',
        'IsActive',
        'CreatedBy',
        'ModifiedBy',
        'ClassId',
        'SectionId',
        'SessionId',
        'StudentId',
        'CampusFeesMasterId',
        'DiscountAmount',
        'BalanceFeeAfterDiscount',
        'DiscountType',
        'TotalFee',
        'loadedCampusMaster',
        'imported_student_fee_discount_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function studentRel()
    {
        return $this->hasOne(Student::class, 'id', 'StudentId')->select('id','FirstName','ClassId','SectionId','LastName');
    }

    public function SessionRel()
    {
        return $this->hasOne(LmsSession::class, 'id', 'SessionId');
    }
    
    public function ClassRel()
    {
        return $this->hasOne(Classes::class, 'id', 'ClassId');
    }
    
    public function SectionRel()
    {
        return $this->hasOne(Section::class, 'id', 'SectionId');
    }
    
    public function CampusFeesMasterRel()
    {
        return $this->hasOne(CampusFeesMaster::class, 'id', 'CampusFeesMasterId')->withTrashed()->select('id','FeesTypeNId','SessionId','Amount');
    }
}
