<?php

namespace App\Services\Fees;

use App\Models\FeeLog;
use App\Models\FeesType;
use Illuminate\Pagination\LengthAwarePaginator;

class FeeTypeService
{
    public function index($request): LengthAwarePaginator
    {
        $feetype = FeesType::where('IsActive', 1)->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $feetype->where(function ($q) use ($request) {
                $q->where('FeeName', 'like', '%'.$request->search.'%')
                    ->orWhere('FeeCycle', 'like', '%'.$request->search.'%')
                    ->orWhere('FeesCode', 'like', '%'.$request->search.'%');
            });
        }

        return $feetype->paginate(25)->withQueryString();
    }

    public function submit($request): void
    {
        $validated = $request->validated();
        $created = FeesType::create([
            ...$validated,
            'CreatedBy' => auth()->user()->id,
        ]);

        if ($created) {
            userActivityLogs('Fee Type Created and By User ID: '.auth()->user()->id.'', FeeLog::class);
        }
    }

    public function update($request): void
    {
        $feetype = FeesType::where('id', $request->id)->first();
        $validated = $request->validated();
        $created = $feetype->update([
            ...$validated,
            'CreatedBy' => auth()->user()->id,
        ]);

        if ($created) {
            userActivityLogs('Fee Type Updated and id is '.$request->id.' By User ID: '.auth()->user()->id.'', FeeLog::class);
        }
    }
}
