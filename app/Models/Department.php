<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;

    protected $table = 'department';

    protected $fillable = [
        'SessionId', 'DepartmentName', 'IsActive', 'CreatedBy', 'ModifiedBy', 'deleted_at', 'tenant_id', 'Code', 'imported_department_id',
    ];
}
