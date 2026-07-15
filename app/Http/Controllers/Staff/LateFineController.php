<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\LateFineRequest;
use App\Services\LateFineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LateFineController extends Controller
{
    protected $lateFineService;

    public function __construct(LateFineService $lateFineService)
    {
        $this->lateFineService = $lateFineService;
    }

    public function index(): Response
    {
        $lateFines = $this->lateFineService->index();

        return Inertia::render('Staff/LateFine/List', [
            'lateFines' => $lateFines,
        ]);
    }

    public function create(): Response
    {
        $data = $this->lateFineService->create();

        return Inertia::render('Staff/LateFine/Create', [
            'data' => $data,
        ]);
    }

    public function submit(LateFineRequest $request): RedirectResponse
    {
        $this->lateFineService->submit($request);

        return $this->redirectSuccess('Late fine created successfully!', 'latefine.index');
    }

    public function edit(Request $request): Response
    {
        $data = $this->lateFineService->edit($request);

        return Inertia::render('Staff/LateFine/Edit', [
            'lateFine' => $data->lateFine,
            'staffList' => $data->staffList,
        ]);
    }

    public function update(LateFineRequest $request): RedirectResponse
    {
        $this->lateFineService->update($request);

        return $this->redirectSuccess('Late fine updated successfully!', 'latefine.index');
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->lateFineService->destroy($request);

        return $this->redirectSuccess('Late fine deleted successfully!', 'latefine.index');
    }
}
