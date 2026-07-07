<?php

namespace App\Services;
use App\Models\AssignClassTeacher;
use App\Models\Classes;
use App\Models\Designation;
use App\Models\HumanResourceLog;
use App\Models\Section;
use App\Models\Staff;

class ClassteacherAssignService
{
    public function create(): array
    {
        $disegnationFilter = ['Teaching Assistant','Primary Teacher','Senior Teacher','Teacher','Pre School Teacher'];
        $DesignationList = Designation::whereIn('DesignationName',$disegnationFilter)->pluck('id')->toArray();
        
        if($DesignationList){
            $data['StaffList'] = Staff::where('tenant_id',tenant('id'))->where('IsActive',1)->whereIn('DesignationId',$DesignationList)->get(['id','tenant_id','FirstName','LastName']);    
        }else{
            $data['StaffList'] = [];
        }
        $classType_ids = campusClassList();
        $classList = Classes::select('id', 'tenant_id', 'ClassName')->whereIn('class_type_id', $classType_ids)->get();
        $class_ids = collect($classList);
        $data['classList'] = $classList;
        $classIdsSection = $class_ids->pluck('id')->toArray();
        $data['sectionList'] = Section::select('id', 'tenant_id', 'SectionName', 'ClassId')->where('tenant_id',tenant('id'))->whereIn('ClassId', $classIdsSection)->get();
        return $data;
    }

    public function submit($request): void
    {
        $validated = $request->validated();
        $created = AssignClassTeacher::create([
            ...$validated,
            'CreatedBy' => auth()->user()->id,
            'tenant_id' => tenant('id'),
        ]);

        if($created){
            userActivityLogs('Assinging Class Creation and By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }

    public function update($request): void
    {
        $validated = $request->validated();
        $assignClassTeacher = AssignClassTeacher::findOrFail($request->id);
        $updated = $assignClassTeacher->update([
            ...$validated,
            'ModifiedBy' => auth()->user()->id,
        ]);

        if($updated){
            userActivityLogs('Assinging Class Updation and id is '.$request->id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }
}
