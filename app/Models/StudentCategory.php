<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentCategory extends Model
{
    use SoftDeletes;

    protected $table = 'student_categories';

    protected $fillable = [
        'SchoolId',
        'IsActive',
        'CreatedBy',
        'CreatedDate',
        'ModifiedBy',
        'ModifiedDate',
        'SessionId',
        'CategoryName',
        'StudentCategoryGroupId',
    ];
}
