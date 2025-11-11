<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MapNode extends Model
{
    protected $fillable = [
        'x',
        'y',
        'type',
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
}
