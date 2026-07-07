<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $fillable = ['token','device_key','expires_at','used'];
    protected $casts = ['expires_at' => 'datetime','used' => 'boolean'];
}
