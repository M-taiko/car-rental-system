<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Rental extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'car_id',
        'customer_id',
        'driver_id',
        'start_time',
        'route_id',
        'expected_end_time',
        'actual_end_time',
        'price_per_day',
        'rental_mode',
        'total_amount',
        'driver_price_per_day',
        'expected_amount',
        'actual_amount',
        'paid_amount',
        'refunded_amount',
        'status',
        'notes',
        'accountable_id',
        'accountable_type'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'expected_end_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'price_per_day' => 'decimal:2',
        'driver_price_per_day' => 'decimal:2',
        'expected_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2'
    ];


    public function accountable()
    {
        return $this->morphTo();
    }

    protected $appends = ['total_cost', 'end_time', 'duration'];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    
    /**
     * The routes that belong to the rental.
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
    
    /**
     * Get the total cost of all routes for this rental.
     *
     * @return float
     */
    public function getRouteCostAttribute()
    {
        return $this->route ? $this->route->price : 0;
    }
    
    /**
     * Calculate the total cost including routes.
     *
     * @return float
     */
    public function calculateTotalCost()
    {
        $baseAmount = $this->actual_amount ?? $this->expected_amount;
        return $baseAmount + $this->route_cost;
    }

    public function calculateExpectedAmount()
    {
        $days = $this->start_time->diffInDays($this->expected_end_time) + 1;
        $amount = $days * $this->price_per_day;

        if ($this->driver_id) {
            $amount += $days * $this->driver_price_per_day;
        }

        return $amount;
    }

    public function calculateActualAmount()
    {
        if (!$this->actual_end_time) {
            return $this->expected_amount;
        }

        $days = $this->start_time->diffInDays($this->actual_end_time) + 1;
        $amount = $days * $this->price_per_day;

        if ($this->driver_id) {
            $amount += $days * $this->driver_price_per_day;
        }

        return $amount;
    }

    public function calculateRefundAmount()
    {
        if (!$this->actual_end_time || !$this->actual_amount) {
            return 0;
        }

        $refund = $this->paid_amount - $this->actual_amount;
        return $refund > 0 ? $refund : 0;
    }

    public function getTotalCostAttribute()
    {
        $baseAmount = $this->actual_amount ?? $this->expected_amount;
        return $baseAmount + $this->route_cost;
    }

    public function getEndTimeAttribute()
    {
        return $this->actual_end_time ?? $this->expected_end_time;
    }

    public function getDurationAttribute()
    {
        $endTime = $this->actual_end_time ?? $this->expected_end_time;
        return $this->start_time->diffInDays($endTime) + 1;
    }

    public function calculateRemainingAmount()
    {
        return $this->total_cost - $this->paid_amount;
    }

    public function getStatusTextAttribute()
    {
        return [
            'active' => __('messages.active'),
            'completed' => __('messages.completed'),
            'cancelled' => __('messages.cancelled')
        ][$this->status] ?? $this->status;
    }

public function accounts()
{
    return $this->morphMany(Account::class, 'accountable');
}







}
