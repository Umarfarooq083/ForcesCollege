<?php

namespace App\Services;

use App\Models\AcademicLog;
use App\Models\Subject;

class SubjectService
{
    public function submit($request): void
    {
        $validated = $request->validated();
        $created = Subject::create([
            ...$validated,
            'tenant_id' => tenant('id'),
            'CreatedBy' => auth()->user()->id,
        ]);

        if($created){
            userActivityLogs('Subject Created and By User ID: '.auth()->user()->id.'', AcademicLog::class);
        }
    }

    public function update($request): void
    {
        $subjectUpdate = Subject::find($request->id);
        $subjectUpdate->SubjectName = $request->SubjectName;
        $subjectUpdate->SubjectType = $request->SubjectType;
        $subjectUpdate->SubjectCode = $request->SubjectCode;
        $subjectUpdate->ClassId = $request->ClassId;

        if($subjectUpdate->save()){
            userActivityLogs('Subject Updated and id is '.$request->id.' By User ID: '.auth()->user()->id.'', AcademicLog::class);
        }
   
    }
}
