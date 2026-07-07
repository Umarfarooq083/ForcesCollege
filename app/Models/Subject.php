<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['SubjectName', 'SubjectType', 'SubjectCode', 'ClassId', 'tenant_id', 'CreatedBy'];

    public function classes()
    {
        return $this->hasOne(Classes::class, 'id', 'ClassId')->select('id', 'ClassName');
    }

    public function scopeClassId($query, $classId)
    {
        return $query->where('ClassId', $classId);
    }
}
