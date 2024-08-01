<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EditionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name_edition' => $this->name_edition,
            'volume' => $this->volume,
            'issue' => $this->issue,
            'description' => $this->description,
            'status' => $this->status,
            'slug' => $this->slug,
            'year' => $this->year,
            'signed_edition_image' => $this->signed_edition_image,
            'publish_date_formatted'=> $this->publish_date_formatted
        ];
    }
}
