<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SMSLog extends Model
{
    use SoftDeletes;
    protected $table = 'sms_log';
    protected $fillable = [
        'tenant_id','mobileNo','body','characterLength','smsCount','status',
        'apiCode','apItype','apiResponseText','apiTransactionID','isActive',
        'createdBy','modifiedBy','sessionId','imported_sms_log_id','deleted_at'
    ];
}
