<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class StudentDocuments extends Model
{
    protected $fillable = [ 'student_id','tenant_id','Title','document','status' ];

    protected $appends = ['document_url'];

    public function getDocumentUrlAttribute()
    {
        return $this->attributes['document']
            ? url('storage/' . $this->attributes['document'])
            : null;
    }

}
