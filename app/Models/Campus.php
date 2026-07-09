<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $fillable = [
        'SchoolId', 'IsActive', 'IsDeleted', 'SessionId', 'zoneid',
        'OwnerName', 'SchoolName', 'Address', 'PhoneNo',
        'OfficePhone', 'MobileNo', 'Area', 'Rooms', 'City',
        'EmailAddress', 'TotalFaculty', 'Rental', 'ContractDuration',
        'Comments', 'Other', 'AgreementPath', 'SchoolType', 'URL',
        'Code', 'AccountNo', 'BranchCode', 'DomainName', 'Logo',
        'IsAvailableForMobApp', 'SortOrder','CreatedBy','CreatedDate','ModifiedBy','ModifiedDate','tenant_id','bankName','AccountTitle','regionid'
    ];
    protected $casts = [
     'IsActive' => 'boolean',
     'IsDeleted' => 'boolean',
     'IsAvailableForMobApp' => 'boolean',
    ];

    public function scopeById($query, $id)
    {
        return $query->where('id', $id);
    }

    public function scopeTenant($query)
    {
        return $query->where('tenant_id', tenant('id'));
    }

    public function zone()
    {
        return $this->belongsTo(\App\Models\Zone::class, 'zoneid');
    }

    public function sessionYear()
    {
        return $this->hasOne(LmsSession::class, 'zoneid','zoneid');
    }

    public function region()
    {
        return $this->belongsTo(\App\Models\Region::class, 'regionid');
    }

}
