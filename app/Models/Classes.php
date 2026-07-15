<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'SessionId', 'ClassName', 'IsActive', 'CreatedBy', 'ModifiedBy', 'deleted_at', 'tenant_id', 'ClassOrder', 'program_id',
    ];

    public function scopeTenant($query)
    {
        return $query->where('tenant_id', tenant('id'));
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'CreatedBy')->select('id', 'name');
    }

    public function subject()
    {
        return $this->hasOne(Subject::class, 'ClassId', 'id')->select('id', 'SubjectName', 'ClassId');
    }

    // public function Session()
    // {
    //     return $this->hasOne(User::class,'id','CreatedBy')->select('id','name');
    // }

    public function sections()
    {
        return $this->hasMany(Section::class, 'ClassId', 'id')->select('id', 'SectionName', 'ClassId', 'SectionType')->where('tenant_id', tenant('id'));
    }

    public function assignedTeacher()
    {
        return $this->hasOne(AssignClassTeacher::class, 'ClassId', 'id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'ClassId', 'id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'id');
    }
}
