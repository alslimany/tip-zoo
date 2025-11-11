<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'schedule',
        'location',
        'type',
        'image_url',
        'status',
        'facility_id',
        'animal_id',
        'start_time',
        'end_time',
        'recurrence',
        'duration_minutes',
        'capacity',
        'requires_booking',
        'price',
        'age_restriction',
        'display_order',
    ];

    protected $casts = [
        'schedule' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'recurrence' => 'array',
        'requires_booking' => 'boolean',
        'price' => 'decimal:2',
    ];

    protected $attributes = [
        'status' => 'scheduled',
        'requires_booking' => false,
    ];

    /**
     * Get the facility that owns the activity.
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Get the animal that owns the activity.
     */
    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    /**
     * Scope a query to only include scheduled activities.
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope a query to only include cancelled activities.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include completed activities.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to filter by activity type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Get the full image URL.
     */
    public function getImageAttribute(): ?string
    {
        return $this->image_url;
    }

    /**
     * Get the activity name (for backward compatibility).
     */
    public function getNameAttribute(): ?string
    {
        return $this->title;
    }
}

