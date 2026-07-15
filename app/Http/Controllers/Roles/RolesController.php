<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\RolesRequest;
use App\Models\Permissions;
use App\Models\RolePermission;
use App\Models\Roles;
use App\Models\SettingLog;
use App\Services\RoleServices;
use App\Traits\CampusListTrait;
use App\Traits\PermissionTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RolesController extends Controller
{
    use CampusListTrait,PermissionTrait;

    protected $roleServices;

    public function __construct(RoleServices $roleServices)
    {
        $this->roleServices = $roleServices;
    }

    public function index(Request $request): Response
    {
        $roles = $this->roleServices->index($request);

        return Inertia::render('Roles/List', compact('roles'));
    }

    public function create(): Response
    {
        return Inertia::render('Roles/Create', [
            'campusList' => $this->getCampusList(),
        ]);
    }

    public function submit(RolesRequest $request): RedirectResponse
    {
        $this->roleServices->submit($request->validated(), $request);

        return $this->redirectSuccess('Role created successfully!', 'role.index');
    }

    public function edit(RolesRequest $request): Response
    {
        return Inertia::render('Roles/Edit', [
            'role' => $this->findRole($request->id),
            'campusList' => $this->getCampusList(),
        ]);
    }

    public function update(RolesRequest $request): RedirectResponse
    {
        $this->roleServices->update($request->validated(), $request);

        return $this->redirectSuccess('Role updated successfully!', 'role.index');
    }

    public function delete(RolesRequest $request): RedirectResponse
    {
        $role = Roles::query();
        $role->where('id', $request->id)->update([
            'deletedBy' => auth()->user()->id,
        ]);
        $this->findRole($request->id)->delete();
        userActivityLogs('Role Deleted and id is '.$request->id.' User ID: '.auth()->user()->id.'', SettingLog::class);

        return $this->redirectSuccess('Role deleted successfully!', 'role.index');
    }

    public function permissionAssign(Request $request): Response
    {
        $permissions = Permissions::toBase()->get();
        $assignedPermissions = RolePermission::toBase()->where('role_id', $request->id)->get()->pluck('permission_id');
        $tree_permission = $this->permissionTreeView($permissions);

        return Inertia::render('Roles/AssignPermission', [
            'role_id' => $request->id,
            'tree_permission' => $tree_permission,
            'assignedPermissions' => $assignedPermissions,
        ]);
    }

    public function permissionAssignSubmit(Request $request): RedirectResponse
    {
        RolePermission::where('role_id', $request->role_id)->delete();
        $insertArray = [];
        foreach ($request->permission as $key => $list) {
            $insertArray[$key]['role_id'] = $request->role_id;
            $insertArray[$key]['permission_id'] = $list;
            $insertArray[$key]['created_at'] = now();
            $insertArray[$key]['updated_at'] = now();
        }
        RolePermission::insert($insertArray);
        removeUserPermission($request->role_id);
        userActivityLogs('Permission Assigned To Role and User ID: '.auth()->user()->id.'', SettingLog::class);

        return $this->redirectSuccess('Permission assigned successfully!', 'role.index');
    }

    private function findRole(int $id)
    {
        return Roles::findOrFail($id);
    }
}
