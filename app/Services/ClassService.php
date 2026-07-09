<?php

namespace App\Services;

use App\Models\AcademicLog;
use App\Models\Classes;

class ClassService
{
    public function index($request)
    {
        $domain = getTenantSubDomain();  
        $query =  Classes::with('user')->orderBy('ClassOrder','asc');


        if ($request->filled('search')) {
            $query->where(function($q) use($request){
                $q->where('ClassName', 'like', '%' . $request->search . '%')
                ->orWhereHas('user', function($sub) use($request) {
                    $sub->where('name', 'like', "%{$request->search}%");
                });
            });
        }

        // if ($request->filled('search')) {
        //     $query->where('ClassName', 'like', '%' . $request->search . '%');
        // }

        if($domain == 'headoffice'){
            return $query->paginate(25)->withQueryString();
        }else{
            return $query->where('tenant_id', tenant('id'))->paginate(25)->withQueryString();
        }
    }

    public function submit($request): void
    {
        $validated = $request->validated();
        $created = Classes::create([
            ...$validated,
            'CreatedBy' => auth()->user()->id,
        ]);

        if($created){
            userActivityLogs('Class Created and By User ID: '.auth()->user()->id.'', AcademicLog::class);
        }
    }


    public function update($request): void
    {
        $validated = $request->validated();
        $created = Classes::where('id',$request->id)->update([
            ...$validated,
            'ModifiedBy' => auth()->user()->id,
        ]);

        if($created){
            userActivityLogs('Class Created and id is '.$request->id.' By User ID: '.auth()->user()->id.'', AcademicLog::class);
        }
    }
   
    public function delete($request): void
    {
        $created = Classes::where('id',$request->id)->delete();
        if($created){
            userActivityLogs('Class Deleted and id is '.$request->id.' By User ID: '.auth()->user()->id.'', AcademicLog::class);
        }
    }

    public function statusUpdate($request, $id):void
    {
        $class = Classes::findOrFail($id);
        $class->IsActive = $request->status;
        $class->ModifiedBy = auth()->user()->id;

        if($class->save()){
            userActivityLogs('Class Status Updated and id is '.$id.' By User ID: '.auth()->user()->id.'', AcademicLog::class);
        }
    }
}
