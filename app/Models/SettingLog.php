<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingLog extends Model
{
    protected $fillable = ['user_id', 'tenant_id', 'action', 'user_name'];
}
