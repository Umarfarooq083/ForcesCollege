<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GenerateFeeChallan extends Model
{
    use SoftDeletes;

    protected $table = 'generate_fee_challan';

    protected $fillable = [
        'tenant_id',
        'challan_no',
        'SchoolId',
        'IsActive',
        'CreatedBy',
        'ModifiedBy',
        'SessionId',
        'StudentId',
        'ChallanMonth',
        'DueDate',
        'ClassId',
        'SectionId',
        'ExpiryDate',
        'Status',
        'SubmitDate',
        'PaymentMode',
        'FineAmount',
        'WaivedFineAmount',
        'Note',
        'CollectDate',
        'CollectBy',
        'IsPartialPayment',
    ];

    public function student()
    {
        return $this->hasOne(Student::class, 'id', 'StudentId')->Select('id', 'ClassId', 'SectionId', 'tenant_id', 'FirstName', 'LastName');
    }

    public function transection()
    {
        return $this->hasMany(ChallanTransactions::class, 'generate_challan_id', 'id');
    }

    protected $casts = [
        'ChallanMonth' => 'date:M-Y',
        'SubmitDate' => 'date:M-d-Y',
        'DueDate' => 'date:M-Y-d',
        'ExpiryDate' => 'date:M-d-Y',
    ];

    public function ChallanTransactions()
    {
        return $this->hasMany(ChallanTransactions::class, 'generate_challan_id', 'id');
    }

    public function StudentRel()
    {
        return $this->hasOne(Student::class, 'id', 'StudentId')->select('id', 'FirstName', 'LastName', 'RollNumber', 'ClassId', 'SectionId', 'FatherName');
    }

    public function partialPayments()
    {
        return $this->hasMany(ChallanPartialPayment::class, 'GenerateClassChallanId', 'id');
    }

    public function ChallanArrearsRel()
    {
        return $this->hasMany(ArrearChallanDetails::class, 'GenerateFeeChallanId', 'id');
    }

    public function challanArrearsSum()
    {
        return $this->hasMany(ArrearChallanDetails::class, 'GenerateFeeChallanId')
            ->select('id', 'GenerateFeeChallanId', 'FKeyId')
            ->where('transactionType', 'Arrears')
            ->with('challan_partial_payment')
            ->withSum('arrear_challan_transaction', 'BalanceFeeAfterDiscount');
    }

    public function SessionRel()
    {
        return $this->hasOne(LmsSession::class, 'id', 'SessionId');
    }

    public function ClassRel()
    {
        return $this->hasOne(Classes::class, 'id', 'ClassId');
    }

    public function SectionRel()
    {
        return $this->hasOne(Section::class, 'id', 'SectionId');
    }

    public function challanArrearsSumMobile()
    {
        return $this->hasMany(ArrearChallanDetails::class, 'GenerateFeeChallanId')
            ->select('id', 'GenerateFeeChallanId', 'FKeyId')
            ->where('transactionType', 'Arrears')
            // ->with('challan_partial_payment')
            ->with('arrear_challan_fine')
            ->withSum('arrear_challan_transaction', 'BalanceFeeAfterDiscount');
    }
}
