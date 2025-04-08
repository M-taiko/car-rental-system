<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePartSale extends Model
{
    protected $fillable = ['spare_part_id', 'quantity', 'total_price', 'sale_date'];

    protected $casts = [
        'sale_date' => 'datetime',
    ];

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
}
