<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\CampusPublication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait StoresCampusPublicationFiles
{
    /**
     * Store a validated PDF outside the public web root.
     *
     * @return array{file_path: string, original_filename: string}
     */
    protected function storeCampusPublicationFile(UploadedFile $file): array
    {
        if ($file->getMimeType() !== 'application/pdf') {
            throw ValidationException::withMessages([
                'file' => 'The file must be a valid PDF document.',
            ]);
        }

        $storedName = Str::random(40).'.pdf';

        Storage::disk('local')->putFileAs('publications', $file, $storedName);

        return [
            'file_path' => $storedName,
            'original_filename' => basename($file->getClientOriginalName()),
        ];
    }

    /**
     * Delete a stored publication file if it exists.
     */
    protected function deleteCampusPublicationFile(?string $filePath): void
    {
        if (! $filePath || ! CampusPublication::isSafeStoredFilename($filePath)) {
            return;
        }

        Storage::disk('local')->delete('publications/'.$filePath);
    }
}
