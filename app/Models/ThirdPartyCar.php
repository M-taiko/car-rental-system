<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThirdPartyCar extends Model
{
    protected $fillable = [
        'car_number',
        'driver_name',
        'driver_phone',
        'route_id',
        'distance_km',
        'price_per_km',
        'total_cost',
        'service_date',
        'status',
        'supervisor_id',
        'approved_at',
        'notes'
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'price_per_km' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'service_date' => 'date',
        'approved_at' => 'datetime'
    ];

    protected $appends = ['status_label'];

    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'قيد الانتظار',
            'approved' => 'معتمد',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ][$this->status] ?? $this->status;
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function calculateTotalCost()
    {
        return $this->distance_km * $this->price_per_km;
    }
}
