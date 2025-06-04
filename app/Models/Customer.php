<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'id_number',
        'id_type',
        'notes',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
    
    /**
     * Scope a query to only include active customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function getIdTypeTextAttribute()
    {
        return [
            'national_id' => __('messages.national_id'),
            'iqama' => __('messages.iqama'),
            'passport' => __('messages.passport')
        ][$this->id_type] ?? $this->id_type;
    }
}
