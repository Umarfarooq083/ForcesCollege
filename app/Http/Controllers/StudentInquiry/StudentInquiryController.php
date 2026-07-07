<?php

namespace App\Http\Controllers\StudentInquiry;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentInquiry\InquiryRequest;
use App\Models\Campus;
use App\Models\Classes;
use App\Models\GuardianInfo;
use App\Models\GuardianRelation;
use App\Models\Source;
use App\Models\StudentInquiry;
use App\Services\StudentInquiryService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Inertia\Response;

class StudentInquiryController extends Controller
{
    protected $StudentInquiryService;
    public function __construct(StudentInquiryService $StudentInquiryService)
    {
        $this->StudentInquiryService = $StudentInquiryService;
    }

    public function index(Request $request): Response
    {
        $studentInquiry = $this->StudentInquiryService->index($request);
        return Inertia::render('StudentInquiry/List',[
            'studentInquiry' => $studentInquiry,
        ]);
    }

    public function create(): Response
    {
        $classList = $this->campusClasses();
        $campusZoneSession = $this->campusZoneSession();
        $source = Source::get();
        $guardianrelation = $this->guardianRelation();
        return Inertia::render('StudentInquiry/Create',[
            'source' => $source,
            'guardianrelation' => $guardianrelation,
            'campusZoneSession' => $campusZoneSession,
            'classList' => $classList
        ]);
    }

    public function submit(InquiryRequest $request) : RedirectResponse
    {
        $this->StudentInquiryService->submit($request->validated(), $request);
        return $this->redirectSuccess('Student Inquiry created successfully!','inquiry.index');
    }

    public function statusUpdate(Request $request, int $id): RedirectResponse
    {
        $Inquiry = StudentInquiry::findOrFail($id);
        $Inquiry->status = $request->status;
        $Inquiry->IsActive = $request->status;
        $Inquiry->save();
        return $this->redirectSuccess('Inquiry status updated successfully!','inquiry.index');
    }

    public function edit(Request $request): Response
    {
        $classList = $this->campusClasses();
        $data = $this->StudentInquiryService->edit($request);
        $campusZoneSession = $this->campusZoneSession();
        $guardianrelation = $this->guardianRelation();
        return Inertia::render('StudentInquiry/Edit',[
            'inquiryData' => $data['inquiryData'],
            'sessionList' => $data['sessionList'],
            'classesList' => $data['classesList'],
            'source' => $data['source'],
            'campusZoneSession' => $campusZoneSession,
            'guardianrelation' => $guardianrelation,
            'classList' => $classList
        ]);
    }

    public function checkGuardian(Request $request): Collection | array
    {
        $session_tenant = session('switched_tenant_id');
        $guardianInfo = GuardianInfo::query();
        if($session_tenant){
            $guardianInfo = $guardianInfo->where('tenant_id',$session_tenant)->where('cnic',$request->cnic)->first();
        }else{
            $guardianInfo = $guardianInfo->Tenant()->where('cnic',$request->cnic)->first();
        }
        $data['guardianInfo']= $guardianInfo;
        $data['studentInquiry'] = [];
        if($guardianInfo)
        {
           $studentInquiry = StudentInquiry::where('guardian_id',$guardianInfo->id)->get();
           $data['studentInquiry'] = $studentInquiry; 
        }
        return $data;
    }

    public function update(InquiryRequest $request): RedirectResponse
    {
        $studentInquiryUpdate = StudentInquiry::findOrFail($request->id);
        $studentInquiryUpdate->update([
            ...$request->validated(),
            'status' => 0,
        ]);
        return $this->redirectSuccess('Inquiry updated successfully!','inquiry.index');
    }

    private function campusZoneSession(): Collection | Campus
    {
        return Campus::select('id','SchoolName','zoneid')->Tenant()->with(['sessionYear' => fn($q) => $q->active()])->first();
    }

    private function campusClasses(): Collection | Classes
    {
        $campusData = Campus::select('id','tenant_id','SchoolName')->Tenant()->with('campusClassType')->first();
        $campusClassType = data_get($campusData, 'campusClassType');
        $classType_id = $campusClassType->pluck('class_type_id')->toArray();
        return Classes::select('id','tenant_id','ClassName')
            ->where('IsActive',1)
            ->whereIn('class_type_id',$classType_id)
            ->get();
    }

    public function detail(Request $request): Response
    {
        $classList = $this->campusClasses();
        $data = $this->StudentInquiryService->edit($request);
        $campusZoneSession = $this->campusZoneSession();
        $guardianrelation = $this->guardianRelation();
        return Inertia::render('StudentInquiry/Detail',[
            'inquiryData' => $data['inquiryData'],
            'sessionList' => $data['sessionList'],
            'classesList' => $data['classesList'],
            'source' => $data['source'],
            'campusZoneSession' => $campusZoneSession,
            'guardianrelation' => $guardianrelation,
            'classList' => $classList
        ]);
    }

    private function guardianRelation(): Collection | GuardianRelation
    {
       return  GuardianRelation::get();
    }
}
