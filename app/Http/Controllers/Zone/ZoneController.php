<?php

namespace App\Http\Controllers\Zone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Zone\ZoneRequest;
use App\Services\ZoneService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;

class ZoneController extends Controller
{
    protected $zoneService;
    public function __construct(ZoneService $zoneService)
    {
        $this->zoneService = $zoneService;
    }

    public function index(): Response
    {
        $zones = $this->zoneService->index();
        return Inertia::render('Zone/List', [
            'zones' => $zones,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Zone/Create');
    }

    public function submit(ZoneRequest $request): RedirectResponse
    {
        $this->zoneService->submit($request);
        return $this->redirectSuccess('Zone created successfully!', 'zone.index');
    }

    public function toggleStatus(Request $request, int $id): RedirectResponse
    {
        try {
            $this->zoneService->toggleStatus($request, $id);
            return $this->redirectSuccess('Zone status updated successfully!', 'zone.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}
