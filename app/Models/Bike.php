<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bike extends Model
{
    protected $fillable = ['name', 'type', 'color', 'price_per_hour', 'status', 'description'];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
