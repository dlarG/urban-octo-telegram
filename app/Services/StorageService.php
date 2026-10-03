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
    public function put(UploadedFile $file, string $category): string
    {
        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();

        return $file->storeAs($category, $filename, $this->disk());
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk($this->disk())->exists($path)) {
            Storage::disk($this->disk())->delete($path);
        }
    }

    public function url(?string $path): ?string
    {
        return $path ? Storage::disk($this->disk())->url($path) : null;
    }

    /**
     * One place to change when we migrate to S3/R2.
     */
    protected function disk(): string
    {
        return config('filesystems.default', 'public');
    }
}
