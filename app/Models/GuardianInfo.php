<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuardianInfo extends Model
{
    protected $table = 'guardian_info';
    protected $fillable = [
        'tenant_id','cnic','name'
    ];

    public function scopeTenant($query)
    {
        return $query->where('tenant_id',tenant('id'));
    }

    public function studentInquiries()
    {
        return $this->hasMany(\App\Models\StudentInquiry::class, 'guardian_id', 'id');
    }
}
