<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'schedule' => $this->schedule,
            'location' => $this->location,
            'type' => $this->type,
            'image_url' => $this->image_url,
            'status' => $this->status,
            'facility_id' => $this->facility_id,
            'facility' => new FacilityResource($this->whenLoaded('facility')),
            'animal_id' => $this->animal_id,
            'animal' => new AnimalResource($this->whenLoaded('animal')),
            'start_time' => $this->start_time?->toISOString(),
            'end_time' => $this->end_time?->toISOString(),
            'recurrence' => $this->recurrence,
            'duration_minutes' => $this->duration_minutes,
            'capacity' => $this->capacity,
            'requires_booking' => $this->requires_booking,
            'price' => $this->price,
            'age_restriction' => $this->age_restriction,
            'display_order' => $this->display_order,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
