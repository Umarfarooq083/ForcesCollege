<?php

namespace App\Http\Controllers\LmsSessions;

use App\Http\Controllers\Controller;
use App\Http\Requests\LmsSessions\LmsSessionsRequest;
use App\Services\LmsSessionsService;
use App\Services\ZoneService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LmsSessionsController extends Controller
{
    protected $lmsSessionsService;

    protected $zoneService;

    public function __construct(LmsSessionsService $lmsSessionsService, ZoneService $zoneService)
    {
        $this->lmsSessionsService = $lmsSessionsService;
        $this->zoneService = $zoneService;
    }

    public function index(Request $request): Response
    {
        $lmssessions = $this->lmsSessionsService->index($request);

        return Inertia::render('LmsSessions/List', [
            'lmssessions' => $lmssessions,
        ]);
    }

    public function create(): Response
    {
        $zones = $this->zoneService->getActiveZones();

        return Inertia::render('LmsSessions/Create', [
            'zones' => $zones,
        ]);
    }

    public function submit(LmsSessionsRequest $request): RedirectResponse
    {
        $this->lmsSessionsService->submit($request);

        return $this->redirectSuccess('Session created successfully!', 'lmssessions.index');
    }

    public function edit(int $id): Response
    {
        $session = $this->lmsSessionsService->find($id);
        $zones = $this->zoneService->getActiveZones();

        return Inertia::render('LmsSessions/Edit', [
            'session' => $session,
            'zones' => $zones,
        ]);
    }

    public function update(LmsSessionsRequest $request, int $id): RedirectResponse
    {
        $this->lmsSessionsService->update($request, $id);

        return $this->redirectSuccess('Session updated successfully!', 'lmssessions.index');
    }

    public function destroy($id): RedirectResponse
    {
        $this->lmsSessionsService->destroy($id);

        return $this->redirectSuccess('Session deleted successfully!', 'lmssessions.index');
    }

    public function toggleStatus(Request $request, int $id): RedirectResponse
    {
        $this->lmsSessionsService->toggleStatus($request, $id);

        return $this->redirectSuccess('Session status updated successfully!', 'lmssessions.index');
    }
}
