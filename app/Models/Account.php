<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['type', 'amount', 'description', 'date'];

    protected $dates = ['date'];
}
