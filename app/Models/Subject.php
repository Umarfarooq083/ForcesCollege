<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['SubjectName', 'SubjectType', 'SubjectCode', 'ClassId', 'program_level_id', 'tenant_id', 'CreatedBy'];

    public function classes()
    {
        return $this->hasOne(Classes::class, 'id', 'ClassId')->select('id', 'ClassName', 'program_id');
    }

    public function programLevel()
    {
        return $this->belongsTo(ProgramLevel::class, 'program_level_id', 'id');
    }

    public function scopeClassId($query, $classId)
    {
        return $query->where('ClassId', $classId);
    }
}
