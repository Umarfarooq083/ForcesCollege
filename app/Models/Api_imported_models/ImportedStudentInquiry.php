<?php

namespace App\Models\Api_imported_models;

use Illuminate\Database\Eloquent\Model;

class ImportedStudentInquiry extends Model
{
    protected $table = 'imported_lost_inquiry';
    protected $fillable = [
        	'internal_student_inquiry','name','studentName','phone','email','address','status','sendNotification',
            'description','note','date','nextFollowUpDate','assignedId','assigned','referenceId','reference',
            'sourceId','source','numberOfChild','classId','class','admissionEnquiryGroupId','admissionEnquiryGroup',
            'isSmsSent','students','schoolId','isActive','isDeleted','createdBy','createdDate','modifiedBy','modifiedDate',
            'sessionId','session'
        ];
}
