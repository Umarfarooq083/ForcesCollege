<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Roles extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'is_super', 'tenant_id', 'modifyBy', 'createdBy', 'deletedBy', 'imported_role_id'];

    public function getIsSuperAttribute($value)
    {
        return (bool) $value;
    }

    public function tenantDomain()
    {
        return $this->hasOne(Tenant::class, 'id', 'tenant_id');
    }

    public function scopeTenant($query, $tenantId)
    {
        return $query->where('tenant_id', null)->orWhere('tenant_id', $tenantId);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permissions::class, 'role_permissions', 'role_id', 'permission_id');
    }
}
