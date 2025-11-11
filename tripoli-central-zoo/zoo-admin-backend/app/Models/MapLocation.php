<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MapLocation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'location_type',
        'reference_id',
        'coordinate_x',
        'coordinate_y',
        'svg_path',
        'map_level',
        'description',
        'is_interactive',
    ];

    protected $casts = [
        'svg_path' => 'array',
        'is_interactive' => 'boolean',
        'coordinate_x' => 'decimal:6',
        'coordinate_y' => 'decimal:6',
    ];

    public function reference()
    {
        return match($this->location_type) {
            'animal' => $this->belongsTo(Animal::class, 'reference_id'),
            'facility' => $this->belongsTo(Facility::class, 'reference_id'),
            'activity' => $this->belongsTo(Activity::class, 'reference_id'),
            default => null,
        };
    }
}
