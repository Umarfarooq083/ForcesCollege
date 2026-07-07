<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\HomeWorkRequest;
use App\Models\HomeWork;
use App\Models\StudentLog;
use App\Services\HomeWorkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class HomeWorkController extends Controller
{
    protected $homeWorkService;
    public function __construct(HomeWorkService $homeWorkService)
    {
        $this->homeWorkService = $homeWorkService;
    }

    public function index(Request $request): Response
    {
        $data['homework_list'] = $this->homeWorkService->index($request);
        return Inertia::render('HomeWork/List', $data);
    }

    public function create(Request $request): Response
    {
        $data = $this->homeWorkService->create();
        return Inertia::render('HomeWork/Create', $data);
    }

    public function submit(HomeWorkRequest $request): RedirectResponse
    {
        $this->homeWorkService->submit($request);
        userActivityLogs('Student Home Work Created and User ID: '.auth()->user()->id.'', StudentLog::class);
        return $this->redirectSuccess('Home work has been created successfully!', 'homework.index');
    }

    public function edit(Request $request): Response
    {
       $request->validate([
            'id' => 'required|integer|exists:home_works,id',
        ]);

       $data = $this->homeWorkService->create();
       $data['home_work'] = HomeWork::where('id', $request->id)->where('tenant_id', tenant('id'))->first();
       return Inertia::render('HomeWork/Edit', $data);
    }

    public function update(HomeWorkRequest $request): RedirectResponse
    {
        $this->homeWorkService->update($request);
        userActivityLogs('Student Home Work Updated and id is '.$request->id.' By User ID: '.auth()->user()->id.'', StudentLog::class);
        return $this->redirectSuccess('Home work has been updated successfully!', 'homework.index');
    }

    public function show(Request $request): Response
    {
        $request->validate([
            'id' => 'required|integer|exists:home_works,id',
        ]);
        $data = $this->homeWorkService->create();
        $data['home_work'] = HomeWork::where('id', $request->id)->where('tenant_id', tenant('id'))->first();
        return Inertia::render('HomeWork/Detail', $data);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'id' => 'required|integer|exists:home_works,id',
        ]);
        $home_work = HomeWork::where('id', $request->id)->where('tenant_id', tenant('id'))->delete();
        return $this->redirectSuccess('Home work has been deleted successfully!', 'homework.index');
    }

}
