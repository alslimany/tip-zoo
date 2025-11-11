<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'facility_type_id',
        'name',
        'description',
        'image',
        'gallery',
        'opening_hours',
        'contact_phone',
        'contact_email',
        'amenities',
        'is_accessible',
        'is_open',
        'capacity',
        'display_order',
    ];

    protected $casts = [
        'gallery' => 'array',
        'opening_hours' => 'array',
        'amenities' => 'array',
        'is_accessible' => 'boolean',
        'is_open' => 'boolean',
    ];

    public function facilityType(): BelongsTo
    {
        return $this->belongsTo(FacilityType::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
