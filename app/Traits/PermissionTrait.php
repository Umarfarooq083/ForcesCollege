<?php

namespace App\Traits;

trait PermissionTrait
{
    public function permissionTreeView($permissions)
    {
        $modules = $permissions->pluck('module_name')->unique()->toArray();
        foreach ($modules as $k => $val) {
            $name = [];
            foreach ($permissions as $ke => $va) {
                if ($va->module_name == $val) {
                    array_push($name, ['id' => $va->id, 'name' => $va->name]);
                }
            }
            $modules[$k] = ['module_name' => $modules[$k], 'names' => $name];
        }

        return array_values($modules);
    }
}
