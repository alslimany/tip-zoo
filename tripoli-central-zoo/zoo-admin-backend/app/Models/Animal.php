<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'species',
        'category_id',
        'location_x',
        'location_y',
        'description',
        'image_url',
        'facts',
        'status',
        'featured',
        'scientific_name',
        'gallery',
        'habitat',
        'conservation_status',
        'diet',
        'age',
        'weight',
        'size',
        'feeding_times',
        'display_order',
    ];

    protected $casts = [
        'location_x' => 'decimal:6',
        'location_y' => 'decimal:6',
        'gallery' => 'array',
        'diet' => 'array',
        'feeding_times' => 'array',
        'featured' => 'boolean',
    ];

    protected $attributes = [
        'status' => 'active',
        'featured' => false,
    ];

    /**
     * Get the category that owns the animal.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all of the animal's opening hours.
     */
    public function openingHours(): MorphMany
    {
        return $this->morphMany(OpeningHour::class, 'entity');
    }

    /**
     * Get all of the activities for the animal.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Scope a query to only include active animals.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include featured animals.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to only include inactive animals.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include animals in maintenance.
     */
    public function scopeMaintenance(Builder $query): Builder
    {
        return $query->where('status', 'maintenance');
    }

    /**
     * Get the animal's coordinate as an array.
     */
    public function getCoordinatesAttribute(): ?array
    {
        if ($this->location_x !== null && $this->location_y !== null) {
            return [
                'x' => (float) $this->location_x,
                'y' => (float) $this->location_y,
            ];
        }
        return null;
    }

    /**
     * Get the full image URL.
     */
    public function getImageAttribute(): ?string
    {
        return $this->image_url;
    }

    /**
     * Get the fun facts.
     */
    public function getFunFactsAttribute(): ?string
    {
        return $this->facts;
    }

    /**
     * Get the map node for this animal.
     */
    public function mapNode()
    {
        return $this->morphOne(MapNode::class, 'placeable');
    }

    /**
     * Check if animal has been placed on the map.
     */
    public function getIsMappedAttribute(): bool
    {
        return $this->mapNode()->exists();
    }
}
