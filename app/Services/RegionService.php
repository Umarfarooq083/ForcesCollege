<?php

namespace App\Services;

use App\Models\Region;
use Illuminate\Support\Facades\Auth;

class RegionService
{
    public function index()
    {
        return Region::query()->latest()->get();
    }

    public function getActiveRegions()
    {
        return Region::select('id', 'name')->where('status', 1)->get();
    }

    public function submit($request): void
    {
        Region::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);
    }

    public function toggleStatus($request, $id)
    {
        $region = Region::findOrFail($id);
        $region->status = $request->status;
        $region->modified_by = Auth::id();
        $region->save();
    }
}
