<?php

namespace App\Services;

use App\Models\Department;
use App\Models\HumanResourceLog;

class DepartmentService
{
    public function submit($request): void
    {
        $validated = $request->validated();
        $department = Department::create([
            ...$validated,
            'CreatedBy' => auth()->user()->id,
        ]);

        if($department){
            userActivityLogs('Department Created and By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }

    public function update($request): void
    {
       $validated = $request->validated();
        $Department = Department::findOrFail($request->id);
        $dep_updated = $Department->update([
            ...$validated,
            'ModifiedBy' => auth()->user()->id,
        ]);

        if($dep_updated){
            userActivityLogs('Department Updated and id is '.$request->id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }
}
