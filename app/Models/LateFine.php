<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LateFine extends Model
{
    use SoftDeletes;

    protected $table = 'late_fines';

    protected $fillable = [
        'tenant_id',
        'staff_id',
        'apply_year',
        'applicable_month',
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
