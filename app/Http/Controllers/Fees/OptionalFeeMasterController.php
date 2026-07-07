<?php

namespace App\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\OptionalFeesRequest;
use App\Models\FeeLog;
use App\Models\OptionalFeeMaster;
use App\Models\Student;
use App\Services\Fees\OptionalMappingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class OptionalFeeMasterController extends Controller
{
    protected $optionalMappingService;
    public function __construct(OptionalMappingService $optionalMappingService)
    {
        $this->optionalMappingService = $optionalMappingService;
    }

    public function index(Request $request): Response
    {
        $optionalFeeMaster = $this->optionalMappingService->index($request);
        return Inertia::render('Fees/OptionalFeeMapping/List',[
            'optionalFeeMaster' =>$optionalFeeMaster
        ]);
    }

    public function create(): Response
    {
        $data = $this->optionalMappingService->create();
        return Inertia::render('Fees/OptionalFeeMapping/Create',$data);
    }
   
    public function submit(OptionalFeesRequest $request): RedirectResponse
    {
        $this->optionalMappingService->submit($request);
        return $this->redirectSuccess('Optional Fee type created successfully!', 'optionalfee.list');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'optional_fee_id' => 'required|integer|exists:optional_fee_mapping,id'
        ]);
        $fee = OptionalFeeMaster::findOrFail($request->optional_fee_id);
        
        if($fee->delete()){
            userActivityLogs('Optional Fee Master Deleted id is '.$request->optional_fee_id.' and User ID: '.auth()->user()->id.'', FeeLog::class);
        }
        return $this->redirectSuccess('Optional Fee has been deleted successfully!', 'optionalfee.list');
    }
   
    public function createFetchStudent(Request $request): Collection
    {
      $tenant_id = tenant('id');
      if(session('switched_tenant_id')){
        $tenant_id = session('switched_tenant_id');
      } 
    return Student::select('id','tenant_id','FirstName','ClassId','SectionId','LastName','IsDisable')
            ->where('tenant_id',$tenant_id)->where('IsActive', 1)->where('ClassId',$request->classId)->where('SectionId',$request->sectoinId)
            ->get();
    }
}
