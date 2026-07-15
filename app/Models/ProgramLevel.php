<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramLevel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'programm_id',
        'title',
        'status',
        'CreatedBy',
        'ModifiedBy',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'programm_id');
    }
}
