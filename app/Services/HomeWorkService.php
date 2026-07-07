<?php

namespace App\Services;

use App\Models\HomeWork;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;

class HomeWorkService
{

    public function index($request)
    {
        $home_work =  HomeWork::where('tenant_id', tenant('id'))->with('ClassRel','SectionRel','SubjectRel');
        if ($request->filled('search')) {
            $home_work->where(function($q) use($request){
                $q->where('class', 'like', '%' . $request->search . '%')
                ->orWhere('section', 'like', '%' . $request->search . '%')
                ->orWhere('subject', 'like', '%' . $request->search . '%');
            });
        }
        return $home_work->orderBy('id','desc')->paginate(25)->withQueryString();
    }

    public function create(): array
    {
        $data = classAndSections();
        $classIds = $data['classList']->pluck('id')->toArray();
        $data['subjects'] = Subject::whereIn('ClassId', $classIds)->get();
        return $data;
    }

    public function submit($request)
    {
        $currentSession = fetchCurrentSession();
        if(!$currentSession){
            throw new \Exception('Current Session Not Found');  
        }
        $validated = $request->validated();
        $filePath = null;
        if ($request->hasFile('attachDocumentPath')) {
            $filePath = $request->file('attachDocumentPath')->store('homeworks', 'public');
        }
        $validated['tenant_id'] =  tenant('id');
        $validated['attachDocumentPath'] =  $filePath;
        HomeWork::create([
            ...$validated,
            'sessionId' => $currentSession->id,
            ]);
    }

    public function update($request)
    {
        $validated = $request->validated();
        $home_work = HomeWork::where('id', $request->id)->where('tenant_id', tenant('id'))->firstOrFail();
        $filePath = null;
        if ($request->hasFile('attachDocumentPath')) {
            if ($home_work->attachDocumentPath && Storage::disk('public')->exists($home_work->attachDocumentPath)) {
                Storage::disk('public')->delete($home_work->attachDocumentPath);
            }

            $filePath = $request->file('attachDocumentPath')->store('homeworks', 'public');
            $validated['attachDocumentPath'] = $filePath;
        } else {
            $validated['attachDocumentPath'] = $home_work->attachDocumentPath;
        }
        $home_work->update($validated);
    }
}