<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
        'modified_by',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function session()
    {
        return $this->belongsTo(LmsSession::class, 'id', 'zoneid')->where('status', 1);
    }
}
