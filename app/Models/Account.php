<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['type', 'amount', 'description', 'date','created_by'];

    protected $dates = ['date'];
}
