<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Expense extends Model
{

    use HasFactory;
    protected $fillable = ['type', 'amount', 'description', 'date'];

    public static function getTypes()
    {
        return [
            'maintenance' => __('messages.maintenance'),
            'fuel' => __('messages.fuel'),
            'salary' => __('messages.salary'),
            'rent' => __('messages.rent'),
            'utilities' => __('messages.utilities'),
            'insurance' => __('messages.insurance'),
            'marketing' => __('messages.marketing'),
            'other' => __('messages.other'),
        ];
    }
}

