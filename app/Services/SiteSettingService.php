<?php

namespace App\Services;

use App\Models\FeeLog;
use App\Models\SiteSetting;

class SiteSettingService
{
    public function submit($validated): void
    {
        $this->createOrUpdateSetting($validated);
        userActivityLogs('Fine type Created and By User ID: '.auth()->user()->id.'', FeeLog::class);
    }

    public function edit($validated): SiteSetting
    {
        return SiteSetting::where('id', $validated['setting_id'])->first(['id', 'tenant_id', 'name', 'key', 'value']);
    }

    public function update($validated): void
    {
        $this->createOrUpdateSetting($validated);
        userActivityLogs('Fine type Updated and By User ID: '.auth()->user()->id.'', FeeLog::class);
    }

    protected function createOrUpdateSetting($validated): SiteSetting
    {
        $name = $validated['name'] ?? '';
        $key = $validated['key'] ?? '';
        $value = $validated['value'] ?? 0;

        return $data = createOrUpdateSetting($name, $key, $value);
    }
}
