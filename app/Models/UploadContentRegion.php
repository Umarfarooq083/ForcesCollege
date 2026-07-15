<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadContentRegion extends Model
{
    protected $table = 'upload_content_regions';

    protected $fillable = [
        'region_id',
        'upload_content_id',
        'status',
    ];

    public function RegiondRel()
    {
        return $this->hasOne(Region::class, 'id', 'region_id');
    }
}
