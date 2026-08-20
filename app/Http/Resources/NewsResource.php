<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class NewsResource extends JsonResource
{
    /**
     * Transform the news article into a public API payload.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'title' => $this->title,
            'description' => $this->description,
            'published_date' => $this->published_date?->toDateString(),
            'image' => $this->image,
            'image_url' => $this->image ? asset('storage/'.$this->image) : null,
            'slug' => $this->slug,
            'status' => (bool) $this->status,
            'department_id' => $this->department_id,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
