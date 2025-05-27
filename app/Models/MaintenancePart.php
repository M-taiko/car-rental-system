<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenancePart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'maintenance_id',
        'spare_part_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2'
    ];

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
}
