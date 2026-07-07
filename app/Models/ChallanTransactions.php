<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallanTransactions extends Model
{
   use SoftDeletes;
   protected $fillable = [
      'tenant_id',
      'generate_challan_id',
      'SchoolId',
      'IsActive',
      'CreatedBy',
      'ModifiedBy',
      'SessionId',
      'FKey',
      'Title',
      'FeeAmount',
      'TransactionType',
      'DiscountAmount',
      'BalanceFeeAfterDiscount',
      'TotalFee',
      'imported_challan_transcation_id',
      'challan_type'
   ];

   public function generateChallan()
   {
      return $this->belongsTo(GenerateFeeChallan::class, 'generate_challan_id');
   }
}
