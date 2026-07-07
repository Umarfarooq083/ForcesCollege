<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsCredits extends Model
{
    protected $table = 'sms_credit';
    protected $fillable = [
        'tenant_id','smsCreditCount','schoolId','isActive',
        'createdBy','modifiedBy','sessionId','imported_sms_credit_id' 
    ];
    
    protected $casts = [
        'created_at' => 'date:Y-m-d H:i A',
    ];

}
