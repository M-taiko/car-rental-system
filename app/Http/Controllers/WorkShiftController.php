<?php

namespace App\Http\Controllers;

use App\Models\WorkShift;
use Illuminate\Http\Request;

class WorkShiftController extends Controller
{
    public function index()
    {
        $shifts = WorkShift::latest()->paginate(10);
        return view('work_shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('work_shifts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:work_shifts,name',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
        ]);

        WorkShift::create($validated);

        return redirect()->route('work-shifts.index')
            ->with('success', 'تم إضافة الوردية بنجاح');
    }

    public function edit(WorkShift $workShift)
    {
        return view('work_shifts.edit', compact('workShift'));
    }

    public function update(Request $request, WorkShift $workShift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:work_shifts,name,' . $workShift->id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $workShift->update($validated);

        return redirect()->route('work-shifts.index')
            ->with('success', 'تم تحديث الوردية بنجاح');
    }

    public function destroy(WorkShift $workShift)
    {
        if ($workShift->carTypeRates()->exists()) {
            return back()->with('error', 'لا يمكن حذف الوردية لأنها مرتبطة بأسعار أنواع السيارات');
        }

        $workShift->delete();

        return redirect()->route('work-shifts.index')
            ->with('success', 'تم حذف الوردية بنجاح');
    }
}
