<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class LmsSession extends Model
{
    use SoftDeletes;

    protected $table = 'lms_sessions';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'zoneid',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'status' => 'integer',
        'zoneid' => 'integer',
        'created_by' => 'integer',
        'modified_by' => 'integer',
    ];

    public function zone()
    {
        return $this->belongsTo(\App\Models\Zone::class, 'zoneid');
    }

    protected $appends = [
        'start_date_formatted',
        'end_date_formatted',
    ];


    public function getStartDateFormattedAttribute()
    {
        return $this->start_date?->format('Y-m-d');
    }

    public function getEndDateFormattedAttribute()
    {
        return $this->end_date?->format('Y-m-d');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
