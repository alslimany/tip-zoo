<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'scientific_name',
        'description',
        'image',
        'gallery',
        'habitat',
        'conservation_status',
        'diet',
        'age',
        'weight',
        'size',
        'fun_facts',
        'feeding_times',
        'is_visible',
        'is_featured',
        'display_order',
    ];

    protected $casts = [
        'gallery' => 'array',
        'diet' => 'array',
        'feeding_times' => 'array',
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AnimalCategory::class);
    }
}
