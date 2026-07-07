<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampusClassType extends Model
{
     protected $fillable = [
        'campus_id', 'class_type_id','created_at','updated_at'
    ];

    public function classType()
    {
        return $this->hasOne(ClassType::class, 'id','class_type_id');
    }

    public function classes()
    {
        return $this->hasOne(Classes::class, 'class_type_id','class_type_id');
    }
}
