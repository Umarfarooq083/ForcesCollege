<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CampusFeesMaster;
use App\Models\LmsSession;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentFeeDiscount;
use App\Models\StudentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PromoteStudentController extends Controller
{
    public function index()
    {
        $classList = campusClass();
        $sectionList = Section::where('tenant_id', tenant('id'))->get();
        $activeSession = fetchCurrentSession();
        $activeSessionList = LmsSession::orderBy('id', 'desc')->get();
        // dd($activeSession);
        return inertia('PromoteStudent/List', [
            'classList' => $classList,
            'sectionList' => $sectionList,
            'activeSession' => $activeSession,
            'activeSessionsList' => $activeSessionList
        ]);
    }

    public function fetch(Request $request)
    {
        $studentData = Student::select('id', 'tenant_id', 'IsActive', 'RollNumber', 'FirstName', 'LastName','ClassId','SectionId','SessionId')
            ->with(['studentFeeDiscount' => function ($query) use ($request) {
                $query->where('SessionId', $request->fetch_student['SessionId'])->with('CampusFeesMasterRel.FeeTypeRel');
            }])
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->where('ClassId', $request->fetch_student['ClassId'])
            ->where('SectionId', $request->fetch_student['SectionId'])
            ->where('SessionId', $request->fetch_student['SessionId'])
            ->get();
        return $studentData;
    }

    public function promotionlist(Request $request)
    {
        $previousStudentData = Student::select('id', 'tenant_id', 'IsActive', 'RollNumber', 'FirstName', 'LastName')
            ->whereIn('id', $request->new_request_student['student_id'])
            ->with(['studentFeeDiscount' => function ($query) use ($request) {
                $query->where('SessionId', $request->old_request_student['SessionId'])->with('CampusFeesMasterRel.FeeTypeRel');
            }])
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->where('ClassId', $request->old_request_student['ClassId'])
            ->where('SectionId', $request->old_request_student['SectionId'])
            ->where('SessionId', $request->old_request_student['SessionId'])
            ->get();

        $studentData = Student::select('id', 'tenant_id', 'IsActive', 'RollNumber', 'FirstName', 'LastName')
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->whereIn('id', $request->new_request_student['student_id'])->get();

        $campusFeesMasterData = CampusFeesMaster::where('tenant_id', tenant('id'))
            ->with('FeeTypeRel')
            ->where('IsActive', 1)
            ->where('ClassId', $request->new_request_student['PromoteClassId'])
            ->where('SessionId', $request->new_request_student['PromoteSessionId'])
            ->get();

        $data['previousStudentData'] = $previousStudentData;
        $data['studentData'] = $studentData;
        $data['campusFeesMasterData'] = $campusFeesMasterData;
        return $data;
    }

    public function promotionSubmit(Request $request)
    {
        DB::beginTransaction();
        try {
            $campusFeesMaster = CampusFeesMaster::where('tenant_id', tenant('id'))
                ->where('SessionId', $request->promote_student['PromoteSessionId'])
                ->where('ClassId', $request->promote_student['PromoteClassId'])
                ->get();

            foreach ($request->start_promotion as $promote) {
                $discountedObjects = [];
                if (count($promote['fees']) > 0) {
                    foreach ($promote['fees'] as $key => $feesData) {
                        if ($feesData['campus_fee_master_id']) {
                            $campusFeesMasterfilterd = $campusFeesMaster->where('id', $feesData['campus_fee_master_id'])->first();
                            $discountedObjects[$key]['tenant_id'] = tenant('id');
                            $discountedObjects[$key]['IsActive'] = 1;
                            $discountedObjects[$key]['CreatedBy'] = auth()->user()->id;
                            $discountedObjects[$key]['SessionId'] = $request->promote_student['PromoteSessionId'];
                            $discountedObjects[$key]['StudentId'] = $promote['student_id'];
                            $discountedObjects[$key]['CampusFeesMasterId'] = $campusFeesMasterfilterd->id;
                            $discountedObjects[$key]['DiscountAmount'] = $feesData['discount_amount'];
                            $discountedObjects[$key]['BalanceFeeAfterDiscount'] = $campusFeesMasterfilterd->Amount - $feesData['discount_amount'];
                            $discountedObjects[$key]['TotalFee'] = $campusFeesMasterfilterd->Amount;
                            $discountedObjects[$key]['loadedCampusMaster'] = 1;
                            $discountedObjects[$key]['ClassId'] = $request->promote_student['PromoteClassId'];
                            $discountedObjects[$key]['SectionId'] = $request->promote_student['PromoteSectionId'];
                            $discountedObjects[$key]['created_at'] = now();
                        }
                    }
                }
                Student::where('id',$promote['student_id'])->update([
                    'promoted_date' => now(),
                    'ClassId' => $request->promote_student['PromoteClassId'],
                    'SectionId' => $request->promote_student['PromoteSectionId'],
                    'SessionId' => $request->promote_student['PromoteSessionId']
                ]);
                userActivityLogs('Student promoted student id '.$promote['student_id'].' promoted session id '.$request->promote_student['PromoteSessionId'].'  and Promoted By => User ID: '.auth()->user()->id.'', StudentLog::class);
                StudentFeeDiscount::insert($discountedObjects);
            }

              DB::commit(); 

            return $this->redirectSuccess('Student promoted successfully!', 'promotestudent.index');

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
