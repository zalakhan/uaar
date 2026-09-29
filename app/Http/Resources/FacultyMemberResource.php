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
            'specialization' => $this->specialization,
            'bio' => $this->bio,
            'research_link' => $this->research_link,
            'research_group' => $this->research_group,
            'affiliation' => $this->affiliation,
            'projects_ongoing' => $this->projects_ongoing,
            'projects_completed' => $this->projects_completed,
            'supervision_phd' => $this->supervision_phd,
            'supervision_mphil_ms_msc' => $this->supervision_mphil_ms_msc,
            'patent' => $this->patent,
            'consultancy_services' => $this->consultancy_services,
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
            'books' => BookResource::collection($this->whenLoaded('books')),
        ];
    }
}
