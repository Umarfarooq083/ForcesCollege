<?php

namespace App\Http\Controllers\UploadContent;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContentGroupRequest;
use App\Models\ContentUpload;
use App\Models\FeeLog;
use App\Models\UploadContentGroup;
use App\Services\ContentGroupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UploadContentGroupController extends Controller
{
    protected $ContentGroupService;

    public function __construct(ContentGroupService $ContentGroupService)
    {
        $this->ContentGroupService = $ContentGroupService;
    }

    public function index(): Response
    {
        $UploadContentGroup = UploadContentGroup::orderBy('id', 'desc')->paginate(25)->withQueryString();

        return Inertia::render('ContentUploadGroup/List', [
            'UploadContentGroup' => $UploadContentGroup,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ContentUploadGroup/Create');
    }

    public function submit(ContentGroupRequest $request): RedirectResponse
    {
        $this->ContentGroupService->submit($request);

        return $this->redirectSuccess('Content group created successfully!', 'uploads.index');
    }

    public function edit(Request $request): Response
    {
        $UploadContentGroup = $this->ContentGroupService->edit($request);

        return Inertia::render('ContentUploadGroup/Edit', [
            'UploadContentGroup' => $UploadContentGroup,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->ContentGroupService->update($request);

        return $this->redirectSuccess('Content group updated successfully!', 'uploads.index');
    }

    public function delete(Request $request)
    {
        $ContentUpload = ContentUpload::where('UploadContentGroupId', $request->id)->exists();
        if ($ContentUpload) {
            return $this->redirectError('Content exists for this group please delete content first', 'content.index');
        }

        $group = UploadContentGroup::findorFail($request->id);
        $deleted = $group->delete();

        if ($deleted) {
            userActivityLogs('Content Group Deleted and id is '.$request->id.' By User ID: '.auth()->user()->id.'', FeeLog::class);

            return $this->redirectSuccess('Content group delted successfully', 'content.index');
        }
    }
}
