<?php

namespace App\Services;

use App\Models\LmsSession;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ZoneService
{
    public function index()
    {
        $zones = Zone::query();

        return $zones = $zones->latest()->get();
    }

    public function getActiveZones()
    {
        return Zone::select('id', 'name')->where('status', '1')->get();
    }

    public function submit($request): void
    {
        Zone::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

    }

    public function toggleStatus($request, $id)
    {
        $LmsSession = LmsSession::where('status', '1')->where('zoneid', $id)->first();
        // dd($LmsSession,$request->status,$id);
        if ($request->status == false && $LmsSession) {
            throw ValidationException::withMessages([
                'activeSessionFound' => 'Cannot deactivate zone with active sessions. ',
            ]);
        }
        $zone = Zone::findOrFail($id);
        $zone->status = $request->status;
        $zone->modified_by = Auth::id();
        $zone->save();
    }
}
