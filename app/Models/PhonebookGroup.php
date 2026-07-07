<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhonebookGroup extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'phonebook_groups';

    protected $fillable = [
        'tenant_id',
        'school_id',
        'session_id',
        'name',
        'is_active',
        'created_by',
        'modified_by',
    ];

}
