<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Route extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'start_point',
        'end_point',
        'distance_km',
        'external_cost',
        'internal_cost',
        'description',
        'is_active',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'distance_km' => 'decimal:2',
        'external_cost' => 'decimal:2',
        'internal_cost' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'formatted_external_cost',
        'formatted_internal_cost',
        'formatted_distance',
    ];

    /**
     * Scope a query to only include active routes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the formatted external cost attribute.
     *
     * @return string
     */
    public function getFormattedExternalCostAttribute(): string
    {
        return number_format($this->external_cost, 2);
    }

    /**
     * Get the formatted internal cost attribute.
     *
     * @return string
     */
    public function getFormattedInternalCostAttribute(): ?string
    {
        return $this->internal_cost ? number_format($this->internal_cost, 2) : null;
    }

    /**
     * Get the formatted distance attribute.
     *
     * @return string
     */
    public function getFormattedDistanceAttribute(): string
    {
        return number_format($this->distance_km, 2) . ' ' . __('km');
    }

    /**
     * Get the rentals that belong to this route.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rentals(): BelongsToMany
    {
        return $this->belongsToMany(Rental::class, 'rental_route')
            ->withPivot([
                'price',
                'notes',
                'sort_order',
                'created_at',
                'updated_at',
            ])
            ->withTimestamps()
            ->orderBy('pivot_sort_order');
    }

    /**
     * Get the third party cars for this route.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function thirdPartyCars(): HasMany
    {
        return $this->hasMany(ThirdPartyCar::class);
    }


}
