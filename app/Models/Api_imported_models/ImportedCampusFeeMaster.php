<?php

namespace App\Models\Api_imported_models;

use Illuminate\Database\Eloquent\Model;

class ImportedCampusFeeMaster extends Model
{
    protected $table = 'imported_campus_fee_master';
    protected $fillable = [
        'title','feesTypeNId','amount','classId','class','sectionId','isActive','isDeleted','createdBy',
        'createdDate','modifiedBy','modifiedDate','sessionId','newFeesTypeNId','imported_fee_master_id',
        'internal_fee_master_id'
    ];
}
