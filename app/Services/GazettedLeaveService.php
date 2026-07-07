<?php

namespace App\Services;

use App\Models\GazettedLeave;
use App\Models\HumanResourceLog;

class GazettedLeaveService
{
    public function index(): object
    {
        return GazettedLeave::where('tenant_id', tenant('id'))
            ->orderBy('id', 'desc')
            ->paginate(25)->withQueryString();
    }

    public function submit($request): void
    {
        $validated = $request->validated();

        $created = GazettedLeave::create([
            'tenant_id' => tenant('id'),
            'title' => $validated['title'],
            'date' => $validated['date'],
            'status' => 1,
            'CreatedBy' => auth()->user()->id,
        ]);

        if ($created) {
            userActivityLogs('Gazetted Leave Created '.$created->id.' and By User ID: ' . auth()->user()->id . '', HumanResourceLog::class);
        }
    }

    public function update($request): void
    {
        $gazettedLeave = GazettedLeave::findOrFail($request->id);
        $validated = $request->validated();

        $updated = $gazettedLeave->update([
            'title' => $validated['title'],
            'date' => $validated['date'],
            'status' => 1,
            'ModifiedBy' => auth()->user()->id,
        ]);

        if ($updated) {
            userActivityLogs('Gazetted Leave Updated '. $request->id .' and By User ID: ' . auth()->user()->id . '', HumanResourceLog::class);
        }
    }

    public function destroy($request): void
    {
        $gazettedLeave = GazettedLeave::findOrFail($request->id);

        $deleted = $gazettedLeave->delete();

        if ($deleted) {
            userActivityLogs('Gazetted Leave Deleted and id is '.$request->id.' By User ID: ' . auth()->user()->id . '', HumanResourceLog::class);
        }
    }
}