<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffDisableReason extends Model
{
    protected $table = 'staff_disable_reasons';
    protected $fillable = [
        'id',
        'IsActive',
        'CreatedBy',
        'CreatedDate',
        'ModifiedBy',
        'ModifiedDate',
        'SessionId',
        'DisableReasonName',
        'DisableReasonGroupId',
    ];
}
