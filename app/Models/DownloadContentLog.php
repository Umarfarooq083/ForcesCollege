<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadContentLog extends Model
{
    protected $fillable = [
        'tenant_id', 'user_id', 'domainName', 'upload_content_id', 'important_content_upload_log_id', 'created_at', 'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function uploadContent()
    {
        return $this->belongsTo(ContentUpload::class, 'upload_content_id');
    }

    public function campus()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
