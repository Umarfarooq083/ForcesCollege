<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SecurityDeduction extends Model
{
    use SoftDeletes;

    protected $table = 'security_deductions';

    protected $fillable = [
        'tenant_id',
        'staff_id',
        'apply_year',
        'from_month',
        'to_month',
        'amount',
        'CreatedBy',
        'ModifiedBy',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'id');
    }

    public function scopeTenant($query)
    {
        return $query->where('tenant_id', tenant('id'));
    }
}