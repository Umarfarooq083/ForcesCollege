<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeesType extends Model
{
    use SoftDeletes;

    protected $table = 'fees_type';

    protected $fillable = [
        'CreatedBy', 'ModifiedBy', 'SessionId', 'FeesCode', 'FeeName', 'Description',
        'FeeCycle', 'ApplicableMonth', 'IsOptional', 'IsRefundable', 'Isroyality', 'import_fee_type_id', 'IsActive', 'deleted_at',
    ];

    protected $casts = [
        'IsOptional' => 'boolean',
        'IsRefundable' => 'boolean',
        'Isroyality' => 'boolean',
    ];
}
