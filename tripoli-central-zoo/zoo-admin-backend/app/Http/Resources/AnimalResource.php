<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalResource extends JsonResource
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
            'name' => $this->name,
            'species' => $this->species,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'location' => [
                'x' => $this->location_x,
                'y' => $this->location_y,
            ],
            'coordinates' => $this->coordinates,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'facts' => $this->facts,
            'status' => $this->status,
            'featured' => $this->featured,
            'scientific_name' => $this->scientific_name,
            'gallery' => $this->gallery,
            'habitat' => $this->habitat,
            'conservation_status' => $this->conservation_status,
            'diet' => $this->diet,
            'age' => $this->age,
            'weight' => $this->weight,
            'size' => $this->size,
            'feeding_times' => $this->feeding_times,
            'display_order' => $this->display_order,
            'opening_hours' => OpeningHourResource::collection($this->whenLoaded('openingHours')),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
