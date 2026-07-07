<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\CampusWeeklyHolidayRequest;
use App\Services\CampusWeeklyHolidayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CampusWeeklyHolidayController extends Controller
{
    protected $campusWeeklyHolidayService;

    public function __construct(CampusWeeklyHolidayService $campusWeeklyHolidayService)
    {
        $this->campusWeeklyHolidayService = $campusWeeklyHolidayService;
    }

    public function index(): Response
    {
        $holidays = $this->campusWeeklyHolidayService->index();
        return Inertia::render('Staff/CampusWeeklyHoliday/List', [
            'holidays' => $holidays,
        ]);
    }

    public function create(): Response
    {
        $campusList = $this->campusWeeklyHolidayService->getCampusList();
        return Inertia::render('Staff/CampusWeeklyHoliday/Create', [
            'campusList' => $campusList,
        ]);
    }

    public function submit(CampusWeeklyHolidayRequest $request): RedirectResponse
    {
        $this->campusWeeklyHolidayService->submit($request);
        return $this->redirectSuccess('Campus weekly holiday configured successfully!', 'campus-weekly-holiday.index');
    }

    public function edit(Request $request): Response
    {
        $holiday = \App\Models\CampusWeeklyHoliday::with('campus')->findOrFail($request->id);
        $campusList = $this->campusWeeklyHolidayService->getCampusList();
        
        return Inertia::render('Staff/CampusWeeklyHoliday/Edit', [
            'holiday' => $holiday,
            'campusList' => $campusList,
        ]);
    }

    public function update(CampusWeeklyHolidayRequest $request): RedirectResponse
    {
        $this->campusWeeklyHolidayService->update($request);
        return $this->redirectSuccess('Campus weekly holiday updated successfully!', 'campus-weekly-holiday.index');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->campusWeeklyHolidayService->destroy($request->id);
        return $this->redirectSuccess('Campus weekly holiday deleted successfully!', 'campus-weekly-holiday.index');
    }
}