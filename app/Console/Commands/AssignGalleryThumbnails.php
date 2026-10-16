<?php

namespace App\Console\Commands;

use App\Models\Gallery;
use Illuminate\Console\Command;

class AssignGalleryThumbnails extends Command
{
    protected $signature = 'galleries:assign-thumbnails';

    protected $description = 'Set sort_order 0 on the first photo for galleries missing a thumbnail';

    public function handle(): int
    {
        $updated = 0;

        Gallery::query()
            ->whereHas('photos')
            ->whereDoesntHave('photos', fn ($query) => $query->where('sort_order', 0))
            ->orderBy('id')
            ->chunkById(100, function ($galleries) use (&$updated) {
                foreach ($galleries as $gallery) {
                    $firstPhoto = $gallery->photos()->orderBy('sort_order')->orderBy('id')->first();

                    if (! $firstPhoto) {
                        continue;
                    }

                    $firstPhoto->update(['sort_order' => 0]);
                    $updated++;
                }
            });

        $this->info("Assigned thumbnails for {$updated} gallery(ies).");

        return self::SUCCESS;
    }
}
