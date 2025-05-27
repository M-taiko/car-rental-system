<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'purchase_price',
        'selling_price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2'
    ];

    public function maintenanceParts()
    {
        return $this->hasMany(MaintenancePart::class);
    }

    public function sales()
    {
        return $this->hasMany(SparePartSale::class);
    }
}
