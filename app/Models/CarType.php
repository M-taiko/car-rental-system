<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function shiftRates(): HasMany
    {
        return $this->hasMany(CarTypeShiftRate::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    public function getShiftRate($shiftId)
    {
        $rate = $this->shiftRates()->where('work_shift_id', $shiftId)->first();
        return $rate ? $rate->rate : 0;
    }
}
