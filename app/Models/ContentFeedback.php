<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentFeedback extends Model
{
    protected $table = 'content_feedback';

    protected $fillable = [
        'tenant_id',
        'title',
        'subject',
        'job_position',
        'summary',
        'content_title',
        'status',
    ];
}
