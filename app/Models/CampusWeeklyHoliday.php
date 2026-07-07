<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampusWeeklyHoliday extends Model
{
    protected $fillable = [
        'campus_id',
        'weekend_day',
        'is_active',
        'CreatedBy',
        'ModifiedBy',
        'CreatedDate',
        'ModifiedDate',
        'tenant_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeTenant($query)
    {
        return $query->where('tenant_id', tenant('id'));
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }
}