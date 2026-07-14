<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PayrollSlip;
use App\Services\PayrollSlipService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PayrollSlipController extends Controller
{
    protected $payrollSlipService;

    public function __construct(PayrollSlipService $payrollSlipService)
    {
        $this->payrollSlipService = $payrollSlipService;
    }

    public function index(): Response
    {
        $staffList = $this->payrollSlipService->getStaffList();

        return Inertia::render('Staff/PayrollSlip/Index', [
            'staffList' => $staffList,
        ]);
    }

    public function create(Request $request): Response
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer'],
            'staff_id' => ['required', 'integer'],
        ]);

        $payrollData = $this->payrollSlipService->generatePayrollSlips(
            $validated['month'],
            $validated['year'],
            [$validated['staff_id']]
        );

        return Inertia::render('Staff/PayrollSlip/Create', [
            'payrollData' => $payrollData,
            'selectedMonth' => $validated['month'],
            'selectedYear' => $validated['year'],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2020'],
            'staff_id' => ['nullable', 'exists:staff,id'],
        ]);

        $month = $request->input('month');
        $year = $request->input('year');
        $staffId = $request->input('staff_id');

        $payrollData = $this->payrollSlipService->generatePayrollSlips($month, $year, $staffId ? [$staffId] : null);

        $this->payrollSlipService->savePayrollSlips($payrollData);

        if ($staffId) {
            $payrollSlip = PayrollSlip::with('staff')
                ->where('tenant_id', tenant('id'))
                ->where('staff_id', $staffId)
                ->where('payroll_month', $month)
                ->where('payroll_year', $year)
                ->first();

            // Return PDF directly for AJAX requests
            if ($request->header('Accept') === 'application/pdf' || $request->header('accept') === 'application/pdf') {
                $pdf = Pdf::loadView('reports.payroll-slip', compact('payrollSlip'))
                    ->setPaper('a4', 'portrait')
                    ->setOption([
                        'defaultFont' => 'Arial',
                        'isRemoteEnabled' => true,
                        'isHtml5ParserEnabled' => true,
                    ]);

                $fileName = $payrollSlip->staff->FirstName.'_'.$payrollSlip->staff->LastName.'_Payroll_Slip.pdf';

                return new \Illuminate\Http\Response($pdf->output(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
                ]);
            }

            return redirect()->away(route('payrollslip.download', ['id' => $payrollSlip->id]));
        }

        return $this->redirectSuccess('Payroll slips generated successfully!', 'payrollslip.show');
    }

    public function download(Request $request)
    {
        $payrollSlip = $this->payrollSlipService->getPayrollSlipDetail($request->id);

        $pdf = Pdf::loadView('reports.payroll-slip', compact('payrollSlip'))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ]);

        $fileName = $payrollSlip->staff->FirstName.'_'.$payrollSlip->staff->LastName.'_Payroll_Slip.pdf';

        return new \Illuminate\Http\Response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    public function show(Request $request): Response
    {
        $payrollSlips = $this->payrollSlipService->getPayrollSlipList($request);
        $staffList = $this->payrollSlipService->getStaffList();

        return Inertia::render('Staff/PayrollSlip/List', [
            'payrollSlips' => $payrollSlips,
            'staffList' => $staffList,
            'filters' => $request->only(['staff_id', 'month', 'year']),
        ]);
    }

    public function detail(Request $request): Response
    {
        $payrollSlip = $this->payrollSlipService->getPayrollSlipDetail($request->id);

        return Inertia::render('Staff/PayrollSlip/Detail', [
            'payrollSlip' => $payrollSlip,
        ]);
    }
}
