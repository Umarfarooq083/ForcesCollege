<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteSetting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'key',
        'value',
        'created_by',
        'modified_by',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }
}
