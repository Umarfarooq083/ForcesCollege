<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\GuardianInfo;
use App\Models\LmsSession;
use App\Models\Source;
use App\Models\StudentInquiry;
use App\Models\StudentLog;

class StudentInquiryService
{
    public function index($request)
    {
        $studentInquiry = StudentInquiry::query();
        $studentInquiry->where('tenant_id', tenant('id'))->with('source', 'studentInquiryRelation', 'guardian')->orderBy('id', 'desc');
        if ($request->filled('search')) {
            $studentInquiry->where(function ($q) use ($request) {
                $q->where('Name', 'like', '%'.$request->search.'%')
                    ->orWhere('FatherName', 'like', '%'.$request->search.'%')
                    ->orWhere('FatherPhoneNo', 'like', '%'.$request->search.'%');
            });
        }

        return $studentInquiry->paginate(25)->withQueryString();
    }

    public function submit($validated, $request)
    {
        if ($request->guardian_id) {
            $created = StudentInquiry::create([
                ...$validated,
                'tenant_id' => tenant('id'),
                'guardian_id' => $request->guardian_id,
                'CreatedBy' => auth()->user()->id,
            ]);

            if ($created) {
                userActivityLogs('Student Inquiry Created and By User ID: '.auth()->user()->id.'', StudentLog::class);
            }
        } else {
            $guardianInfo = new GuardianInfo;
            $guardianInfo->name = $request->FatherName;
            $guardianInfo->cnic = $request->cnic;
            $guardianInfo->tenant_id = tenant('id');
            $guardianInfo->save();

            $created = StudentInquiry::create([
                ...$validated,
                'tenant_id' => tenant('id'),
                'guardian_id' => $guardianInfo->id,
                'CreatedBy' => auth()->user()->id,
            ]);

            if ($created) {
                userActivityLogs('Student Inquiry Created and By User ID: '.auth()->user()->id.'', StudentLog::class);
            }
        }

    }

    public function edit($request)
    {
        $data['inquiryData'] = StudentInquiry::Tenant()->ById($request->id)->with('guardian')->first();
        $data['sessionList'] = LmsSession::get();
        $data['classesList'] = Classes::get();
        $data['source'] = Source::get();

        return $data;
    }
}
