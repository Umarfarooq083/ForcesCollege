<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArrearChallanDetails extends Model
{
    use SoftDeletes;
    protected $table = 'arrears_challan_details';
    protected $fillable = [
        'tenant_id','SchoolId','IsActive','CreatedBy','ModifiedBy',
        'SessionId','GenerateFeeChallanId','FKeyId','TransactionType','imported_challan_arrear_id','deleted_at'
    ];

    public function arrear_challan_transaction()
    {
        return $this->hasOne(ChallanTransactions::class, 'generate_challan_id', 'FKeyId');
    }

    
    public function challan_partial_payment()
    {
        return $this->hasMany(ChallanPartialPayment::class, 'GenerateClassChallanId', 'FKeyId');
    }

    public function arrear_challan_fine()
    {
        return $this->hasOne(GenerateFeeChallan::class, 'id', 'FKeyId')->select('id','challan_no', 'FineAmount', 'Status','WaivedFineAmount','per_day_fine','DueDate','CollectDate','ExpiryDate','SubmitDate');
    }
}
