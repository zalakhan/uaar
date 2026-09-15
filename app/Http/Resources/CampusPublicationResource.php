<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class CampusPublicationResource extends JsonResource
{
    /**
     * Transform the campus publication into a public API payload.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'type' => $this->type,
            'duration' => $this->duration,
            'year' => $this->year,
            'file_url' => $this->fileUrl(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
