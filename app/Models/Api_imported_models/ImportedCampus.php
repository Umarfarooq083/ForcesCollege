<?php

namespace App\Models\Api_imported_models;

use Illuminate\Database\Eloquent\Model;

class ImportedCampus extends Model
{
    protected $table = 'imported_campus';
    protected $fillable = [
        'SchoolId', 'IsActive', 'IsDeleted', 'SessionId', 'zoneid',
        'OwnerName', 'SchoolName', 'Address', 'PhoneNo',
        'OfficePhone', 'MobileNo', 'Area', 'Rooms', 'City',
        'EmailAddress', 'TotalFaculty', 'Rental', 'ContractDuration',
        'Comments', 'Other', 'AgreementPath', 'SchoolType', 'URL',
        'Code', 'AccountNo', 'BranchCode', 'DomainName', 'Logo',
        'IsAvailableForMobApp', 'SortOrder','CreatedBy','CreatedDate','ModifiedBy','ModifiedDate','tenant_id','session'
    ];

    protected $casts = [
     'IsActive' => 'boolean',
     'IsDeleted' => 'boolean',
     'IsAvailableForMobApp' => 'boolean',
    ];
}
