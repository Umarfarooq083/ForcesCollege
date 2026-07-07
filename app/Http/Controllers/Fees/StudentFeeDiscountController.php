<?php

namespace App\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\StudentFeeDiscount;
use App\Models\CampusFeesMaster;
use App\Models\FeesType;
use App\Models\StudentFeeDiscount as ModelsStudentFeeDiscount;
use App\Services\Fees\OptionalMappingService;
use App\Services\Fees\StudentFeeDisountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class StudentFeeDiscountController extends Controller
{
    protected $optionalMappingService;
    protected $studentFeeDisountService;
    public function __construct(StudentFeeDisountService $studentFeeDisountService, OptionalMappingService $optionalMappingService)
    {
        $this->optionalMappingService = $optionalMappingService;
        $this->studentFeeDisountService = $studentFeeDisountService;
    }

    // public function index(): Response
    // {
    //     $studentFeeDiscount = ModelsStudentFeeDiscount::where('tenant_id',tenant('id'))
    //     ->with('studentRel.class','studentRel.section','SessionRel','CampusFeesMasterRel.FeeTypeRel')
    //         ->paginate(10);
    //     return Inertia::render('Fees/FeeDiscount/List',[
    //         'studentFeeDiscount' =>$studentFeeDiscount, 
    //     ]);
    // }

    public function index(Request $request): Response
    {
        $query = ModelsStudentFeeDiscount::where('tenant_id', tenant('id'))->where('IsActive',1)->where('sessionid', fetchCurrentSession()->id)
         ->whereHas('CampusFeesMasterRel.FeeTypeRel')
            ->with([
                'studentRel',
                'SessionRel',
                'CampusFeesMasterRel.FeeTypeRel',
                'ClassRel',
                'SectionRel'
            ]);

        if ($request->filled('search')) {

            $search = trim(preg_replace('/\s+/', ' ', $request->search));
            $words  = explode(' ', $search);

            $query->where(function ($q) use ($words, $search) {

                $q->whereHas('studentRel', function ($sub) use ($words) {

                    foreach ($words as $word) {
                        $sub->where(function ($nameQuery) use ($word) {

                            $nameQuery
                                ->where('FirstName', 'like', "%{$word}%")
                                ->orWhere('LastName', 'like', "%{$word}%")
                                ->orWhereRaw(
                                    "CONCAT(
                                TRIM(COALESCE(FirstName,'')),
                                ' ',
                                TRIM(COALESCE(LastName,''))
                            ) LIKE ?",
                                    ["%{$word}%"]
                                );
                        });
                    }
                })
                    ->orWhereHas('studentRel.class', function ($sub) use ($search) {
                        $sub->where('ClassName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('studentRel.section', function ($sub) use ($search) {
                        $sub->where('SectionName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('CampusFeesMasterRel.FeeTypeRel', function ($sub) use ($search) {
                        $sub->where('FeeName', 'like', "%{$search}%");
                    });
            });
        }


        $query->orderBy('id', 'desc');
        $studentFeeDiscount = $query->paginate(25)->withQueryString();

        return Inertia::render('Fees/FeeDiscount/List', [
            'studentFeeDiscount' => $studentFeeDiscount,
            // 'filters' => $request->only('search'), 
        ]);
    }

    public function create(): Response
    {
        $data = $this->optionalMappingService->create();
        return Inertia::render('Fees/FeeDiscount/Create', $data);
    }

    public function optionalFeeMapping(Request $request): Collection
    {
        return $this->campuseeMasterFun($request->classId, 1);
    }

    public function optionalFeeMappingMaster(Request $request): Collection
    {
        return $this->campuseeMasterFun($request->classId, 0);
    }

    public function submit(StudentFeeDiscount $request): RedirectResponse
    {
        $this->studentFeeDisountService->submit($request);
        return $this->redirectSuccess('Student discount created successfully!', 'discount.list');
    }

    public function edit(Request $request): Response
    {
        $studentFeeDiscount = $this->studentFeeDisountService->edit($request);
        return Inertia::render('Fees/FeeDiscount/Edit', [
            'studentFeeDiscount' => $studentFeeDiscount
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->studentFeeDisountService->update($request);
        return $this->redirectSuccess('Student discount updated successfully!', 'discount.list');
    }
    
    public function delete(Request $request): RedirectResponse
    {
        $this->studentFeeDisountService->delete($request);
        return $this->redirectSuccess('Student discount deleted successfully!', 'discount.list');
    }

    private function campuseeMasterFun(int $classId, int $IsOptional): Collection
    {
        $current_session = fetchCurrentSession();
        if ($IsOptional == 0) {
            $feesType = FeesType::where('IsOptional', 0)->get()->pluck('id')->toArray();
        } else {
            $feesType = FeesType::where('IsOptional', 1)->get()->pluck('id')->toArray();
        }
        // dd($feesType,$IsOptional);
        return CampusFeesMaster::where('tenant_id', tenant('id'))
            ->whereIn('FeesTypeNId', $feesType)
            ->where('ClassId', $classId)
            ->where('SessionId', $current_session->id)
            ->with('FeeTypeRel')
            ->get();
    }
}
