<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacilityResource extends JsonResource
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
            'type' => $this->type,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'location' => [
                'x' => $this->location_x,
                'y' => $this->location_y,
            ],
            'coordinates' => $this->coordinates,
            'description' => $this->description,
            'opening_hours' => $this->opening_hours,
            'image_url' => $this->image_url,
            'status' => $this->status,
            'gallery' => $this->gallery,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'amenities' => $this->amenities,
            'is_accessible' => $this->is_accessible,
            'is_open' => $this->is_open,
            'capacity' => $this->capacity,
            'display_order' => $this->display_order,
            'hours' => OpeningHourResource::collection($this->whenLoaded('openingHours')),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
