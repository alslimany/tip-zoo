<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'activity_type',
        'description',
        'image',
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
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'recurrence' => 'array',
        'requires_booking' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
}
