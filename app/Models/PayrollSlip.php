<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollSlip extends Model
{
    use SoftDeletes;

    protected $table = 'payroll_slips';

    protected $fillable = [
        'tenant_id',
        'staff_id',
        'department_name',
        'desigination_name',
        'payroll_month',
        'payroll_year',
        'basic_salary',
        'transport_allowance',
        'computer_allowance',
        'mobile_allowance',
        'recreation_allowance',
        'gazetted_leaves_count',
        'working_days',
        'present_days',
        'absent_days',
        'leave_days',
        'total_absent_deduction',
        'gazetted_leave_deduction',
        'fine_deduction',
        'miscellaneous_payment',
        'salary_tax',
        'security_refund',
        'security_deduction',
        'net_salary',
        'gross_salary',
        'status',
        'late_fine_deduction',
        'CreatedBy',
        'ModifiedBy',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'computer_allowance' => 'decimal:2',
        'mobile_allowance' => 'decimal:2',
        'recreation_allowance' => 'decimal:2',
        'total_absent_deduction' => 'decimal:2',
        'gazetted_leave_deduction' => 'decimal:2',
        'fine_deduction' => 'decimal:2',
        'miscellaneous_payment' => 'decimal:2',
        'salary_tax' => 'decimal:2',
        'security_refund' => 'decimal:2',
        'security_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'gross_salary' => 'decimal:2',
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
