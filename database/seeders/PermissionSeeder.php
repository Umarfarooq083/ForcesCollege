<?php

namespace Database\Seeders;

use App\Models\Permissions;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //    $permissions = collect([
    //         ['id' => 1, 'abilities' => 'tenant', 'module_name' => 'Campus ', 'name' => 'View Campus', 'route_names' => ['campus.index'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 2, 'abilities' => 'tenant', 'module_name' => 'Campus ', 'name' => 'Add Campus', 'route_names' => ['campus.index','campus.create','campus.submit'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 3, 'abilities' => 'tenant', 'module_name' => 'Campus ', 'name' => 'Edit Campus', 'route_names' => ['campus.index','campus.edit','campus.update','update.structure'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 4, 'abilities' => 'tenant', 'module_name' => 'Campus ', 'name' => 'Delete Campus', 'route_names' => ['campus.index','campus.delete'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 5, 'abilities' => 'tenant', 'module_name' => 'Roles ', 'name' => 'View Role', 'route_names' => ['role.index'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 6, 'abilities' => 'tenant', 'module_name' => 'Roles ', 'name' => 'Add Role', 'route_names' => ['role.index','role.create','role.submit'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 7, 'abilities' => 'tenant', 'module_name' => 'Roles ', 'name' => 'Edit Role', 'route_names' => ['role.index','role.edit','role.update'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 8, 'abilities' => 'tenant', 'module_name' => 'Roles ', 'name' => 'Delete Role', 'route_names' => ['role.index','role.delete'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 9, 'abilities' => 'tenant', 'module_name' => 'Roles ', 'name' => 'Assing Permissions', 'route_names' => ['role.index','role.permission.assign','role.permission.submit'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 10, 'abilities' => 'tenant', 'module_name' => 'Campus Users ', 'name' => 'View User', 'route_names' => ['user.index'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 11, 'abilities' => 'tenant', 'module_name' => 'Campus Users ', 'name' => 'Add User', 'route_names' => ['user.index','user.create','user.submit'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 12, 'abilities' => 'tenant', 'module_name' => 'Campus Users ', 'name' => 'Edit User', 'route_names' => ['user.index','user.edit','user.update'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 13, 'abilities' => 'tenant', 'module_name' => 'Campus Users ', 'name' => 'Delete User', 'route_names' => ['user.index','user.delete'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 14, 'abilities' => 'tenant', 'module_name' => 'Zones', 'name' => 'View Zone', 'route_names' => ['zone.index'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 15, 'abilities' => 'tenant', 'module_name' => 'Zones', 'name' => 'Add Zone', 'route_names' => ['zone.index','zone.create','zone.submit'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 16, 'abilities' => 'tenant', 'module_name' => 'Zones', 'name' => 'Update Zone Status', 'route_names' => ['zone.index','zone.toggleStatus'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 17, 'abilities' => 'tenant', 'module_name' => 'Student Inquiry', 'name' => 'View Inquiry', 'route_names' => ['inquiry.index'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 18, 'abilities' => 'tenant', 'module_name' => 'Student Inquiry', 'name' => 'Add Inquiry', 'route_names' => ['inquiry.index','inquiry.create','inquiry.submit'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 19, 'abilities' => 'tenant', 'module_name' => 'Student Inquiry', 'name' => 'Update Inquiry Status', 'route_names' => ['inquiry.index','inquiry.status'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 20, 'abilities' => 'tenant', 'module_name' => 'Classes', 'name' => 'View Class', 'route_names' => ['class.index'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 21, 'abilities' => 'tenant', 'module_name' => 'Classes', 'name' => 'Add Class', 'route_names' => ['class.index','class.create','class.submit'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    //         ['id' => 22, 'abilities' => 'tenant', 'module_name' => 'Classes', 'name' => 'Change Class Status', 'route_names' => ['class.index','class.status'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

    //     ]);

    //     $permissions->each(function ($val, $key) {
    //         $permission = Permissions::find($val['id']);
    //         if (is_null($permission)) {
    //             $permission = new Permissions();
    //             $permission->id = $val['id'];
    //             $permission->abilities = $val['abilities'];
    //             $permission->module_name = $val['module_name'];
    //             $permission->name = $val['name'];
    //             $permission->route_names = $val['route_names'];
    //             $permission->created_at = $val['created_at'];
    //             $permission->updated_at = $val['updated_at'];
    //             $permission->save();
    //         } else {
    //             $permission->abilities = $val['abilities'];
    //             $permission->module_name = $val['module_name'];
    //             $permission->name = $val['name'];
    //             $permission->route_names = $val['route_names'];
    //             $permission->save();
    //         }
    //     });
    // }


    public function run(): void
    {
        // Clear the permissions table
        Permissions::truncate();

        $routes = $this->getTenantRoutes();

        $modules = [];

        foreach ($routes as $route) {
            $parts = explode('.', $route['name']);
            $module = ucfirst($parts[0] ?? 'General');
            $action = ucfirst($parts[1] ?? 'Access');
            $permissionName = $action . ' ' . $module;
            $key = $module . '_' . $action;

            if (!isset($modules[$key])) {
                $modules[$key] = [
                    'abilities' => 'tenant',
                    'module_name' => $module,
                    'name' => $permissionName,
                    'route_names' => [$route['name']],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                $modules[$key]['route_names'][] = $route['name'];
            }
        }

        // Insert all permissions
        foreach ($modules as $item) {
            Permissions::create($item);
        }
    }
    protected function getTenantRoutes()
    {
        return collect(\Route::getRoutes())->filter(function ($route) {
            $name = $route->getName();

            // Skip routes with no name or without dot format (e.g. zone.index)
            if (!$name || !str_contains($name, '.')) {
                return false;
            }

            // Exclude specific route groups like sanctum.*, tenancy.*, etc.
            $excludedPrefixes = [
                'sanctum.',
                'tenancy.',
                'ignition.',
                'csrf',
                'livewire.',
                'stancl.tenancy.asset',
                'get.campus.data',
                'password.request',
                'password.email',
                'password.reset',
                'password.store',
                'verification.notice',
                'verification.verify',
                'verification.send',
                'password.confirm',
                'password.update',
                'storage.local',
                'create.fetch.student'
            ];

            foreach ($excludedPrefixes as $prefix) {
                if (str_starts_with($name, $prefix)) {
                    return false;
                }
            }

            return true;
        })->map(function ($route) {
            return [
                'name' => $route->getName(),
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'action' => $route->getActionName(),
            ];
        })->values();
    }
}
