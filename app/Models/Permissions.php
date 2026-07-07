<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    protected $casts = [
        'route_names' => 'array',
    ];
}
