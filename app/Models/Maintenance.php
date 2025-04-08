<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $table = 'maintenance'; // تحديد اسم الجدول بشكل صريح

    protected $fillable = [
        'bike_id',
        'type',           // الحقل الجديد
        'customer_id',    // الحقل الجديد
        'cost',
        'description',
        'date',
        'status',
    ];
    public function bike()
    {
        return $this->belongsTo(Bike::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
    public function parts()
    {
        return $this->hasMany(MaintenancePart::class);
    }

}
