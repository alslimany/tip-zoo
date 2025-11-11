<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    const TYPE_ANIMAL = 'animal';
    const TYPE_FACILITY = 'facility';

    protected $fillable = [
        'name',
        'color',
        'icon',
        'type',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Get all of the animals for the category.
     */
    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class);
    }

    /**
     * Get all of the facilities for the category.
     */
    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    /**
     * Scope a query to only include animal categories.
     */
    public function scopeAnimals(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_ANIMAL);
    }

    /**
     * Scope a query to only include facility categories.
     */
    public function scopeFacilities(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_FACILITY);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
