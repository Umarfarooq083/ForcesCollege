<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OptionalFeeMaster extends Model
{
    use SoftDeletes;

    protected $table = 'optional_fee_mapping';

    protected $fillable = [
        'tenant_id',
        'FeesTypeNId',
        'ClassId',
        'SectionId',
        'StudentId',
        'FromMonth',
        'ToMonth',
        'CampusFeesMasterId',
        'SchoolId',
        'IsActive',
        'CreatedBy',
        'ModifiedBy',
        'SessionId',
        'imported_fee_mapping_id',
        'deleted_at',
    ];

    protected $casts = [
        'FromMonth' => 'date:Y-m',
        'ToMonth' => 'date:Y-m',
    ];

    public function scopeTenant($query)
    {
        return $query->where('tenant_id', tenant('id'));
    }

    public function classRel()
    {
        return $this->hasOne(Classes::class, 'id', 'ClassId')->select('id', 'ClassName');
    }

    public function sectionRel()
    {
        return $this->hasOne(Section::class, 'id', 'SectionId')->select('id', 'SectionName');
    }

    public function studentRel()
    {
        return $this->hasOne(Student::class, 'id', 'StudentId')->select('id', 'FirstName', 'LastName');
    }

    public function feeTypeRel()
    {
        return $this->hasOne(FeesType::class, 'id', 'FeesTypeNId')->select('id', 'FeeName');
    }

    public function campusMasterRel()
    {
        return $this->hasOne(CampusFeesMaster::class, 'id', 'CampusFeesMasterId')->select('id', 'Amount');
    }
}
