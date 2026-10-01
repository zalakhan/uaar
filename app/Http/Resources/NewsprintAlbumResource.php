<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class NewsprintAlbumResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'title' => $this->title,
            'publish_date' => $this->publish_date?->toDateString(),
            'status' => (bool) $this->status,
            'clippings' => NewsPrintResource::collection($this->whenLoaded('newsPrints')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
