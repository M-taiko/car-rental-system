<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand',
        'model',
        'plate_number',
        'year',
        'color',
        'daily_rate',
        'weekly_rate',
        'monthly_rate',
        'status',
        'description',
        'has_rental_percentage',
        'rental_percentage',
        'car_type_id'
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'weekly_rate' => 'decimal:2',
        'monthly_rate' => 'decimal:2',
        'rental_percentage' => 'decimal:2',
    ];

    public function maintenance()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function calculateRentalAmount($baseAmount)
    {
        if ($this->has_rental_percentage && $this->rental_percentage) {
            return $baseAmount + ($baseAmount * ($this->rental_percentage / 100));
        }
        return $baseAmount;
    }
    
    /**
     * Get the active rental for the car.
     */
    public function activeRental()
    {
        return $this->hasOne(Rental::class)
            ->where(function($query) {
                $query->where('status', 'active')
                      ->orWhere('status', 'reserved');
            });
    }
    
    /**
     * Get the car type that owns the car.
     */
    public function carType()
    {
        return $this->belongsTo(CarType::class, 'car_type_id');
    }
}
