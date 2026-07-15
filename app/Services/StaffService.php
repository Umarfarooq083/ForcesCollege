<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Designation;
use App\Models\HumanResourceLog;
use App\Models\Roles;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class StaffService
{
    public function index(): object
    {
        $staffList = Staff::query();

        return $staffList = $staffList
            ->select(
                'id',
                'DisableReasonId',
                'StaffCode',
                'tenant_id',
                'RolesId',
                'DesignationId',
                'DepartmentId',
                'FirstName',
                'LastName',
                'Gender',
                'DateOfJoining',
                'PhotoPath',
                'IsActive'
            )
            ->Tenant()
            ->orderby('id', 'desc')
            ->with('StaffRole', 'DesignationRel', 'DepartmentRel', 'disabledReason')
                 // ->get();
            ->paginate(25)->withQueryString();
    }

    public function create(): array
    {
        $rolesList = Roles::select('id', 'tenant_id', 'name', 'status')
            ->where('status', 'active')
            ->get();
        $rolesListOnNull = [];
        $rolesListOnTenant = [];
        if (getTenantSubDomain() !== 'headoffice') {
            $rolesListOnNull = $rolesList->where('tenant_id', null)->toArray();
            $rolesListOnTenant = $rolesList->where('tenant_id', tenant('id'))->toArray();
            $rolesList = array_merge($rolesListOnNull, $rolesListOnTenant);
        }
        $desiginationList = Designation::select('id', 'DesignationName')
            ->get();
        $departmentList = Department::select('id', 'DepartmentName')
            ->get();
        $data['rolesList'] = $rolesList;
        $data['desiginationList'] = $desiginationList;
        $data['departmentList'] = $departmentList;

        return $data;
    }

    public function submit($request): void
    {
        $validated = $request->validated();
        $filePath = null;
        if ($request->hasFile('PhotoPath')) {
            $file = $request->file('PhotoPath');
            $ext = $file->extension();

            $folder = 'staff/profile/'.now()->year.'/'.now()->format('F');
            $fileName = uniqid().'_'.time().'.'.$ext;
            $filePath = $file->storeAs($folder, $fileName, 'public');
        }

        $created = $staffData = Staff::create([
            ...$validated,
            'tenant_id' => tenant('id'),
            'CreatedBy' => auth()->user()->id,
            'PhotoPath' => $filePath,
        ]);

        if ($created) {
            userActivityLogs('Staff Created and By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }

        if ($request->CreateUser == 1) {
            $createUser = new User;
            $createUser->name = $request->FirstName;
            $createUser->email = $request->Email;
            $createUser->tenant_id = tenant('id');
            $createUser->phone_no = $request->Phone;
            $createUser->password = 'Admin@123!@#$';
            $createUser->staff_id = $staffData->id;
            $createUser->save();
        }
    }

    public function update($request): void
    {
        $staff = Staff::findOrFail($request->id);
        $validated = $request->validated();

        if ($request->hasFile('PhotoPath')) {
            if ($staff->PhotoPath && Storage::disk('public')->exists($staff->PhotoPath)) {
                Storage::disk('public')->delete($staff->PhotoPath);
            }
            $file = $request->file('PhotoPath');
            $ext = $file->extension();
            $folder = 'staff/profile/'.now()->year.'/'.now()->format('F');
            $fileName = uniqid().'_'.time().'.'.$ext;
            $filePath = $file->storeAs($folder, $fileName, 'public');

            $updated = $staff->update([
                ...$validated,
                'PhotoPath' => $filePath,
                'ModifiedBy' => auth()->user()->id,
            ]);

            if ($updated) {
                userActivityLogs('Staff Updated and id is '.$request->id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
            }

        } else {
            $updated = $staff->update([
                ...$validated,
                'ModifiedBy' => auth()->user()->id,
            ]);

            if ($updated) {
                userActivityLogs('Staff Updated and id is '.$request->id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
            }
        }
    }

    public function toggleStatus($request, $id): void
    {
        $staff = Staff::where('tenant_id', tenant('id'))->findOrFail($id);
        $staff->IsActive = ! $staff->IsActive;

        if (isset($request->Status) && $request->Status === 'disabled') {
            $staff->DisableReasonId = $request->ReasonId;
        } else {
            $staff->DisableReasonId = null;
        }

        $reasonText = '';
        if (isset($request->Status) && $request->Status === 'disabled') {
            $reasonText = $request->Reason['name'];
        } else {
            $reasonText = $request->Reason;
        }

        \Illuminate\Support\Facades\DB::table('staff_disable_log')->insert([
            'staff_id' => $staff->id,
            'tenant_id' => $staff->tenant_id,
            'CreatedBy' => auth()->id(),
            'FromDate' => $request->FromDate,
            'Reason' => $reasonText,
            'Type' => $staff->IsActive ? 'Enable' : 'Disable',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($staff->save()) {
            userActivityLogs('Staff Status Updated and id is '.$id.' to '.$request->Reason['name'].' and By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }
}
