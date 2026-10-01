<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsPrint extends Model
{
    protected $table = 'news_print';

    protected $fillable = [
        'newsprint_album_id',
        'newspaper_id',
        'news_file',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(NewsprintAlbum::class, 'newsprint_album_id');
    }

    public function newspaper(): BelongsTo
    {
        return $this->belongsTo(Newspaper::class);
    }

    /**
     * Full public URL for the clipping image.
     */
    public function fileUrl(): ?string
    {
        return $this->news_file ? asset('storage/'.$this->news_file) : null;
    }
}
