<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OpeningHour extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
        'notes',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_closed' => 'boolean',
    ];

    protected $attributes = [
        'is_closed' => false,
    ];

    /**
     * Get the parent entity model (animal, facility, etc.).
     */
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to filter by day of week.
     */
    public function scopeForDay(Builder $query, int $dayOfWeek): Builder
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Scope a query to only include open days.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('is_closed', false);
    }

    /**
     * Scope a query to only include closed days.
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('is_closed', true);
    }

    /**
     * Get the day name.
     */
    public function getDayNameAttribute(): string
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return $days[$this->day_of_week] ?? 'Unknown';
    }
}
