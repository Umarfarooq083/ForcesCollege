<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampusCategory extends Model
{
    use SoftDeletes;

    protected $table = 'campus_categories';

    protected $fillable = [
        'SchoolId',
        'IsActive',
        'IsDeleted',
        'CreatedBy',
        'CreatedDate',
        'ModifiedBy',
        'ModifiedDate',
        'SessionId',
        'CategoryName',
        'CategoryCode',
        'CategoryType',
        'Description',
        'tenant_id',
        'SortOrder',
    ];

    protected $casts = [
        'IsActive' => 'boolean',
        'IsDeleted' => 'boolean',
    ];
}
