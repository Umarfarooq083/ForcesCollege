<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;

    protected $table = 'sectiones';

    protected $fillable = [
        'tenant_id',
        'SessionId',
        'SectionName',
        'ClassId',
        'SchoolId',
        'IsActive',
        'CreatedBy',
        'ModifiedBy',
        'ClassGroupId',
        'SectionType',
        'Strength',
    ];

    public function sectionType()
    {
        return $this->belongsTo(SectionType::class, 'SectionType');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'ClassId');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'CreatedBy')->select('id', 'name');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'SectionId');
    }
}
