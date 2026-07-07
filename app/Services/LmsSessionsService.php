<?php

namespace App\Services;
use App\Models\LmsSession;
use App\Models\SettingLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LmsSessionsService
{

    public function index($request)
    {
        $lmssessions = LmsSession::query(); 
        if($request->filled('search'))
        {    
            $lmssessions->where('name', 'like', '%'.$request->search.'%');
        }
        // dd($lmssessions->get());
        return $lmssessions = $lmssessions->with('zone')->latest()->get();
    }


    public function submit($request): void
    {
       $overlap = LmsSession::where('zoneid', $request->zoneid)
                ->where('end_date', '>=', $request->start_date)
                ->where('start_date', '<=', $request->end_date)
                ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'start_date' => ['Another session already exists for this zone within the same period.']
            ]);
        }

        $created = LmsSession::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'zoneid' => $request->zoneid,
            'created_by' => Auth::id(),
        ]);

        if($created){
            userActivityLogs('LMS Session Created and By User ID: '.auth()->user()->id.'', SettingLog::class);
        }
    }


    public function update($request, $id): void
    {
        $session = LmsSession::findOrFail($id);

        $overlap = LmsSession::where('id', '!=', $id)
                    ->where('zoneid', $request->zoneid)
                    ->where('end_date', '>=', $request->start_date)
                    ->where('start_date', '<=', $request->end_date)
                    ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'start_date' => ['Another session already exists for this zone within the same period.']
            ]);
        }
        $exists = LmsSession::where('zoneid',$request->zoneid)->exists();
        
        $updated = $session->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'zoneid' => $request->zoneid,
            'status' => $exists ? 0 : 1,
            'modified_by' => Auth::id(),
        ]);

        if($updated){
            userActivityLogs('LMS Session Updated and id is '.$id.' By User ID: '.auth()->user()->id.'', SettingLog::class);
        }
    }


    public function find($id)
    {
        $session = LmsSession::findOrFail($id);
        return [
            'id' => $session->id,
            'name' => $session->name,
            'start_date' => $session->start_date->format('Y-m-d'),
            'end_date' => $session->end_date->format('Y-m-d'),
            'zoneid' => $session->zoneid,
        ];
    }
    

    public function destroy($id): void
    {
        $session = LmsSession::findOrFail($id);
        $session->modified_by = Auth::id();
        $session->save();
        $session->delete();
    }


    public function toggleStatus($request, $id)
    {
        $status = (int) $request->status;

        if (!in_array($status, [0, 1])) {
            throw new \InvalidArgumentException("Invalid status value.");
        }

        $session = LmsSession::findOrFail($id);
        $zoneId = $session->zoneid;

        if ($status === 1) {
            LmsSession::where('zoneid', $zoneId)
                ->where('id', '!=', $session->id)
                ->update(['status' => 0]);

            $session->status = 1;
            $session->modified_by = Auth::id();
            $session->save();

        } 
        else {
            $isOnlyActive = !LmsSession::where('zoneid', $zoneId)
                ->where('id', '!=', $session->id)
                ->where('status', 1)
                ->exists();

            if ($session->status == 1 && $isOnlyActive) {
                throw ValidationException::withMessages([
                    'status' => ['To deactivate this session, please activate another one in the same zone first.']
                ]);
            }

            $session->status = 0;
            $session->modified_by = Auth::id();
            $session->save();
        }
    }



}