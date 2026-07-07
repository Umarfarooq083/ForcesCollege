<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_no',
        'address',
        'tenant_id',
        'updated_at',
        'createdBy',
        'modify_at',
        'deleted_at',
        'modifyBy',
        'imported_user_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_permissions' => 'array',
        ];
    }

    public function scopeTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'role_user', 'user_id', 'role_id');
    }

    public function hasRole($roleName)
    {
        return $this->roles->contains('name', $roleName);
    }


    public function getAllPermissions()
    {
        return Cache::remember("user_permissions_{$this->id}", 3600, function () {
            return $this->roles()
                ->with('permissions')
                ->get()
                ->pluck('permissions')
                ->flatten()
                ->pluck('route_names')
                ->flatten()
                ->unique()
                ->values()
                ->toArray();
        });
    }

}
