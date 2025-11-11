<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapPath extends Model
{
    protected $fillable = [
        'start_node_id',
        'end_node_id',
        'distance',
        'accessible',
        'path_data',
        'description',
    ];

    protected $casts = [
        'distance' => 'decimal:2',
        'accessible' => 'boolean',
        'path_data' => 'array',
    ];

    protected $attributes = [
        'accessible' => true,
    ];

    /**
     * Get the starting node.
     */
    public function startNode(): BelongsTo
    {
        return $this->belongsTo(MapNode::class, 'start_node_id');
    }

    /**
     * Get the ending node.
     */
    public function endNode(): BelongsTo
    {
        return $this->belongsTo(MapNode::class, 'end_node_id');
    }

    /**
     * Scope a query to only include accessible paths.
     */
    public function scopeAccessible(Builder $query): Builder
    {
        return $query->where('accessible', true);
    }
}
