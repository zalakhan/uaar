<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class NewsprintAlbum extends Model
{
    protected $table = 'newsprint_album';

    protected $fillable = [
        'title',
        'publish_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'publish_date' => 'date',
            'status' => 'boolean',
        ];
    }

    public function newsPrints(): HasMany
    {
        return $this->hasMany(NewsPrint::class, 'newsprint_album_id');
    }

    /**
     * Delete all clipping files for this album from storage.
     */
    public function deleteStoredFiles(): void
    {
        Storage::disk('public')->deleteDirectory('newsprint/'.$this->id);
    }
}
