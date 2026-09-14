<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampusPublication extends Model
{
    public const TYPE_NEWS_LETTER = 'News Letter';

    public const TYPE_CAMPUS_NEWS = 'Campus News';

    protected $fillable = [
        'type',
        'duration',
        'year',
        'file_path',
        'original_filename',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
        ];
    }

    /**
     * Available publication types.
     *
     * @return array<int, string>
     */
    public static function types(): array
    {
        return [
            self::TYPE_NEWS_LETTER,
            self::TYPE_CAMPUS_NEWS,
        ];
    }

    /**
     * Build the whitelisted public URL for the stored PDF file.
     */
    public function fileUrl(): ?string
    {
        if (! $this->file_path || ! self::isSafeStoredFilename($this->file_path)) {
            return null;
        }

        return url('media/publications/'.$this->file_path);
    }

    /**
     * Ensure a stored filename matches the expected secure pattern.
     */
    public static function isSafeStoredFilename(string $filename): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9]{40}\.pdf$/', $filename);
    }
}
