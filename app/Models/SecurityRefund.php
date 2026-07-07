<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SecurityRefund extends Model
{
    use SoftDeletes;

    protected $table = 'security_refunds';

    protected $fillable = [
        'tenant_id',
        'staff_id',
        'apply_date',
        'applicable_month',
        'amount',
        'CreatedBy',
        'ModifiedBy',
    ];

    protected $casts = [
        'apply_date' => 'date:Y-m-d',
        'amount' => 'decimal:2',
    ];

    public function scopeTenant($query)
    {
        return $query->where('tenant_id', tenant('id'));
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'id');
    }
}