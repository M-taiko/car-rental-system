<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BikeController extends Controller
{
    public function index()
    {
        return view('bikes.index');
    }

    public function getBikesData(Request $request)
    {
        if ($request->ajax()) {
            $data = Bike::select(['id', 'name', 'type', 'color', 'price_per_hour', 'status']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '';
                    if (auth()->user()->hasPermissionTo('edit-bikes')) {
                        $actionBtn .= '<a href="javascript:void(0)" class="btn btn-sm btn-primary editBike" data-id="' . $row->id . '"><i class="fas fa-edit"></i></a> ';
                    }
                    if (auth()->user()->hasPermissionTo('delete-bikes')) {
                        $actionBtn .= '<a href="javascript:void(0)" class="btn btn-sm btn-danger deleteBike" data-id="' . $row->id . '"><i class="fas fa-trash"></i></a>';
                    }
                    return $actionBtn ?: 'No Actions'; // لو مفيش أي أكشن متاح
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'price_per_hour' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Bike::create($validated);

        return redirect()->route('bikes.index')->with('success', __('messages.bike_added_successfully'));
    }

    // دوال تانية زي edit, update, destroy لو عاوز تضيفها
}
