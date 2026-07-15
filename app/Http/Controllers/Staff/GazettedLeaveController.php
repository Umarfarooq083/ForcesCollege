<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\GazettedLeaveRequest;
use App\Services\GazettedLeaveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GazettedLeaveController extends Controller
{
    protected $gazettedLeaveService;

    public function __construct(GazettedLeaveService $gazettedLeaveService)
    {
        $this->gazettedLeaveService = $gazettedLeaveService;
    }

    public function index(): Response
    {
        $gazettedLeaves = $this->gazettedLeaveService->index();

        return Inertia::render('Staff/GazettedLeave/List', [
            'gazettedLeaves' => $gazettedLeaves,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Staff/GazettedLeave/Create');
    }

    public function submit(GazettedLeaveRequest $request): RedirectResponse
    {
        $this->gazettedLeaveService->submit($request);

        return $this->redirectSuccess('Gazetted leave created successfully!', 'gazettedleave.index');
    }

    public function edit(Request $request): Response
    {
        $gazettedLeave = \App\Models\GazettedLeave::findOrFail($request->id);

        return Inertia::render('Staff/GazettedLeave/Edit', [
            'gazettedLeave' => $gazettedLeave,
        ]);
    }

    public function update(GazettedLeaveRequest $request): RedirectResponse
    {
        $this->gazettedLeaveService->update($request);

        return $this->redirectSuccess('Gazetted leave updated successfully!', 'gazettedleave.index');
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->gazettedLeaveService->destroy($request);

        return $this->redirectSuccess('Gazetted leave deleted successfully!', 'gazettedleave.index');
    }
}
