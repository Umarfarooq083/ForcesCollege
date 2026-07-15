<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignClassTeacher extends Model
{
    use SoftDeletes;

    protected $table = 'assign_class_teacher';

    protected $fillable = [
        'tenant_id',
        'ClassId',
        'SectionId',
        'StaffId',
        'SchoolId',
        'IsActive',
        'CreatedBy',
        'ModifiedBy',
        'SessionId',
        'imported_assign_class_teacher_id',
    ];

    public function scopeTenant($query)
    {
        return $query->where('tenant_id', tenant('id'));
    }

    public function ClassRel(): HasOne
    {
        return $this->hasOne(Classes::class, 'id', 'ClassId')->select('id', 'ClassName');
    }

    public function SectionRel(): HasOne
    {
        return $this->hasOne(Section::class, 'id', 'SectionId')->select('id', 'SectionName');
    }

    public function StaffRel(): HasOne
    {
        return $this->hasOne(Staff::class, 'id', 'StaffId')->select('id', 'FirstName', 'LastName');
    }
}
