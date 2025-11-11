<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class, 'facility_type_id');
    }
}
