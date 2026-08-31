<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class GalleryPhotoResource extends JsonResource
{
    /**
     * Transform the gallery photo into a public API payload.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'gallery_id' => Hashids::encode($this->gallery_id),
            'photo' => $this->photo,
            'photo_url' => $this->photo ? asset('storage/'.$this->photo) : null,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
