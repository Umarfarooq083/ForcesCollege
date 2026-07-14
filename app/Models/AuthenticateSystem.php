<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthenticateSystem extends Model
{
    protected $table = 'authenticate_system';

    protected $fillable = [
        'tenant_id', 'tenant_domain_name', 'system_generated_key', 'user_id', 'deleted_at',
    ];
}
