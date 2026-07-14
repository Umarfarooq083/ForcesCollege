<?php

namespace App\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\FeeTypeRequest;
use App\Models\FeeLog;
use App\Models\FeesType;
use App\Services\Fees\FeeTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeesTypeController extends Controller
{
    protected $feeTypeService;

    public function __construct(FeeTypeService $feeTypeService)
    {
        $this->feeTypeService = $feeTypeService;
    }

    public function index(Request $request): Response
    {
        $feesType = $this->feeTypeService->index($request);

        return Inertia::render('Fees/FeesType/List', [
            'feesType' => $feesType,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Fees/FeesType/Create');
    }

    public function submit(FeeTypeRequest $request): RedirectResponse
    {
        $this->feeTypeService->submit($request);

        return $this->redirectSuccess('Fee type created successfully!', 'fees.list');
    }

    public function edit(Request $request): Response
    {
        $feesType = FeesType::find($request->id);

        return Inertia::render('Fees/FeesType/Edit', [
            'feesType' => $feesType,
        ]);
    }

    public function update(FeeTypeRequest $request): RedirectResponse
    {
        $this->feeTypeService->update($request);

        return $this->redirectSuccess('Fee type updated successfully!', 'fees.list');
    }

    public function delete(Request $request): RedirectResponse
    {
        $deleted = FeesType::findorFail($request->id)->delete();

        if ($deleted) {
            userActivityLogs('Fee Type Deleted and id is '.$request->id.' User ID: '.auth()->user()->id.'', FeeLog::class);
        }

        return $this->redirectSuccess('Fee type deleted successfully!', 'fees.list');
    }
}
