<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = ['name', 'type', 'duration', 'tenant_id', 'CreatedBy', 'ModifiedBy', 'IsActive'];

    protected $casts = [
        'IsActive' => 'boolean',
    ];

    public function programLevels()
    {
        return $this->hasMany(ProgramLevel::class, 'programm_id', 'id');
    }
}
