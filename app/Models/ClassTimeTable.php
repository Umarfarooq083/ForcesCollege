<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ClassTimeTable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'SchoolId',
        'IsActive',
        'CreatedBy',
        'ModifiedBy',
        'SessionId',
        'ClassId',
        'SectionId',
        'SubjectGroupId',
        'Day',
        'date',
        'ClassId',
        'SectionId',
        'SubjectId',
        'StaffId',
        'TimeFrom',
        'TimeTo',
        'RoomNo',
        'ClassTimeTableGroupId',
        'imported_class_time_table'
    ];
    

    protected $casts = [
        'IsActive' => 'boolean',
    ];


    public function getTimeFromFormattedAttribute(): ?string
    {
        return $this->TimeFrom
            ? Carbon::createFromFormat('H:i:s', $this->TimeFrom)->format('h:i A')
            : null;
    }

    
    public function getTimeToFormattedAttribute(): ?string
    {
        return $this->TimeTo
            ? Carbon::createFromFormat('H:i:s', $this->TimeTo)->format('h:i A')
            : null;
    }


    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'StaffId');
    }


    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'ClassId');
    }


    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'SectionId');
    }


    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'SubjectId');
    }
}
