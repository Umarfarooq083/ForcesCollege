<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionType extends Model
{
    protected $table = 'section_types';

    protected $fillable = ['name', 'status'];
}
