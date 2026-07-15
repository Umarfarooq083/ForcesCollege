<?php

namespace App\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Models\ArrearChallanDetails;
use App\Models\ChallanTransactions;
use App\Models\FeeLog;
use App\Models\GenerateFeeChallan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class DeleteChallanController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Fees/DeleteChallan/List');
    }

    public function fetchChallan(Request $request): GenerateFeeChallan
    {
        $challanId = $request->input('id');

        return GenerateFeeChallan::where('tenant_id', tenant('id'))
            ->where('challan_no', $challanId)
            ->with('StudentRel.class', 'StudentRel.section')
            ->first();

    }

    public function deleteChallan(Request $request)
    {
        $challanId = $request->input('id');

        $generateFeeChallan = GenerateFeeChallan::where('tenant_id', tenant('id'))
            ->where('id', $challanId)
            ->first();
        if ($generateFeeChallan->IsPartialPayment === 1 || $generateFeeChallan->Status == 'Paid') {
            return throw ValidationException::withMessages([
                'ChallanId' => 'Challan is partial or fully paid',
            ]);
        } else {
            $generateFeeChallan->update([
                'ModifiedBy' => auth()->user()->id,
            ]);
            $generateFeeChallan->delete();
            ChallanTransactions::where('tenant_id', tenant('id'))->where('generate_challan_id', $generateFeeChallan->id)->update([
                'deleted_at' => now(),
                'IsActive' => 0,
            ]);

            ArrearChallanDetails::where('tenant_id', tenant('id'))->where('GenerateFeeChallanId', $generateFeeChallan->id)->update([
                'deleted_at' => now(),
                'IsActive' => 0,
            ]);
            userActivityLogs('Deleted Challan Id '.$generateFeeChallan->id.' And Challan Deleted By User ID'.auth()->user()->id.' ', FeeLog::class);

            return response()->json(['ChallanId' => 'Challan deleted successfully']);
            // return $this->redirectSuccess('Challan deleted successfully', 'deletechallan.list');
        }

    }
}
