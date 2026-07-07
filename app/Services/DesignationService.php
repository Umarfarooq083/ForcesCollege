<?php

namespace App\Services;
use App\Models\Designation;
use App\Models\HumanResourceLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DesignationService
{
    public function index()
    {
        $designation = Designation::query();
        return $designationService =  $designation->orderBy('id', 'desc')->paginate(25)->withQueryString();
    }


    public function submit($request): void
    {
        $validated = $request->validated();
        $validated['CreatedBy'] = Auth::id();

        $created = Designation::create([
            ...$validated,
        ]);

        if($created){
            userActivityLogs('Designation Created and By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }

    }


    public function update($request): void
    {
        $designation = Designation::findOrFail($request->id);
        $designation->DesignationName = $request->DesignationName;
        $designation->ModifiedBy = Auth::id();

        if($designation->save()){
            userActivityLogs('Designation Updated and id is '.$request->id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }



    public function destroy($id): void
    {
        $designation = Designation::findOrFail($id);
        $designation->ModifiedBy = Auth::id();
        $designation->save();

        if($designation->delete()){
            userActivityLogs('Designation Deleted and id is '.$id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }


    public function toggleStatus($request, $id)
    {
        $designation = Designation::findOrFail($id);
        $designation->IsActive = $request->status;
        $designation->ModifiedBy = Auth::id();

        if($designation->save()){
            userActivityLogs('Designation Status Updated and id is '.$id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }

}