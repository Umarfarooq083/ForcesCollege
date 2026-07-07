<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampusFeesMaster extends Model
{
    use SoftDeletes;
    protected $table = 'campus_fees_masters';
    
    protected $casts = [
     'IsActive' => 'boolean',
    ];

    protected $fillable = [
        'SchoolId',
        'IsActive',
        'tenant_id',
        'CreatedBy',
        'ModifiedBy',
        'SessionId',
        'Title',
        'FeesTypeNId',
        'Amount',
        'ClassId',
        'SectionId',
        'import_fee_type_id',
        'import_fee_master_id',
        'deleted_at'
    ];

    public function scopeTenant($query)
    {
        return $query->where('tenant_id',tenant('id'));
    }
    
    public function scopeClass($query,$classId)
    {
        return $query->where('ClassId',$classId);
    }

    public function FeeTypeRel()
    {
        return $this->hasOne(FeesType::class, 'id','FeesTypeNId')->select('id','FeeName','FeesCode','Description','FeeCycle','IsOptional','IsRefundable','Isroyality');
    }

    public function fee_master_class(){
        return $this->hasOne(Classes::class, 'id', 'ClassId');
    }

    public function fee_master_type(){
        return $this->hasOne(FeesType::class, 'id', 'FeesTypeNId');
    }

    public function SessionRel()
    {
        return $this->hasOne(LmsSession::class, 'id','SessionId');
    }
}
