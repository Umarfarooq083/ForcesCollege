<?php

namespace App\Jobs;

use App\Events\FileUploaded;
use App\Models\ContentUpload;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessContentUpload implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $contentUpload;

    public $disk;

    public $tempPath;

    public $newPath;

    public $user_id;

    public $tenantId;

    public $oldPath;

    public function __construct(ContentUpload $contentUpload, $disk, $tempPath, $newPath, $user_id, $tenantId, $oldPath = null)
    {
        $this->contentUpload = $contentUpload;
        $this->disk = $disk;
        $this->tempPath = $tempPath;
        $this->newPath = $newPath;
        $this->user_id = $user_id;
        $this->tenantId = $tenantId;
        $this->oldPath = $oldPath;
    }

    public function handle()
    {
        $tenant = \App\Models\Tenant::find($this->tenantId);
        tenancy()->initialize($tenant);

        $disk = $this->disk ?: (string) config('filesystems.content_upload_disk', 'public');

        try {
            if (! Storage::disk($disk)->exists($this->tempPath)) {
                \Log::error('Content upload: temp file missing', [
                    'tenant_id' => $this->tenantId,
                    'upload_content_id' => $this->contentUpload->id,
                    'disk' => $disk,
                    'temp_path' => $this->tempPath,
                ]);

                return;
            }

            Storage::disk($disk)->makeDirectory(dirname($this->newPath));

            $stream = Storage::disk($disk)->readStream($this->tempPath);
            if ($stream === false) {
                \Log::error('Content upload: failed to open temp stream', [
                    'tenant_id' => $this->tenantId,
                    'upload_content_id' => $this->contentUpload->id,
                    'disk' => $disk,
                    'temp_path' => $this->tempPath,
                ]);

                return;
            }

            try {
                $stored = Storage::disk($disk)->put($this->newPath, $stream);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }

            if (! $stored || ! Storage::disk($disk)->exists($this->newPath)) {
                \Log::error('Content upload: failed to store final file', [
                    'tenant_id' => $this->tenantId,
                    'upload_content_id' => $this->contentUpload->id,
                    'disk' => $disk,
                    'temp_path' => $this->tempPath,
                    'final_path' => $this->newPath,
                    'stored' => (bool) $stored,
                ]);

                return;
            }

            $this->contentUpload->update([
                'IsActive' => 1,
            ]);

            Storage::disk($disk)->delete($this->tempPath);

            if ($this->oldPath) {
                $oldPath = ltrim((string) $this->oldPath, '/');
                if ($oldPath !== '' && $oldPath !== ltrim((string) $this->newPath, '/')) {
                    foreach (array_values(array_unique([$disk, 'public', 'local'])) as $oldDisk) {
                        if (Storage::disk($oldDisk)->exists($oldPath)) {
                            Storage::disk($oldDisk)->delete($oldPath);
                            break;
                        }
                    }
                }
            }

            event(new FileUploaded($this->user_id));
        } finally {
            tenancy()->end();
        }
    }
}
