<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class GalleryResource extends JsonResource
{
    /**
     * Transform the gallery into a public API payload.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'name' => $this->name,
            'date' => $this->date?->toDateString(),
            'department_id' => $this->department_id,
            'status' => (bool) $this->status,
            'slug' => $this->slug,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'thumbnail_url' => $this->when(
                $this->relationLoaded('thumbnail'),
                fn () => $this->thumbnail?->photo ? asset('storage/'.$this->thumbnail->photo) : null
            ),
            'photos' => GalleryPhotoResource::collection($this->whenLoaded('photos')),
        ];
    }
}
