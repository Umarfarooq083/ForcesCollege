<?php

namespace App\Services;

use App\Models\ProgramLevel;

class ProgramLevelService
{
    public function submit($request): void
    {
        $validated = $request->validated();
        ProgramLevel::create([
            ...$validated,
            'tenant_id' => tenant('id'),
            'CreatedBy' => auth()->user()->id,
        ]);
    }

    public function update($request): void
    {
        $programLevel = ProgramLevel::where('id', $request->id)->where('tenant_id', tenant('id'))->firstOrFail();
        $validated = $request->validated();
        $validated['ModifiedBy'] = auth()->user()->id;

        $programLevel->update($validated);
    }

    public function delete($request): void
    {
        $programLevel = ProgramLevel::where('id', $request->id)->where('tenant_id', tenant('id'))->firstOrFail();
        $programLevel->delete();
    }
}