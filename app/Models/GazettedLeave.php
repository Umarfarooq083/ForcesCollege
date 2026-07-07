<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GazettedLeave extends Model
{
    use SoftDeletes;

    protected $table = 'gazetted_leaves';

    protected $fillable = [
        'tenant_id',
        'title',
        'date',
        'status',
        'CreatedBy',
        'ModifiedBy',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}