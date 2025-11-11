<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnimalCategory extends Model
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

    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class, 'category_id');
    }
}
