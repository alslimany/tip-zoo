<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MapNode extends Model
{
    protected $fillable = [
        'x',
        'y',
        'type',
        'placeable_type',
        'placeable_id',
        'name',
        'connections',
        'description',
    ];

    protected $casts = [
        'x' => 'decimal:6',
        'y' => 'decimal:6',
        'connections' => 'array',
    ];

    /**
     * Get the owning placeable model (Animal or Facility).
     */
    public function placeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get all paths starting from this node.
     */
    public function pathsFrom(): HasMany
    {
        return $this->hasMany(MapPath::class, 'start_node_id');
    }

    /**
     * Get all paths ending at this node.
     */
    public function pathsTo(): HasMany
    {
        return $this->hasMany(MapPath::class, 'end_node_id');
    }

    /**
     * Get the node's coordinate as an array.
     */
    public function getCoordinatesAttribute(): array
    {
        return [
            'x' => (float) $this->x,
            'y' => (float) $this->y,
        ];
    }

    /**
     * Get the display name for the node.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->placeable) {
            return $this->placeable->name;
        }
        return $this->name ?? "Node #{$this->id}";
    }

    /**
     * Check if node is linked to a place.
     */
    public function getIsPlaceAttribute(): bool
    {
        return !is_null($this->placeable_type) && !is_null($this->placeable_id);
    }
}
