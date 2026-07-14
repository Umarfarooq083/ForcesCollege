<?php

namespace App\Http\Controllers\UploadContent;

use App\Exports\DownloadedLogsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadContent\UpdateUploadContentRequest;
use App\Http\Requests\UploadContent\UploadContentRequest;
use App\Models\ContentUpload;
use App\Models\Subject;
use App\Services\UploadContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ContentUploadController extends Controller
{
    protected $uploadContentService;

    public function __construct(UploadContentService $uploadContentService)
    {
        $this->uploadContentService = $uploadContentService;
    }

    public function specialistList(Request $request): Response
    {
        $contentUpload = $this->uploadContentService->specialistList($request);

        return Inertia::render('UploadContent/SpecialistList', [
            'contentUpload' => $contentUpload['contentUpload'],
            'groups' => $contentUpload['groups'],
            'selectedGroup' => $request->group, // Pass the selected group to frontend
        ]);
    }

    public function index(Request $request): Response
    {
        $contentUpload = $this->uploadContentService->index($request);

        return Inertia::render('UploadContent/List', [
            'contentUpload' => $contentUpload['contentUpload'],
            'groups' => $contentUpload['groups'],
            'selectedGroup' => $request->group,
            'Campuses' => $contentUpload['campusList'],
            'classesList' => $contentUpload['classesList'],
        ]);
    }

    public function Approval(Request $request)
    {
        $contentUpload = $this->uploadContentService->Approval($request);

        return Inertia::render('UploadContent/Approval', [
            'contentUpload' => $contentUpload['contentUpload'],
            'groups' => $contentUpload['groups'],
            'selectedGroup' => $request->group,
            'Campuses' => $contentUpload['campusList'],
            'classesList' => $contentUpload['classesList'],
        ]);
    }

    public function Approve(Request $request)
    {
        $this->uploadContentService->Approve($request);

        return redirect()->back()->with('receipt', 'Content status updated successfully!');

        // return $this->redirectSuccess('Status updated successfully!', 'uploads.approval');
    }

    public function create(): Response
    {
        $data = $this->uploadContentService->create();

        return Inertia::render('UploadContent/Create', [
            'classesList' => $data['classesList'],
            'uploadContentGroupList' => $data['uploadContentGroupList'],
            'campusList' => $data['campusList'],
            'region' => $data['region'],
        ]);
    }

    public function submit(UploadContentRequest $request): RedirectResponse
    {
        $request->validate([
            'ContentFilePath' => 'required|file|max:512000',
        ]);
        $this->uploadContentService->submit($request);

        return $this->redirectSuccess('File upload in progress. You may continue working on other things.', 'uploads.specialistlist');
    }

    public function edit(Request $request): Response
    {
        $data = $this->uploadContentService->edit($request);

        return Inertia::render('UploadContent/Edit', [
            'classesList' => $data['classesList'],
            'uploadContentGroupList' => $data['uploadContentGroupList'],
            'campusList' => $data['campusList'],
            'region' => $data['region'],
            'content' => $data['content'],
            'selectedRegions' => $data['selectedRegions'],
        ]);
    }

    public function update(UpdateUploadContentRequest $request): RedirectResponse
    {
        $request->validate([
            'ContentFilePath' => 'nullable|file|max:512000',
        ]);

        $this->uploadContentService->update($request);

        return $this->redirectSuccess('Content updated successfully!', 'uploads.index');
    }

    public function classSubjectFetch(Request $request): Collection|Subject
    {
        return Subject::select('id', 'SubjectName', 'ClassId')->ClassId($request->class_id)->get();
    }

    public function download(Request $request)
    {
        return $this->uploadContentService->download($request);
    }

    public function delete(Request $request): RedirectResponse
    {

        $contentUpload = ContentUpload::query();
        $contentUpload->where('id', $request->id)->update([
            'ModifiedBy' => auth()->user()->id,
        ]);
        ContentUpload::findorFail($request->id)->delete();

        return $this->redirectSuccess('Content deleted successfully!', 'uploads.index');
    }

    public function downloadedLogs(Request $request): Response
    {
        $result = $this->uploadContentService->downloadedLogs($request);

        return Inertia::render('ContentUploadGroup/DownloadedLogs', [
            'DownloadedLogs' => $result['logs'],
            'Campuses' => $result['campuses'],
        ]);
    }

    public function exportDownloadedLogs(Request $request)
    {
        $filters = [
            'campus_wise' => $request->input('campus_wise'),
            'title' => $request->input('title'),
            'date' => $request->input('date'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'selected_ids' => $request->input('selected_ids'),
        ];

        $filters = array_filter($filters, fn ($v) => $v !== '' && $v !== null);

        if (isset($filters['selected_ids'])) {
            $filters['selected_ids'] = array_map('intval', $filters['selected_ids']);
        }

        return Excel::download(new DownloadedLogsExport($filters), 'downloaded-logs.xlsx');
    }
}
