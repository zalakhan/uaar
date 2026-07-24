<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class FacultyMemberResource extends JsonResource
{
    /**
     * Transform the faculty member into a public API payload.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'name' => $this->name,
            'designation' => $this->designation?->name,
            'additional_designation' => $this->additionalDesignation?->name,
            'department' => $this->department?->name,
            'additional_department' => $this->additionalDepartment?->name,
            'faculty' => $this->faculty?->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'qualification' => $this->qualification,
            'bio' => $this->bio,
            'address' => $this->address,
            'total_experience' => $this->total_experience,
            'total_publication' => $this->total_publication,
            'is_hec' => $this->is_hec,
            'is_studyleave' => $this->is_studyleave,
            'is_onleave' => $this->is_onleave,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            // 'photo_url' => $this->photo ? asset('storage/'.$this->photo) : null,
            'photo_url' => $this->photo ? url('media/faculty/' . basename($this->photo)) : null,
            'publications' => PublicationResource::collection($this->whenLoaded('publications')),
            'awards' => AwardResource::collection($this->whenLoaded('awards')),
        ];
    }
}
