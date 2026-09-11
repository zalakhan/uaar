<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class TenderResource extends JsonResource
{
    /**
     * Transform the tender into a public API payload.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'category' => $this->category,
            'title' => $this->title,
            'tender_no' => $this->tender_no,
            'description' => $this->description,
            'uploaded_date' => $this->uploaded_date?->toDateString(),
            'due_date' => $this->due_date?->toDateString(),
            'tender_file' => $this->tender_file,
            'tender_file_url' => $this->tender_file ? asset('storage/'.$this->tender_file) : null,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
