<?php

namespace App\Services;

use App\Models\DownloadContentLog;
use App\Models\UploadContentGroup;

class ContentGroupService
{
    public function submit($request): void
    {
        $validated = $request->validated();
        UploadContentGroup::create([
            ...$validated,
            'Category' => 'Academics',
            'CategoryId' => '1',
            'CreatedBy' => auth()->user()->id,
        ]);
    }

    public function edit($request)
    {
        return UploadContentGroup::findOrFail($request->id);
    }
    
    public function update($request):void
    {
        $UploadContentGroup = UploadContentGroup::findOrFail($request->id);
        $UploadContentGroup->name = $request->name;
        $UploadContentGroup->save();
    }

}