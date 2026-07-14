<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentUpload extends Model
{
    use SoftDeletes;

    protected $table = 'content_upload';

    protected $fillable = [
        'tenant_id',
        'ContentTitle',
        'ContentType',
        'ClassId',
        'SectionId',
        'UploadDate',
        'Description',
        'ContentFilePath',
        'AvailableForAllCampuses',
        'AvailableForAllClasses',
        'UploadContentGroupId',
        'AllowedSchools',
        'IsActive',
        'CreatedBy',
        'ModifiedBy',
        'SchoolId',
        'SessionId',
        'DisableReasonName',
        'SuperAdmin',
        'Student',
        'DisableReasonGroupId',
        'subjectId',
        'termId',
        'monthId',
        'weekId',
        'imported_upload_content_id',
    ];

    public function Classes()
    {
        return $this->hasOne(Classes::class, 'id', 'ClassId')->select('id', 'ClassName');
    }

    public function Subjects()
    {
        return $this->hasOne(Subject::class, 'id', 'subjectId')->select('id', 'SubjectName');
    }

    public function ContentGroup()
    {
        return $this->hasOne(UploadContentGroup::class, 'id', 'UploadContentGroupId')->select('id', 'name');
    }

    public function ContentRegion()
    {
        return $this->hasMany(UploadContentRegion::class, 'upload_content_id', 'id');
    }
}
