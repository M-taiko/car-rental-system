<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $fillable = ['bike_id', 'customer_id', 'customer_name', 'price_per_hour', 'start_time', 'original_start_time', 'end_time', 'total_cost', 'status'];
    // protected $casts = [
    //     'start_time' => 'datetime',
    //     'end_time' => 'datetime',
    // ];

    public function bike()
    {
        return $this->belongsTo(Bike::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
