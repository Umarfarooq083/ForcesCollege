<?php

namespace App\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\CampusFeeMasterRequest;
use App\Models\CampusFeesMaster;
use App\Services\Fees\CampusFeesMasterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CampusFeesMasterController extends Controller
{
    protected $campusFeeMasterService;
    public function __construct(CampusFeesMasterService $campusFeeMasterService)
    {
        $this->campusFeeMasterService = $campusFeeMasterService;   
    }

    public function index(Request $request): Response
    {
        $data = $this->campusFeeMasterService->index($request);
        return Inertia::render('Fees/CampusFeesMaster/List', $data);
    }

    public function create(): Response
    {
       $data = $this->campusFeeMasterService->getData();
       return Inertia::render('Fees/CampusFeesMaster/Create', $data);
    }

    public function submit(CampusFeeMasterRequest $request): RedirectResponse
    {
        $this->campusFeeMasterService->submit($request->validated()); 
        return $this->redirectSuccess('Campus fee master saved successfully.', 'feemaster.list'); 
    }

    public function edit(CampusFeeMasterRequest $request): Response
    {
       $data = $this->campusFeeMasterService->edit($request);
       return Inertia::render('Fees/CampusFeesMaster/Edit', $data);
    }

    public function update(CampusFeeMasterRequest $request): RedirectResponse
    {
       $this->campusFeeMasterService->update($request);  
        return $this->redirectSuccess('Campus fee master saved successfully.', 'feemaster.list'); 
    }
    
    public function delete(CampusFeeMasterRequest $request): RedirectResponse
    {
        $data =  $this->campusFeeMasterService->delete($request); 
        if (!$data) {
            return $this->redirectError('Cannot delete fee master as it is associated with optional fees or student discounts.', 'feemaster.list');
        }
        return $this->redirectSuccess('Campus fee master deleted successfully.', 'feemaster.list'); 
    }

    public function getClasses(Request $request): array
    {
       return CampusFeesMaster::where('FeesTypeNId', $request->fee_type_id)->where('tenant_id', tenant('id'))->pluck('ClassId')->toArray();   
    }

}
