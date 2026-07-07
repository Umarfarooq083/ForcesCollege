<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallanPartialPayment extends Model
{
    use SoftDeletes;
    
    protected $table = 'challan_partial_payments';

    protected $fillable = [
        'SchoolId',
        'IsActive',
        'tenant_id',
        'CreatedBy',
        'ModifiedBy',
        'SessionId',
        'GenerateClassChallanId',
        'ReceivedAmount',
        'CollectDate',
        'CollectBy',
        'PaymentMode',
        'SubmitDate',
        'note',
        'imported_challan_partial_payment_id'
    ];

    protected $casts = [
        'IsActive' => 'boolean',
        'ReceivedAmount' => 'decimal:2',
    ];

    public function feeChallan()
    {
        return $this->belongsTo(GenerateFeeChallan::class, 'GenerateClassChallanId', 'id')->select('id', 'tenant_id', 'challan_no', 'StudentId', 'ChallanMonth');
    }

    
}
