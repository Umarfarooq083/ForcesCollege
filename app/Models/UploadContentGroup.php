<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadContentGroup extends Model
{
    
    protected $table = 'upload_content_group';
    protected $fillable = ['tenant_id','name','Category','CategoryId','ClassId','IsActive','SessionId','CreatedBy','ModifiedBy','deleted_at','imported_content_group_id'];



}
