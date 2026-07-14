<?php

namespace App\Services\Fees;

use App\Models\CampusFeesMaster;
use App\Models\FeeLog;
use App\Models\FeesType;
use App\Models\OptionalFeeMaster;

class OptionalMappingService
{
    public function index($request)
    {
        $optionalFeeMaster = OptionalFeeMaster::Tenant()
            ->whereHas('feeTypeRel')
            ->with('classRel', 'sectionRel', 'studentRel', 'feeTypeRel', 'campusMasterRel')
            ->where('IsActive', 1)
            ->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;

            $optionalFeeMaster->where(function ($q) use ($search) {
                $q->whereHas('classRel', function ($sub) use ($search) {
                    $sub->where('ClassName', 'like', "%{$search}%");
                })
                    ->orWhereHas('sectionRel', function ($sub) use ($search) {
                        $sub->where('SectionName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('studentRel', function ($sub) use ($search) {
                        $sub->where('FirstName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('feeTypeRel', function ($sub) use ($search) {
                        $sub->where('FeeName', 'like', "%{$search}%");
                    });
            });
        }

        return $optionalFeeMaster->paginate(25)->withQueryString();

        // return OptionalFeeMaster::Tenant()
        //     ->with('classRel', 'sectionRel', 'studentRel', 'feeTypeRel', 'campusMasterRel', 'campusMasterRel')
        //     ->where('IsActive', 1)
        //     ->paginate(25);
    }

    public function create(): array
    {
        $currentSession = fetchCurrentSession();
        $data = classAndSections();
        $data['feesType'] = FeesType::select('id', 'FeeName', 'IsOptional', 'IsActive')->where('IsOptional', 1)->get();
        $data['campusFeesMaster'] = [];
        if ($currentSession) {
            $data['campusFeesMaster'] = CampusFeesMaster::where('tenant_id', tenant('id'))
                ->where('SessionId', $currentSession->id)
                ->with(['FeeTypeRel' => function ($q) {
                    $q->select('id', 'FeeName', 'IsOptional', 'IsActive')->where('IsOptional', 1);
                }])
                ->get();
        }

        $data['currentSession'] = $currentSession;

        return $data;
    }

    public function submit($request)
    {
        $insertArray = [];
        foreach ($request->StudentId as $key => $student) {
            $insertArray[$key]['tenant_id'] = tenant('id');
            $insertArray[$key]['FeesTypeNId'] = $request->FeesTypeNId;
            $insertArray[$key]['ClassId'] = $request->ClassId;
            $insertArray[$key]['SectionId'] = $request->SectionId;
            $insertArray[$key]['StudentId'] = $student['id'];
            $insertArray[$key]['FromMonth'] = $request->FromMonth.'-01';
            $insertArray[$key]['ToMonth'] = $request->ToMonth.'-01';
            $insertArray[$key]['CampusFeesMasterId'] = $request->CampusFeesMasterId;
            $insertArray[$key]['CreatedBy'] = auth()->user()->id;
        }
        $created = OptionalFeeMaster::insert($insertArray);

        if ($created) {
            userActivityLogs('Optional Fee Master Created and By User ID: '.auth()->user()->id.'', FeeLog::class);
        }
    }
}
