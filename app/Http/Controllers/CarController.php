<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:car-list|car-create|car-edit|car-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:car-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:car-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:car-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $cars = Car::latest()->paginate(10);
        return view('cars.index', compact('cars'));
    }

    public function create()
    {
        return view('cars.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand' => 'required',
            'model' => 'required',
            'year' => 'required',
            'plate_number' => 'required|unique:cars',
            'color' => 'required',
            'daily_rate' => 'required|numeric',
            'weekly_rate' => 'required|numeric',
            'monthly_rate' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('cars', 'public');
            $data['image'] = $imagePath;
        }

        Car::create($data);

        return redirect()->route('cars.index')
            ->with('success', 'Car added successfully.');
    }

    public function show(Car $car)
    {
        return view('cars.show', compact('car'));
    }

    public function edit(Car $car)
    {
        return view('cars.edit', compact('car'));
    }

    public function update(Request $request, Car $car)
    {
        $request->validate([
            'brand' => 'required',
            'model' => 'required',
            'year' => 'required',
            'plate_number' => 'required|unique:cars,plate_number,' . $car->id,
            'color' => 'required',
            'daily_rate' => 'required|numeric',
            'weekly_rate' => 'required|numeric',
            'monthly_rate' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($car->image) {
                Storage::disk('public')->delete($car->image);
            }
            $imagePath = $request->file('image')->store('cars', 'public');
            $data['image'] = $imagePath;
        }

        $car->update($data);

        return redirect()->route('cars.index')
            ->with('success', 'Car updated successfully');
    }

    public function destroy(Car $car)
    {
        if ($car->image) {
            Storage::disk('public')->delete($car->image);
        }
        $car->delete();

        return redirect()->route('cars.index')
            ->with('success', 'Car deleted successfully');
    }
}
