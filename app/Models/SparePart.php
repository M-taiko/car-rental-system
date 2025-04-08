<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    protected $fillable = ['name', 'quantity', 'purchase_price', 'selling_price', 'description'];

    public function sales()
    {
        return $this->hasMany(SparePartSale::class);
    }
}
