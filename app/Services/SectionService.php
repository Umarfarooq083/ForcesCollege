<?php

namespace App\Services;

use App\Models\AcademicLog;
use App\Models\Section;

class SectionService
{
    public function index($request)
    {
        $sectionService = Section::query();
        $domain = getTenantSubDomain();
        $sectionService->where('tenant_id', tenant('id'))->with('SectionType', 'class', 'user')->orderBy('id', 'desc');
        if ($request->filled('search')) {
            $sectionService->where(function ($q) use ($request) {
                $q->where('SectionName', 'like', '%'.$request->search.'%')
                    ->orWhereHas('class', function ($sub) use ($request) {
                        $sub->where('ClassName', 'like', "%{$request->search}%");
                    })
                    ->orWhereHas('user', function ($sub) use ($request) {
                        $sub->where('name', 'like', "%{$request->search}%");
                    });
            });
        }

        return $sectionService->paginate(25)->withQueryString();
    }

    public function submit($request): void
    {
        $selectedCampusId = session('selected_campus_id');
        $validated = $request->validated();
        $tenantId = $selectedCampusId ? session('switched_tenant_id') : getDomainTenantId();

        $created = Section::create([
            ...$validated,
            'tenant_id' => $tenantId,
            'CreatedBy' => auth()->user()->id,
        ]);

        if ($created) {
            userActivityLogs('Section Created and By User ID: '.auth()->user()->id.'', AcademicLog::class);
        }
    }

    public function update($request): void
    {
        $updated = Section::find($request['id'])->update(['SectionName' => $request['SectionName']]);
        if ($updated) {
            userActivityLogs('Section Updated and id is '.$request['id'].' By User ID: '.auth()->user()->id.'', AcademicLog::class);
        }
    }
}
