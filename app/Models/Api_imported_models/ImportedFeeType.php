<?php

namespace App\Models\Api_imported_models;

use Illuminate\Database\Eloquent\Model;

class ImportedFeeType extends Model
{
    protected $table = 'imported_fee_type';
    protected $fillable = [
        'feesCode','feeName','description','feeCycle','applicableMonth','isOptional','isRefundable','createdBy','internal_fee_type_id',
        'campusFeesMastersExist','isActive','isDeleted','createdDate','modifiedBy','modifiedDate','sessionId','imported_fee_type_id'
    ];
}
