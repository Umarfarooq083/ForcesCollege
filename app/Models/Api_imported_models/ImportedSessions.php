<?php

namespace App\Models\Api_imported_models;

use Illuminate\Database\Eloquent\Model;

class ImportedSessions extends Model
{
    protected $fillable = [
        'sessionName',
        'sessionStartDate',
        'sessionEndDate',
        'imported_session_id',
        'lms_session_id'
    ];
}
