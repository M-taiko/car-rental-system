<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePart extends Model
{
    use HasFactory;
    protected $fillable = ['maintenance_id', 'spare_part_id', 'quantity'];

  public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }

}
