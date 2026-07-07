<?php

namespace App\Services;

use App\Models\Roles;
use App\Models\SettingLog;
use App\Models\StudentLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserCampusServices
{
    public function index($request) 
    {
        $users = User::query();
        if($request->filled('search'))
        {
            $users->where('name', 'like', '%'. $request->search .'%');
        }
        return $users = $users->Tenant(tenant('id'))->with('roles')->orderBy('id','desc')->paginate(25)->withQueryString();
    }

    public function getTenantRoles() 
    {
       $domain = getTenantSubDomain();
        $roles = Roles::query();
        if ($domain === 'headoffice') {
           return $roles->get();
        } else {
            return $roles->Tenant(tenant('id'))->get();
        }
    }

    public function submit($request): void
    { 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'tenant_id' => tenant('id'),
            'createdBy' => auth()->user()->id,
        ]);

        if($user){
            userActivityLogs('User Created and By User ID: '.auth()->user()->id.'', SettingLog::class);
        }

        $roleIds = collect($request->roles_ids)->pluck('id');
        $user->roles()->attach($roleIds);
        getRolesPermissions($roleIds,$user->id);
        
    }

    public function update($request): void
    {
        $userData = User::with('roles')->findOrFail($request->id);
        $userData->name = $request->name;
        $userData->phone_no = $request->phone_no;
        $userData->address = $request->address;
        $userData->modify_at = now();
        $userData->modifyBy = auth()->user()->id;
        if($userData->save()){
            userActivityLogs('User Updated and id is '.$request->id.' By User ID: '.auth()->user()->id.'', SettingLog::class);
        }
        $roleIds = collect($request->roles_ids)->pluck('id');
        $userData->roles()->sync($roleIds);
        getRolesPermissions($roleIds,$request->id);
    }

    public function delete($request)
    {
        if (auth()->user()->id == $request->id) {
            return false;
        }
        $userData = User::findOrFail($request->id);
        if($userData->delete()){
            userActivityLogs('User Deleted and id is '.$request->id.' By - '.auth()->user()->id , StudentLog::class);
            return true;
        }
    }
}
