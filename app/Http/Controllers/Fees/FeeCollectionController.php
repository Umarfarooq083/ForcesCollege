<?php

namespace App\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\FeeCollectionRequest;
use App\Models\Campus;
use App\Models\FeeLog;
use App\Models\GenerateFeeChallan;
use App\Services\Fees\FeeCollectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;
class FeeCollectionController extends Controller
{
    protected $feeCollectionService;
    public function __construct(FeeCollectionService $feeCollectionService)
    {
        $this->feeCollectionService = $feeCollectionService;
    }

    public function index(Request $request): Response
    {
        $data = $this->feeCollectionService->index($request);
        return Inertia::render('Fees/FeeCollection/ChallanList', $data);
    }

    public function create(FeeCollectionRequest $request): Response|RedirectResponse
    {
        $data = $this->feeCollectionService->create($request->validated());

        if (!empty($data['error'])) {
            return $this->redirectSuccess($data['error'], 'fee.collection.list'); 
        }

        $challan_after_discount = $data['GenerateFeeChallan']->transection_sum_balancefeeafterdiscount;
        $data['TotalChallanAmount'] = ($data['total_challan_amount'] + $challan_after_discount - $data['wived_off']);
        // dd($data['TotalChallanAmount']);
        return Inertia::render('Fees/FeeCollection/Create', $data);
    }

    public function submit(FeeCollectionRequest $request): RedirectResponse
    {
        $receipt = $this->feeCollectionService->submit($request);

        $tenantId = tenant('id');
        if (session('switched_tenant_id')) {
            $tenantId = session('switched_tenant_id');
        }

        $challan = GenerateFeeChallan::where('tenant_id', $tenantId)
            ->with(['StudentRel', 'ClassRel', 'SectionRel'])
            ->find($receipt['generate_fee_challan_id']);

        if ($challan) {
            $receipt['student_name'] = trim(($challan->StudentRel?->FirstName ?? '') . ' ' . ($challan->StudentRel?->LastName ?? ''));
            $receipt['roll_no'] = $challan->StudentRel?->RollNumber;
            $receipt['father_name'] = $challan->StudentRel?->FatherName;
            $receipt['class_name'] = $challan->ClassRel?->ClassName;
            $receipt['section_name'] = $challan->SectionRel?->SectionName;
        }

        userActivityLogs('Fee Collected and Challan Id Is: '.$challan->id.' And Challan Collected By User ID: '.auth()->user()->id.' ', FeeLog::class);

        return redirect()
            ->route('fee.collection.list')
            ->with('toast', ['type' => 'success', 'message' => 'Fee collection submitted successfully.'])
            ->with('receipt', $receipt);
    }

    public function show(FeeCollectionRequest $request): Response|RedirectResponse
    {
        $data = $this->feeCollectionService->show($request->validated());
        if (!empty($data['error'])) {
            return $this->redirectSuccess($data['error'], 'fee.collection.list'); 
        }
        return Inertia::render('Fees/FeeCollection/Details', $data);
    }

    public function delete($id): RedirectResponse
    {
        $this->feeCollectionService->delete($id);
        return $this->redirectSuccess('challan item deleted successfully.', 'fee.collection'); 
    }

    public function receipt(Request $request, int $id): View
    {
        $tenantId = tenant('id');
        if (session('switched_tenant_id')) {
            $tenantId = session('switched_tenant_id');
        }

        $receipt = session('receipt');
        if (!is_array($receipt) || ((int) ($receipt['generate_fee_challan_id'] ?? 0) !== $id)) {
            abort(404, 'Receipt not found (receipt is not stored).');
        }

        $challan = \App\Models\GenerateFeeChallan::where('tenant_id', $tenantId)
            ->with(['StudentRel', 'ClassRel', 'SectionRel'])
            ->findOrFail($id);

        $campusData = Campus::select('id', 'tenant_id', 'SchoolName', 'Address', 'PhoneNo', 'MobileNo', 'Logo')
            ->where('tenant_id', $tenantId)
            ->first() ?? new Campus();

        return view('fees.receipt', [
            'receipt' => $receipt,
            'challan' => $challan,
            'campusData' => $campusData,
        ]);
    }
}
