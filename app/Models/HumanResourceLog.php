<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HumanResourceLog extends Model
{
    protected $fillable = ['user_id', 'tenant_id', 'action', 'user_name'];
}
