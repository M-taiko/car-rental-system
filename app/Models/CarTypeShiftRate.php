<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarTypeShiftRate extends Model
{
    protected $fillable = [
        'car_type_id',
        'work_shift_id',
        'rate'
    ];

    protected $casts = [
        'rate' => 'decimal:2'
    ];

    public function carType(): BelongsTo
    {
        return $this->belongsTo(CarType::class);
    }

    public function workShift(): BelongsTo
    {
        return $this->belongsTo(WorkShift::class);
    }
}
