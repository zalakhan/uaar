<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsPrintResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'newspaper' => $this->newspaper?->newspaper_name,
            'newspaper_id' => $this->newspaper_id,
            'news_file_url' => $this->fileUrl(),
        ];
    }
}
