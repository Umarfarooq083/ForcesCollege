<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campus\CampusRequest;
use App\Models\Campus;
use App\Models\CampusCategory;
use App\Models\Roles;
use App\Services\CampusService;
use App\Services\RegionService;
use App\Services\ZoneService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CampusController extends Controller
{
    protected $campusService;

    protected $zoneService;

    protected $regionService;

    public function __construct(CampusService $campusService, ZoneService $zoneService, RegionService $regionService)
    {
        $this->campusService = $campusService;
        $this->zoneService = $zoneService;
        $this->regionService = $regionService;
    }

    public function index(): Response
    {
        $campusList = $this->campusService->index();

        return Inertia::render('Campus/List', [
            'campusList' => $campusList,
        ]);
    }

    public function create(): Response
    {
        $roleExist = Roles::where('name', 'Campus Admin')->exists();
        $zones = $this->zoneService->getActiveZones();
        $regions = $this->regionService->getActiveRegions();
        $campusCategories = CampusCategory::where('IsActive', true)->get();

        return Inertia::render('Campus/Create', [
            'zones' => $zones,
            'regions' => $regions,
            'campus_categories' => $campusCategories,
            'role_exist' => $roleExist,
        ]);
    }

    public function submit(CampusRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $this->campusService->submit($validated, $request);

            return redirect()->route('campus.index')->with('toast', [
                'type' => 'success',
                'message' => 'Campus created successfully!',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function edit(Request $request): Response
    {
        $zones = $this->zoneService->getActiveZones();
        $regions = $this->regionService->getActiveRegions();
        $campusCategories = CampusCategory::where('IsActive', true)->get();
        $campus = Campus::findOrFail($request->id);

        return Inertia::render('Campus/Edit', [
            'campus' => $campus,
            'zones' => $zones,
            'regions' => $regions,
            'campus_categories' => $campusCategories,
        ]);
    }

    public function update(CampusRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $campus = Campus::findOrFail($request->id);
        $campus->update([
            ...$validated,
            'ModifiedBy' => auth()->user()->id,
            'ModifiedDate' => now(),
        ]);

        return redirect()->route('campus.index')->with('toast', [
            'type' => 'success',
            'message' => 'Campus updated successfully!',
        ]);
    }

    public function delete() {}
}
