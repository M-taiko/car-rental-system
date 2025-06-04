<?php

namespace App\Http\Controllers;

use App\Models\CarType;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarTypeController extends Controller
{
    public function index()
    {
        $carTypes = CarType::with('shiftRates.workShift')
            ->latest()
            ->paginate(10);
            
        return view('car_types.index', compact('carTypes'));
    }

    public function create()
    {
        $workShifts = WorkShift::where('is_active', true)->get();
        return view('car_types.create', compact('workShifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:car_types,name',
            'description' => 'nullable|string',
            'shift_rates' => 'required|array',
            'shift_rates.*.work_shift_id' => 'required|exists:work_shifts,id',
            'shift_rates.*.rate' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $carType = CarType::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            foreach ($validated['shift_rates'] as $rate) {
                $carType->shiftRates()->create([
                    'work_shift_id' => $rate['work_shift_id'],
                    'rate' => $rate['rate'],
                ]);
            }
        });

        return redirect()->route('car-types.index')
            ->with('success', 'تم إضافة نوع السيارة بنجاح');
    }

    public function edit(CarType $carType)
    {
        $carType->load('shiftRates.workShift');
        $workShifts = WorkShift::where('is_active', true)->get();
        
        return view('car_types.edit', compact('carType', 'workShifts'));
    }

    public function update(Request $request, CarType $carType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:car_types,name,' . $carType->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'shift_rates' => 'required|array',
            'shift_rates.*.work_shift_id' => 'required|exists:work_shifts,id',
            'shift_rates.*.rate' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($carType, $validated) {
            $carType->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Remove existing rates
            $carType->shiftRates()->delete();

            // Add new rates
            foreach ($validated['shift_rates'] as $rate) {
                $carType->shiftRates()->create([
                    'work_shift_id' => $rate['work_shift_id'],
                    'rate' => $rate['rate'],
                ]);
            }
        });

        return redirect()->route('car-types.index')
            ->with('success', 'تم تحديث نوع السيارة بنجاح');
    }

    public function destroy(CarType $carType)
    {
        if ($carType->cars()->exists()) {
            return back()->with('error', 'لا يمكن حذف نوع السيارة لأنه مرتبط بسيارات');
        }

        $carType->delete();

        return redirect()->route('car-types.index')
            ->with('success', 'تم حذف نوع السيارة بنجاح');
    }
}
