<?php

namespace App\Services;

use App\Models\Program;

class ProgramService
{
    public function submit($request): void
    {
        $validated = $request->validated();
        Program::create([
            ...$validated,
            'tenant_id' => tenant('id'),
            'CreatedBy' => auth()->user()->id,
        ]);
    }

    public function update($request): void
    {
        $program = Program::find($request->id);
        $program->name = $request->name;
        $program->type = $request->type;
        $program->duration = $request->duration;
        $program->ModifiedBy = auth()->user()->id;

        $program->save();
    }

    public function delete($request): void
    {
        $program = Program::find($request->id);
        $program->delete();
    }
}