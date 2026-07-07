<?php

namespace App\Services;

use App\Jobs\ProcessContentUpload;
use App\Models\Campus;
use App\Models\Classes;
use App\Models\ContentUpload;
use App\Models\DownloadContentLog;
use App\Models\Region;
use App\Models\UploadCampusContent;
use App\Models\UploadContentGroup;
use App\Models\UploadContentRegion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadContentService
{
public function Approval($request = null)
     {
         $data['contentUpload'] = '';
         $contentUpload = ContentUpload::query();
         $contentUpload->orderBy('id', 'desc');

         $perPage = (int) ($request->per_page ?? 25);

         if ($request && $request->has('group') && $request->group) {
             $contentUpload->where('UploadContentGroupId', $request->group);
         }

         if ($request->filled('title')) {
             $contentUpload->where('ContentTitle', 'like', '%' . $request->title . '%');
         }

         if ($request->filled('content_type')) {
             $contentUpload->where('ContentType', $request->content_type);
         }

         if ($request->filled('class_id')) {
             $contentUpload->where('ClassId', $request->class_id);
         }

         if ($request->filled('subject_id')) {
             $contentUpload->where('subjectId', $request->subject_id);
         }

         if ($request->filled('tenant_id')) {
             $contentUpload->where('tenant_id', $request->tenant_id);
         }

         if ($request->filled('date_from') && $request->filled('date_to')) {
             $contentUpload->whereDate('UploadDate', '>=', $request->date_from)
                 ->whereDate('UploadDate', '<=', $request->date_to);
         } elseif ($request->filled('date_from')) {
             $contentUpload->whereDate('UploadDate', '>=', $request->date_from);
         } elseif ($request->filled('date_to')) {
             $contentUpload->whereDate('UploadDate', '<=', $request->date_to);
         }

         if (getTenantSubDomain() == 'headoffice') {
             $contentUpload = $contentUpload
                 ->select('id', 'ContentTitle', 'ContentType', 'ClassId', 'subjectId', 'ContentFilePath', 'UploadContentGroupId', 'IsActive', 'tenant_id')
                 ->with('Classes','Subjects', 'ContentGroup')
                 ->orderBy('id', 'desc')
                 ->paginate($perPage)->withQueryString();

             $data['contentUpload'] = $contentUpload;
         } else {
             $Campus = Campus::where('tenant_id', tenant('id'))->firstOrFail();

             $contentUpload = $contentUpload
                 ->with(['Classes','Subjects', 'ContentGroup', 'ContentRegion' => function ($q) use ($Campus) {
                     $q->where('region_id', $Campus->regionid);
                 }])
                 ->whereHas('ContentRegion', function ($q) use ($Campus) {
                     $q->where('region_id', $Campus->regionid);
                 })
                 ->orderBy('id', 'desc')
                 ->paginate($perPage)->withQueryString();

             $data['contentUpload'] = $contentUpload;
         }

         $data['groups'] = UploadContentGroup::orderBy('id', 'desc')->get();
         $data['classesList'] = Classes::select('id', 'ClassName')->get();
         $data['campusList'] = Campus::select('id', 'tenant_id', 'SchoolName')
             ->orderBy('SchoolName')
             ->get();

         return $data;
     }

    public function Approve($request = null)
    {
        return ContentUpload::where('id', $request->id)->update([
            'IsActive' => $request->status,
        ]);
    }

    public function specialistList($request = null)
    {
        $UploadContentGroup = UploadContentGroup::orderBy('id', 'desc')->get();
        $data['groups'] = $UploadContentGroup;
        $contentUpload = ContentUpload::query();
        $contentUpload->where('CreatedBy', auth()->user()->id);
        $contentUpload->orderBy('id', 'desc');
        if ($request && $request->has('group') && $request->group) {
            $contentUpload->where('UploadContentGroupId', $request->group);
        }

        if (getTenantSubDomain() == 'headoffice') {
            $contentUpload = $contentUpload
                ->select('id', 'ContentTitle', 'ContentType', 'ClassId', 'subjectId', 'ContentFilePath', 'UploadContentGroupId', 'IsActive')
                ->with('Classes','Subjects', 'ContentGroup')
                ->orderBy('id', 'desc')
                ->paginate(25)->withQueryString();

            $data['contentUpload'] = $contentUpload;
        }

        return $data;
    }

    public function index($request = null)
    {
        $UploadContentGroup = UploadContentGroup::orderBy('id', 'desc')->get();
        $data['groups'] = $UploadContentGroup;

        $perPage = (int) ($request->per_page ?? 25);

        if (getTenantSubDomain() == 'headoffice') {
            $contentUpload = ContentUpload::query()
                ->with('Classes','Subjects', 'ContentGroup', 'ContentRegion.RegiondRel')
                ->orderByDesc('id');

            // Filter: group
            if ($request && $request->has('group') && $request->group) {
                $contentUpload->where('UploadContentGroupId', $request->group);
            }

            // Filter: title
            if ($request->filled('title')) {
                $contentUpload->where('ContentTitle', 'like', '%' . $request->title . '%');
            }

            // Filter: content type
            if ($request->filled('content_type')) {
                $contentUpload->where('ContentType', $request->content_type);
            }

            // Filter: class
            if ($request->filled('class_id')) {
                $contentUpload->where('ClassId', $request->class_id);
            }

            // Filter: subject
            if ($request->filled('subject_id')) {
                $contentUpload->where('subjectId', $request->subject_id);
            }

            // Filter: campus (tenant_id)
            if ($request->filled('tenant_id')) {
                $contentUpload->where('tenant_id', $request->tenant_id);
            }

            // Filter: date range (UploadDate)
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $contentUpload->whereDate('UploadDate', '>=', $request->date_from)
                    ->whereDate('UploadDate', '<=', $request->date_to);
            } elseif ($request->filled('date_from')) {
                $contentUpload->whereDate('UploadDate', '>=', $request->date_from);
            } elseif ($request->filled('date_to')) {
                $contentUpload->whereDate('UploadDate', '<=', $request->date_to);
            }

            $data['contentUpload'] = $contentUpload->paginate($perPage)->withQueryString();
        } else {
            $Campus = Campus::where('tenant_id', tenant('id'))->firstOrFail();

            $contentUpload = ContentUpload::query()
                ->with('Classes','Subjects', 'ContentGroup', 'ContentRegion.RegiondRel')
                ->whereHas('ContentRegion', function ($q) use ($Campus) {
                    $q->where('region_id', $Campus->regionid);
                })
                ->with(['ContentRegion' => function ($q) use ($Campus) {
                    $q->where('region_id', $Campus->regionid);
                }])
                ->orderByDesc('id');

            // Filter: group
            if ($request && $request->has('group') && $request->group) {
                $contentUpload->where('UploadContentGroupId', $request->group);
            }

            // Filter: title
            if ($request->filled('title')) {
                $contentUpload->where('ContentTitle', 'like', '%' . $request->title . '%');
            }

            // Filter: content type
            if ($request->filled('content_type')) {
                $contentUpload->where('ContentType', $request->content_type);
            }

            // Filter: class
            if ($request->filled('class_id')) {
                $contentUpload->where('ClassId', $request->class_id);
            }

            // Filter: subject
            if ($request->filled('subject_id')) {
                $contentUpload->where('subjectId', $request->subject_id);
            }

            // Filter: date range (UploadDate)
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $contentUpload->whereDate('UploadDate', '>=', $request->date_from)
                    ->whereDate('UploadDate', '<=', $request->date_to);
            } elseif ($request->filled('date_from')) {
                $contentUpload->whereDate('UploadDate', '>=', $request->date_from);
            } elseif ($request->filled('date_to')) {
                $contentUpload->whereDate('UploadDate', '<=', $request->date_to);
            }

            $contentUpload->where('IsActive', 2);

            $data['contentUpload'] = $contentUpload->paginate($perPage)->withQueryString();
        }

        // ── Shared look-up data ─────────────────────────────────────────────────
        $data['classesList'] = Classes::select('id', 'ClassName')->get();
        $data['campusList'] = Campus::select('id', 'tenant_id', 'SchoolName')
            ->orderBy('SchoolName')
            ->get();

        return $data;
    }

    public function create()
    {
        $data['classesList'] = Classes::select('id', 'ClassName')->get();
        $data['region'] = Region::get();
        $data['uploadContentGroupList'] = UploadContentGroup::select('id', 'name')->get();
        $data['campusList'] = Campus::get();

        return $data;
    }

    public function submit($request)
    {

        $user_id = auth()->user()->id;
        ini_set('memory_limit', '1024M');
        $validated = $request->validated();

        $file = $request->file('ContentFilePath');
        if (! $file) {
            throw new \InvalidArgumentException('No file provided.');
        }
        $fileName = $file->getClientOriginalName();
        $fileNameOnly = pathinfo($fileName, PATHINFO_FILENAME);
        $ext = $file->extension();

        $uniquePrefix = time().'_'.uniqid();
        $safeBaseName = Str::of($fileNameOnly)->replaceMatches('/[\\\\\\/\\x00-\\x1F\\x7F]+/', ' ')->trim();
        $safeBaseName = $safeBaseName === '' ? 'file' : (string) $safeBaseName;
        $originalName = $uniquePrefix.'_'.$safeBaseName.'.'.$ext;

        $year = now()->format('Y');
        $month = strtolower(now()->format('F'));
        $newPath = "uploads/contentupload/{$year}/{$month}/{$originalName}";

        $disk = (string) config('filesystems.content_upload_disk', 'public');
        $tempPrefix = trim((string) config('filesystems.content_upload_temp_prefix', 'uploads/temp/contentupload'), '/');
        $tenantPart = tenant('id') ? ('tenant_'.tenant('id')) : 'tenant_unknown';
        $tempPath = $tempPrefix.'/'.$tenantPart.'/'.$originalName;

        $upload = ContentUpload::create([
            ...$validated,
            'ContentFilePath' => $newPath,
            'IsActive' => 0,
            'CreatedBy' => $user_id,
        ]);

        $insertRegionContentUpload = [];
        foreach ($request->regionId as $key => $regionlist) {
            $insertRegionContentUpload[$key]['region_id'] = $regionlist['id'];
            $insertRegionContentUpload[$key]['upload_content_id'] = $upload->id;
            $insertRegionContentUpload[$key]['created_at'] = now();
            $insertRegionContentUpload[$key]['updated_at'] = now();
        }
        UploadContentRegion::insert($insertRegionContentUpload);

        // if ($request->AvailableForAllCampuses == 0) {
        //     $AllowedSchools = collect($request->AllowedSchools);
        //     $campusIds = $AllowedSchools->pluck('id')->toArray();
        //     $campusList = Campus::select('id', 'DomainName', 'tenant_id')
        //         ->whereIn('id', $campusIds)->get();
        //     $insertCampusContentUpload = [];
        //     foreach ($campusList as $key => $list) {
        //         $insertCampusContentUpload[$key]['tenant_id'] = $list->tenant_id;
        //         $insertCampusContentUpload[$key]['campus_id'] = $list->id;
        //         $insertCampusContentUpload[$key]['upload_content_id'] = $upload->id;
        //         $insertCampusContentUpload[$key]['created_at'] = now();
        //         $insertCampusContentUpload[$key]['updated_at'] = now();
        //     }
        //     UploadCampusContent::insert($insertCampusContentUpload);
        // }

        try {
            // Store the temp file on the configured disk so queue workers on other servers can access it.
            Storage::disk($disk)->makeDirectory(dirname($tempPath));
            $stored = Storage::disk($disk)->putFileAs(dirname($tempPath), $file, basename($tempPath));
            if (! $stored) {
                throw new \RuntimeException('Failed to store uploaded file.');
            }
        } catch (\Throwable $e) {
            \Log::error('Content upload: failed to store temp file', [
                'tenant_id' => tenant('id'),
                'upload_content_id' => $upload->id,
                'disk' => $disk,
                'temp_path' => $tempPath,
                'final_path' => $newPath,
                'error' => $e->getMessage(),
            ]);
            $upload->delete();
            throw $e;
        }

        try {
            // On local/dev it's common to not run a queue worker; process synchronously to avoid "stuck uploading".
            if (app()->environment('local')) {
                ProcessContentUpload::dispatchSync($upload, $disk, $tempPath, $newPath, $user_id, tenant('id'));
            } else {
                ProcessContentUpload::dispatch($upload, $disk, $tempPath, $newPath, $user_id, tenant('id'));
            }
        } catch (\Throwable $e) {
            \Log::error('Content upload: failed to dispatch processing job', [
                'tenant_id' => tenant('id'),
                'upload_content_id' => $upload->id,
                'disk' => $disk,
                'temp_path' => $tempPath,
                'final_path' => $newPath,
                'error' => $e->getMessage(),
            ]);
            Storage::disk($disk)->delete($tempPath);
            $upload->delete();
            throw $e;
        }
    }

    public function edit($request)
    {
        $content = ContentUpload::with('ContentRegion.RegiondRel')->findOrFail($request->id);

        if (getTenantSubDomain() !== 'headoffice' && (int) $content->CreatedBy !== (int) auth()->user()->id) {
            abort(403, 'You are not allowed to edit this content.');
        }

        $data = $this->create();
        $data['content'] = $content->only([
            'id',
            'ContentTitle',
            'ContentType',
            'ClassId',
            'subjectId',
            'termId',
            'monthId',
            'weekId',
            'UploadDate',
            'Description',
            'UploadContentGroupId',
            'ContentFilePath',
            'IsActive',
        ]);

        $data['selectedRegions'] = $content->ContentRegion
            ->map(function ($row) {
                return [
                    'id' => $row->region_id,
                    'name' => optional($row->RegiondRel)->name,
                ];
            })
            ->filter(fn ($r) => $r['id'] !== null)
            ->values();

        return $data;
    }

    public function update($request)
    {
        $userId = auth()->user()->id;
        ini_set('memory_limit', '1024M');

        $validated = $request->validated();

        $content = ContentUpload::with('ContentRegion')->findOrFail($validated['id']);

        if (getTenantSubDomain() !== 'headoffice' && (int) $content->CreatedBy !== (int) $userId) {
            abort(403, 'You are not allowed to edit this content.');
        }

        if ((int) $content->IsActive === 0) {
            abort(409, 'Content is still uploading/processing. Please try again in a moment.');
        }

        $regions = $validated['regionId'] ?? [];
        unset($validated['regionId'], $validated['ContentFilePath'], $validated['id']);

        $content->update([
            ...$validated,
            'ModifiedBy' => $userId,
        ]);

        UploadContentRegion::where('upload_content_id', $content->id)->delete();
        $insertRegionContentUpload = [];
        foreach ($regions as $key => $regionItem) {
            $insertRegionContentUpload[$key]['region_id'] = $regionItem['id'] ?? $regionItem;
            $insertRegionContentUpload[$key]['upload_content_id'] = $content->id;
            $insertRegionContentUpload[$key]['created_at'] = now();
            $insertRegionContentUpload[$key]['updated_at'] = now();
        }
        if (! empty($insertRegionContentUpload)) {
            UploadContentRegion::insert($insertRegionContentUpload);
        }

        $file = $request->file('ContentFilePath');
        if (! $file) {
            return;
        }

        $oldPath = $content->ContentFilePath;

        $fileName = $file->getClientOriginalName();
        $fileNameOnly = pathinfo($fileName, PATHINFO_FILENAME);
        $ext = $file->extension();

        $uniquePrefix = time().'_'.uniqid();
        $safeBaseName = Str::of($fileNameOnly)->replaceMatches('/[\\\\\\/\\x00-\\x1F\\x7F]+/', ' ')->trim();
        $safeBaseName = $safeBaseName === '' ? 'file' : (string) $safeBaseName;
        $originalName = $uniquePrefix.'_'.$safeBaseName.'.'.$ext;

        $year = now()->format('Y');
        $month = strtolower(now()->format('F'));
        $newPath = "uploads/contentupload/{$year}/{$month}/{$originalName}";

        $disk = (string) config('filesystems.content_upload_disk', 'public');
        $tempPrefix = trim((string) config('filesystems.content_upload_temp_prefix', 'uploads/temp/contentupload'), '/');
        $tenantPart = tenant('id') ? ('tenant_'.tenant('id')) : 'tenant_unknown';
        $tempPath = $tempPrefix.'/'.$tenantPart.'/'.$originalName;

        $content->update([
            'ContentFilePath' => $newPath,
            'IsActive' => 0,
            'ModifiedBy' => $userId,
        ]);

        try {
            Storage::disk($disk)->makeDirectory(dirname($tempPath));
            $stored = Storage::disk($disk)->putFileAs(dirname($tempPath), $file, basename($tempPath));
            if (! $stored) {
                throw new \RuntimeException('Failed to store uploaded file.');
            }
        } catch (\Throwable $e) {
            \Log::error('Content update: failed to store temp file', [
                'tenant_id' => tenant('id'),
                'upload_content_id' => $content->id,
                'disk' => $disk,
                'temp_path' => $tempPath,
                'final_path' => $newPath,
                'error' => $e->getMessage(),
            ]);
            $content->update([
                'ContentFilePath' => $oldPath,
                'IsActive' => 1,
            ]);
            throw $e;
        }

        try {
            if (app()->environment('local')) {
                ProcessContentUpload::dispatchSync($content, $disk, $tempPath, $newPath, $userId, tenant('id'), $oldPath);
            } else {
                ProcessContentUpload::dispatch($content, $disk, $tempPath, $newPath, $userId, tenant('id'), $oldPath);
            }
        } catch (\Throwable $e) {
            \Log::error('Content update: failed to dispatch processing job', [
                'tenant_id' => tenant('id'),
                'upload_content_id' => $content->id,
                'disk' => $disk,
                'temp_path' => $tempPath,
                'final_path' => $newPath,
                'error' => $e->getMessage(),
            ]);
            Storage::disk($disk)->delete($tempPath);
            $content->update([
                'ContentFilePath' => $oldPath,
                'IsActive' => 1,
            ]);
            throw $e;
        }
    }

    public function download($request)
    {
        $content = ContentUpload::findOrFail($request->id);
        if ($content) {
            $downloadLog = new DownloadContentLog;
            $downloadLog->tenant_id = tenant('id');
            $downloadLog->user_id = auth()->user()->id;
            $downloadLog->domainName = getTenantSubDomain();
            $downloadLog->upload_content_id = $request->id;
            $downloadLog->save();

            $filePath = ltrim((string) $content->ContentFilePath, '/');

            if ((int) $content->IsActive === 0) {
                abort(409, 'File is still uploading/processing. Please try again in a moment.');
            }

            $primaryDisk = (string) config('filesystems.content_upload_disk', 'public');

            // Backward/forward compatibility:
            // - older uploads may exist on `public` or `local`
            $candidateDisks = array_values(array_unique([$primaryDisk, 'public', 'local']));

            foreach ($candidateDisks as $disk) {
                if (! Storage::disk($disk)->exists($filePath)) {
                    continue;
                }

                $downloadName = basename($filePath);

                return Storage::disk($disk)->download($filePath, $downloadName);
            }

            \Log::warning('Content download: file missing on all disks', [
                'tenant_id' => tenant('id'),
                'upload_content_id' => (int) $request->id,
                'path' => $filePath,
                'checked_disks' => $candidateDisks,
            ]);

            abort(404, 'File not found.');
        }
    }

    public function downloadedLogs($request)
    {
        // dd($request->all());
        $tenant = tenant()->domain;
        if ($tenant == 'headoffice') {
            $query = DownloadContentLog::query()
                ->with('user:id,name,tenant_id,email', 'uploadContent:id,ContentTitle,ContentType');

            if ($request->filled('campus_wise')) {
                $query->where('tenant_id', $request->campus_wise);
            }

            if ($request->filled('title')) {
                $query->whereHas('uploadContent', function ($q) use ($request) {
                    $q->where('ContentTitle', 'like', '%'.$request->title.'%');
                });
            }

            $hasRange = $request->filled('date_from') || $request->filled('date_to');

            if ($hasRange) {
                if ($request->filled('date_from')) {
                    $query->whereDate('created_at', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $query->whereDate('created_at', '<=', $request->date_to);
                }
            } elseif ($request->filled('date')) {
                $query->whereDate('created_at', $request->date);
            }

            $campuses = Campus::select('id', 'tenant_id', 'SchoolName')
                ->orderBy('SchoolName')
                ->get();

            return [
                'logs' => $query->orderByDesc('id')->paginate((int) ($request->per_page ?? 25))->withQueryString(),
                'campuses' => $campuses,
            ];
        }

        return [
            'logs' => collect([]),
            'campuses' => collect([]),
        ];
    }

    public function exportDownloadedLogs(array $filters)
    {
        $tenant = tenant()->domain;
        if ($tenant == 'headoffice') {
            $export = new \App\Exports\DownloadedLogsExport($filters);

            return $export->collection();
        }

        return collect();
    }
}
