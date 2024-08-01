<?php

namespace App\Http\Resources;

use App\Models\AnnouncementCriteria;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'announcement_title' => $this->announcement_title,
            'submission_deadline_date'=>$this->submission_deadline_date,
            'published_date' => $this->published_date,
            'published_date_formatted' => $this->published_date_formatted,
            'submission_deadline_formatted' => $this->submission_deadline_formatted,
            'edition' => new EditionResource($this->edition),
            'date' => $this->created_at_formatted,
            'description' => $this->announcement_description,
            'slug' => $this->slug,
            'criteria' => $this->criteria
        ];
    }
}
