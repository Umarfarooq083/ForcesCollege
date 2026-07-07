<?php

namespace App\Models;

use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    use HasDomains;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'domain', 'data'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function CampusRel()
    {
        return $this->hasOne(Campus::class, 'tenant_id', 'id')->select('id', 'tenant_id');
    }

    /**
     * Alias used by DownloadContentLog eager-loading: maps campus by tenant_id.
     */
    public function campus()
    {
        return $this->hasOne(Campus::class, 'tenant_id', 'id')
            ->select('id', 'SchoolName', 'DomainName', 'tenant_id');
    }
}
