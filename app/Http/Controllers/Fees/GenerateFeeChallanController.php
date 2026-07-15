<?php

namespace App\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\GenerateFeeChallanRequest;
use App\Models\Campus;
use App\Models\GenerateFeeChallan;
use App\Services\Fees\GenerateFeeChallanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class GenerateFeeChallanController extends Controller
{
    protected $generateFeeChallanService;

    public function __construct(GenerateFeeChallanService $generateFeeChallanService)
    {
        $this->generateFeeChallanService = $generateFeeChallanService;
    }

    public function index(Request $request): Response
    {
        $data = $this->generateFeeChallanService->index($request);

        return Inertia::render('Fees/GenerateChallan/List', $data);
    }

    public function submit(GenerateFeeChallanRequest $request): JsonResponse
    {
        ini_set('max_execution_time', 0);
        $studentIds = collect($request->StudentId)->pluck('id');
        if ($request->filled('ChallanMonths')) {
            $combineMonths = (bool) $request->input('CombineMonths', false);
            if ($combineMonths) {
                $this->generateFeeChallanService->submitMultiMonthCombined($request);
                $endDate = collect($request->ChallanMonths)->sort()->last().'-01';
                $challan = GenerateFeeChallan::whereIn('StudentId', $studentIds->toArray())
                    ->where('tenant_id', tenant('id'))
                    ->where('ChallanMonth', $endDate)
                    ->with('ChallanTransactions')
                    ->get();
            } else {
                // $this->generateFeeChallanService->submitMultiMonth($request);
                // $dates = collect($request->ChallanMonths)
                //      ->map(fn ($m) => $m . '-01')
                //      ->values()
                //      ->toArray();

                // $challan = GenerateFeeChallan::whereIn('StudentId', $studentIds->toArray())
                //      ->where('tenant_id', tenant('id'))
                //      ->whereIn('ChallanMonth', $dates)
                //      ->with('ChallanTransactions')
                //      ->get();
            }
        } else {
            $this->generateFeeChallanService->submit($request);
            $date = $request->ChallanMonth.'-01';
            $challan = GenerateFeeChallan::whereIn('StudentId', $studentIds->toArray())
                ->where('tenant_id', tenant('id'))
                ->where('ChallanMonth', $date)
                ->with('ChallanTransactions')
                ->get();
        }

        return response()->json([
            'challan_id' => $challan->pluck('id')->toArray(),
        ]);
    }

    public function print(Request $request): View
    {
        $tenant_id = tenant('id');
        if (session('switched_tenant_id')) {
            $tenant_id = session('switched_tenant_id');
        }
        $data = $this->generateFeeChallanService->print($request);
        $campusData = Campus::select('id', 'tenant_id', 'AccountNo', 'BranchCode', 'bankName', 'AccountTitle')->where('tenant_id', $tenant_id)->first();
        $data['campusData'] = $campusData;

        return view('challan.print', $data);
    }

    public function markAsUnpaid(Request $request)
    {
        $markAsUnpaid = $this->generateFeeChallanService->markAsUnpaid($request);
        if ($markAsUnpaid === false) {
            return $this->redirectError('Cannot mark as Unpaid. There is a subsequent challan generated for this student.', 'challan.list');
        } else {
            return $this->redirectSuccess('Challan Mark as Unpaid Successfully!', 'challan.list');
        }
    }
}
