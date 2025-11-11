<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use SoftDeletes;

    const TYPE_RESTROOM = 'restroom';
    const TYPE_DINING = 'dining';
    const TYPE_GIFT_SHOP = 'gift_shop';
    const TYPE_INFORMATION = 'information';
    const TYPE_FIRST_AID = 'first_aid';
    const TYPE_PARKING = 'parking';

    protected $fillable = [
        'name',
        'type',
        'category_id',
        'location_x',
        'location_y',
        'description',
        'opening_hours',
        'image_url',
        'status',
        'gallery',
        'contact_phone',
        'contact_email',
        'amenities',
        'is_accessible',
        'capacity',
        'display_order',
    ];

    protected $casts = [
        'location_x' => 'decimal:6',
        'location_y' => 'decimal:6',
        'gallery' => 'array',
        'opening_hours' => 'array',
        'amenities' => 'array',
        'is_accessible' => 'boolean',
    ];

    protected $attributes = [
        'status' => 'open',
        'is_accessible' => true,
    ];

    /**
     * Get the category that owns the facility.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all of the facility's opening hours.
     */
    public function openingHours(): MorphMany
    {
        return $this->morphMany(OpeningHour::class, 'entity');
    }

    /**
     * Get all of the activities for the facility.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Scope a query to only include open facilities.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope a query to only include closed facilities.
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope a query to only include facilities in maintenance.
     */
    public function scopeMaintenance(Builder $query): Builder
    {
        return $query->where('status', 'maintenance');
    }

    /**
     * Scope a query to filter by facility type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include restroom facilities.
     */
    public function scopeRestrooms(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_RESTROOM);
    }

    /**
     * Scope a query to only include dining facilities.
     */
    public function scopeDining(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_DINING);
    }

    /**
     * Scope a query to only include gift shop facilities.
     */
    public function scopeGiftShops(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_GIFT_SHOP);
    }

    /**
     * Scope a query to only include accessible facilities.
     */
    public function scopeAccessible(Builder $query): Builder
    {
        return $query->where('is_accessible', true);
    }

    /**
     * Get the facility's coordinate as an array.
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
     * Check if the facility is currently open.
     */
    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'open';
    }
}
