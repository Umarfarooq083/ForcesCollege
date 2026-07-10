<?php

namespace App\Services\Fees;

use App\Models\Campus;
use App\Models\CampusFeesMaster;
use App\Models\Classes;
use App\Models\FeeLog;
use App\Models\FeesType;
use App\Models\LmsSession;
use App\Models\OptionalFeeMaster;
use App\Models\StudentFeeDiscount;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class CampusFeesMasterService
{
    protected $campus_query;
    public function __construct()
    {
        $this->campus_query = CampusFeesMaster::where('tenant_id', tenant('id'))->where('IsActive', 1);
    }

    public function index($request): array
    {
        if(fetchCurrentSession() === null){
            $campusQuery = [];
            $items = collect($campusQuery); 
            $page = request()->get('page', 1);
            $perPage = 25;
            $paginatedData = new LengthAwarePaginator(
                $items->forPage($page, $perPage),
                $items->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            $data['CampusFeesMasterList'] = $paginatedData;
            $data['LmsSession'] = $this->getSessionData();

            return $data;
        }
        $campusQuery = CampusFeesMaster::where('tenant_id', tenant('id'))
            ->where('sessionid', fetchCurrentSession()->id)
            ->whereHas('fee_master_type')
            ->with('fee_master_class:id,ClassName', 'fee_master_type:id,FeesCode,FeeName', 'SessionRel')
            ->where('IsActive', 1)->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $campusQuery->where(function ($query) use ($search) {
                $query->whereHas('fee_master_type', function ($q) use ($search) {
                    $q->where('FeeName', 'like', '%' . $search . '%');
                })
                ->orWhereHas('fee_master_class', function ($q) use ($search) {
                    $q->where('ClassName', 'like', '%' . $search . '%');
                });
            });
        }


        $data['CampusFeesMasterList'] = $campusQuery->paginate(25)->withQueryString();
        $data['LmsSession'] = $this->getSessionData();
        return $data;
    }

    /**
     * Insert new campus fee master records.
     *
     * @param array $validatedData
     * @return void
     */
    public function submit($validatedData): void
    {
        $maxGroupId = DB::table('campus_fees_masters')->max('group_id') ?? 0;
        $insertData = [];
        foreach ($validatedData['fees_master'] as $fee) {
            $insertData[] = [
                'SchoolId'     => null,
                'IsActive'     => true,
                'tenant_id'    => tenant('id'),
                'CreatedBy'    => auth()->id(),
                'ModifiedBy'   => null,
                'SessionId'    => $validatedData['session']['id'],
                'Title'        => null,
                'FeesTypeNId'  => $validatedData['feetypeid'],
                'Amount'       => $fee['amount'],
                'ClassId'      => $fee['class_id'],
                'SectionId'    => null,
                'group_id'     => $maxGroupId + 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }
        $created =  DB::table('campus_fees_masters')->insert($insertData);

        if ($created) {
            userActivityLogs('Campus Fee Master Created and By User ID: '.auth()->user()->id.'', FeeLog::class);
        }
    }

    /**
     * Get fee types, classes and session for form.
     *
     * @return array
     */
    public function getData(): array
    {
        $data['FeeTypes'] = FeesType::get(['id', 'FeeName as name']);
        $data['Classes']  = $this->getClassesList();
        $data['Session']  = $this->getSessionData();
        return $data;
    }


    /**
     * Get existing fee master group data for edit form.
     *
     * @param Request $request
     * @return array
     */
    public function edit($request): array
    {
        $campus_fee_master = $this->campus_query
            ->where('id', $request->campus_fee_master_id)
            ->where('sessionid', fetchCurrentSession()->id)
            ->first();
        $data['campus_fee_master_group'] = CampusFeesMaster::where('tenant_id', tenant('id'))
            ->where('id', $request->campus_fee_master_id)
            ->with('fee_master_class:id,ClassName as name')
            ->where('group_id', $campus_fee_master?->group_id)
            ->with('fee_master_class:id,ClassName', 'fee_master_type:id,FeesCode,FeeName')
            ->get();

        $data['Classes']  = $this->getClassesList();
        $data['fee_type_id'] = $campus_fee_master?->FeesTypeNId;
        $data['SessionId'] = $campus_fee_master?->SessionId;
        return $data;
    }


    /**
     * Upsert fee master records (update or insert).
     *
     * @param Request $request
     * @return array
     */
    public function update($request): array
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $collection = collect($request->fees_master)
            ->map(function ($item) use ($now) {

                unset(
                    $item['fee_master_class'],
                    $item['fee_master_type'],
                    $item['created_at'] // IMPORTANT
                );

                // Force valid MySQL datetime
                $item['updated_at'] = $now;

                return $item;
            })
            ->toArray();

        $this->campus_query
            ->where('FeesTypeNId', $request->fee_type_id)
            ->where('group_id', $request->fee_group_id)
            ->upsert(
                $collection,
                ['id'],
                [
                    'SchoolId',
                    'IsActive',
                    'tenant_id',
                    'CreatedBy',
                    'ModifiedBy',
                    'SessionId',
                    'Title',
                    'FeesTypeNId',
                    'Amount',
                    'ClassId',
                    'SectionId',
                    'deleted_at',
                    'updated_at',
                    'group_id'
                ]
            );

        userActivityLogs('Campus Fee Master Updated and By User ID: '.auth()->user()->id.'', FeeLog::class);

        return [
            'collection' => $collection
        ];
    }

    public function delete($request)
    {
        $optionalFeeMaster = OptionalFeeMaster::where('tenant_id', tenant('id'))
            ->where('CampusFeesMasterId', $request->id)
            ->first();

        $studentFeeDiscount = StudentFeeDiscount::where('tenant_id', tenant('id'))
            ->where('CampusFeesMasterId', $request->id)
            ->first();
        if ($optionalFeeMaster || $studentFeeDiscount) {
            return false;
            // throw new \Exception('Cannot delete fee master as it is associated with optional fees or student discounts.');
        }
        // $request->id;
        $this->campus_query
            ->where('tenant_id', tenant('id'))
            ->where('id', $request->id)
            ->delete();
        
        userActivityLogs('Campus Fee Master Deleted and id is '.$request->id.' By User ID: '.auth()->user()->id.'', FeeLog::class);
        return true;
    }

    protected function getClassesList(): Collection
    {
        return Classes::where('tenant_id', tenant('id'))->where('IsActive', 1)->get(['id', 'ClassName as name', 'tenant_id']);
    }

    protected function getSessionData(): ?LmsSession
    {
        return Campus::where('tenant_id', tenant('id'))->with('zone.session')->first(['id', 'zoneid'])?->zone?->session;
    }
}
