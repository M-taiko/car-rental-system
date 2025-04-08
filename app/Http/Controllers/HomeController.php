<?php

namespace App\Http\Controllers;

use App\Models\Well;
use App\Models\Vessel;
use App\Models\Reading;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('index');
    }

}
