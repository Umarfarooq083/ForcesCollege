<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisableReason extends Model
{
    use SoftDeletes;
    protected $table = 'disable_reasons';
    protected $fillable = [
        'SchoolId',
        'IsActive',
        'IsDeleted',
        'CreatedBy',
        'CreatedDate',
        'ModifiedBy',
        'ModifiedDate',
        'SessionId',
        'DisableReasonName',
        'DisableReasonGroupId'
    ];
}
