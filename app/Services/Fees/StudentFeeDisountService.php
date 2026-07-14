<?php

namespace App\Services\Fees;

use App\Models\FeeLog;
use App\Models\Student;
use App\Models\StudentFeeDiscount;
use App\Models\StudentFeeDiscount as ModelsStudentFeeDiscount;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class StudentFeeDisountService
{
    // public function submit($request): void
    // {
    //     $typeOfRequest = gettype($request->StudentIdArray);
    //     dd($request->validated(), $typeOfRequest, $request->all());
    //     if ($typeOfRequest === 'integer') {
    //         $this->handleIntegerSubmit($request);
    //     } else {
    //         $this->handleArraySubmit($request);
    //     }
    // }

    public function submit($request): void
    {
        $validated = $request->validated();
        $this->handleMultiFeeCardsSubmit($validated);
    }

    public function edit($request): Collection|ModelsStudentFeeDiscount
    {
        return ModelsStudentFeeDiscount::with('studentRel', 'studentRel', 'SessionRel', 'CampusFeesMasterRel.FeeTypeRel', 'ClassRel', 'SectionRel')
            ->where('id', $request->id)
            ->first();
    }

    private function handleMultiFeeCardsSubmit(array $data): void
    {
        $sessionId = fetchCurrentSession();
        $createFee = [];

        foreach ($data['FeeCards'] as $key => $card) {
            $createFee[$key] = [
                'tenant_id' => tenant('id'),
                'IsActive' => 1,
                'CreatedBy' => auth()->user()->id,
                'SessionId' => $sessionId->id,
                'ClassId' => $data['ClassId'],
                'SectionId' => $data['SectionId'],
                'StudentId' => $card['studentId'],
                'CampusFeesMasterId' => $card['masterId'],
                'loadedCampusMaster' => $data['loadedCampusMaster'],
                'DiscountAmount' => $card['discountAmount'],
                'BalanceFeeAfterDiscount' => $card['balanceAmount'],
                'TotalFee' => $card['totalFee'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $exists = StudentFeeDiscount::where('tenant_id', tenant('id'))
                ->where('StudentId', $card['studentId'])
                ->where('CampusFeesMasterId', $card['masterId'])
                ->where('loadedCampusMaster', $data['loadedCampusMaster'])
                ->exists();
            if ($exists) {
                $studentName = Student::where('id', $card['studentId'])->first('FirstName');
                throw ValidationException::withMessages([
                    'msg' => 'Discount already applied for Student: '.$studentName->FirstName,
                ]);
            }
        }

        $created = StudentFeeDiscount::insert($createFee);

        if ($created) {
            userActivityLogs('Fee Discount Created and By User ID: '.auth()->user()->id.'', FeeLog::class);
        }
    }

    public function update($request): void
    {
        $studentFeeDiscount = ModelsStudentFeeDiscount::where('tenant_id', tenant('id'))->where('id', $request->id)->first();
        $updated = $studentFeeDiscount->update([
            'DiscountAmount' => $request->discountAmount,
            'BalanceFeeAfterDiscount' => $request->BelanceAmount,
            'TotalFee' => $request->TotalFee,
        ]);

        if ($updated) {
            userActivityLogs('Fee Discount Updated and id is '.$request->id.' By User ID: '.auth()->user()->id.'', FeeLog::class);
        }
    }

    public function delete($request): void
    {
        $studentFeeDiscount = ModelsStudentFeeDiscount::where('tenant_id', tenant('id'))->where('id', $request->id)->delete();
        if ($studentFeeDiscount) {
            userActivityLogs('Fee Discount Deleted and id is '.$request->id.'  By User ID: '.auth()->user()->id.'', FeeLog::class);
        }
    }
}
