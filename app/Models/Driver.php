<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'id_number',
        'id_type',
        'license_number',
        'license_expiry',
        'status',
        'daily_rate',
        'address',
        'image',
        'notes'
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'daily_rate' => 'decimal:2'
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function getIsAvailableAttribute()
    {
        return $this->status === 'available';
    }
}
