<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:driver-list|driver-create|driver-edit|driver-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:driver-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:driver-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:driver-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $drivers = Driver::latest()->paginate(10);
        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:drivers',
            'license_number' => 'required|unique:drivers',
            'license_expiry' => 'required|date',
            'daily_rate' => 'required|numeric',
            'address' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();
        $data['status'] = 'available';

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('drivers', 'public');
            $data['image'] = $imagePath;
        }

        Driver::create($data);

        return redirect()->route('driver.index')
            ->with('success', 'Driver added successfully.');
    }

    public function show(Driver $driver)
    {
        return view('driver.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        return view('driver.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:drivers,phone,' . $driver->id,
            'license_number' => 'required|unique:drivers,license_number,' . $driver->id,
            'license_expiry' => 'required|date',
            'daily_rate' => 'required|numeric',
            'address' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($driver->image) {
                Storage::disk('public')->delete($driver->image);
            }
            $imagePath = $request->file('image')->store('drivers', 'public');
            $data['image'] = $imagePath;
        }

        $driver->update($data);

        return redirect()->route('driver.index')
            ->with('success', 'Driver updated successfully');
    }

    public function destroy(Driver $driver)
    {
        if ($driver->image) {
            Storage::disk('public')->delete($driver->image);
        }
        $driver->delete();

        return redirect()->route('driver.index')
            ->with('success', 'Driver deleted successfully');
    }

    public function updateStatus(Request $request, Driver $driver)
    {
        $request->validate([
            'status' => 'required|in:available,assigned,off_duty'
        ]);

        $driver->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Driver status updated successfully');
    }
}
