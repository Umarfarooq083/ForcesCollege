<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadCampusContent extends Model
{
    protected $table = 'upload_campus_content';

    protected $fillable = ['tenant_id', 'campus_id', 'upload_content_id'];
}
