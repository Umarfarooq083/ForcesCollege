<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use SoftDeletes;

    protected $table = 'leave_requests';

    protected $fillable = [
        'tenant_id',
        'staff_id',
        'date',
        'leave_type',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'approval_note',
        'CreatedBy',
        'ModifiedBy',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'approved_at' => 'datetime',
    ];

    public function scopeTenant($query)
    {
        return $query->where('tenant_id', tenant('id'));
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
