<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $table = 'fcm_token';
    protected $fillable = [ 'user_id','fcm_token' ];
}
