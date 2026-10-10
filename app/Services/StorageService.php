<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageService
{
    /**
     * Store an uploaded file under a category subfolder.
     * Returns the disk-relative path (e.g. "landlord-ids/abc123.jpg").
     */
    public function put(UploadedFile $file, string $category, ?string $disk = null): string
    {
        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();

        return $file->storeAs($category, $filename, $disk ?? $this->disk());
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk($this->disk())->exists($path)) {
            Storage::disk($this->disk())->delete($path);
        }
    }

    public function url(?string $path, ?string $disk = null): ?string
    {
        if (! $path) return null;
        $disk = $disk ?? $this->disk();

        // Private files are served through a route, not Storage::url()
        if ($disk === 'local') {
            return route('admin.documents.show', ['path' => $path]);
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * One place to change when we migrate to S3/R2.
     */
    protected function disk(): string
    {
        $disk = config('filesystems.default', 'public');

        if ($disk === 'local') {
            throw new \RuntimeException(
                'StorageService is using the "local" (private) disk. '.
                'Set FILESYSTEM_DISK=public in .env, or switch to S3/R2.'
            );
        }

        return $disk;
    }
}
