<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SiteSettingRequest;
use App\Models\SiteSetting;
use App\Services\SiteSettingService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SiteSettingController extends Controller
{
    protected $siteSettingService;

    public function __construct(SiteSettingService $siteSettingService)
    {
        $this->siteSettingService = $siteSettingService;
    }

    public function index(): Response
    {
        $data['settings'] = SiteSetting::get();

        return Inertia::render('SiteSetting/List', $data);
    }

    public function create(): Response
    {
        return Inertia::render('SiteSetting/Create');
    }

    public function submit(SiteSettingRequest $request): RedirectResponse
    {
        $this->siteSettingService->submit($request->validated());

        return $this->redirectSuccess('Site Setting saved successfully.', 'setting.index');
    }

    public function edit(SiteSettingRequest $request): Response
    {
        $data['setting'] = $this->siteSettingService->edit($request->validated());

        return Inertia::render('SiteSetting/Edit', $data);
    }

    public function update(SiteSettingRequest $request): RedirectResponse
    {
        $this->siteSettingService->update($request->validated());

        return $this->redirectSuccess('Site Setting updated successfully.', 'setting.index');
    }
}
