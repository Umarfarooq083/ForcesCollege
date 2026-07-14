<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryTax extends Model
{
    use SoftDeletes;

    protected $table = 'salary_taxes';

    protected $fillable = [
        'tenant_id',
        'staff_id',
        'amount',
        'reason',
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
}
