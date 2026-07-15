<?php

namespace App\Services;

use App\Models\Campus;
use App\Models\Roles;
use App\Models\SettingLog;
use App\Models\Tenant;

class RoleServices
{
    public function index($request)
    {
        $roles = Roles::query();
        // $roles->where('name', '!=', 'SuperAdmin');
        if ($request->filled('search')) {
            $roles->where('name', 'like', '%'.$request->search.'%');
        }
        $roles->orderBy('id', 'desc');
        $domain = getTenantSubDomain();
        if ($domain === 'headoffice') {
            $IsSwitch = session('switched_tenant_id');
            if ($IsSwitch == auth()->user()->tenant_id) {
                return $roles->with('tenantDomain')->latest('id')->paginate(25)->withQueryString();
            } else {
                return $roles->Tenant(tenant('id'))->with('tenantDomain')->latest('id')->paginate(25)->withQueryString();
            }
        } else {
            return $roles->Tenant(tenant('id'))->where('is_super', 0)->with('tenantDomain')->latest('id')->paginate(25)->withQueryString();
        }
    }

    public function submit($validated, $request): void
    {
        $selectedDomain = $this->findDomain($request);
        $created = Roles::create([
            ...$validated,
            'tenant_id' => $selectedDomain,
            'createdBy' => auth()->user()->id,
        ]);

        if ($created) {
            userActivityLogs('Role Created , created role id is: '.$created->id, SettingLog::class);
        }
    }

    public function update($validated, $request): void
    {
        $selectedDomain = $this->findDomain($request);
        $role = Roles::findOrFail($request->id);
        $updated = $role->update([
            ...$validated,
            'tenant_id' => $selectedDomain,
            'modifyBy' => auth()->user()->id,
        ]);

        if ($updated) {
            userActivityLogs('Role Updated and  id is '.$request->id.' By User ID: '.auth()->user()->id.'', SettingLog::class);
        }
    }

    private function findDomain($request)
    {
        $selectedDomain = null;
        if ($request->campus_id != null) {
            $campusData = Campus::select('id', 'DomainName')->byId($request->campus_id)->first();
            $Domain = Tenant::where('domain', $campusData->DomainName)->first();
            $selectedDomain = $Domain->id;
        }

        return $selectedDomain;
    }
}
