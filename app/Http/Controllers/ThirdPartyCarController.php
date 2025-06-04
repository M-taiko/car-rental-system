<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\ThirdPartyCar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThirdPartyCarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_third_party_cars', ['only' => ['index', 'show']]);
        $this->middleware('permission:create_third_party_cars', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_third_party_cars', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_third_party_cars', ['only' => ['destroy']]);
        $this->middleware('permission:approve_third_party_cars', ['only' => ['approve', 'reject']]);
    }

    public function index(Request $request)
    {
        $query = ThirdPartyCar::with(['route', 'supervisor'])
            ->when($request->filled('status'), function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                return $q->whereDate('service_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                return $q->whereDate('service_date', '<=', $request->date_to);
            });

        $thirdPartyCars = $query->latest()->paginate(15);
        
        return view('third_party_cars.index', compact('thirdPartyCars'));
    }

    public function create()
    {
        $routes = Route::where('is_active', true)->get();
        return view('third_party_cars.create', compact('routes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_number' => 'required|string|max:50',
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'route_id' => 'required|exists:routes,id',
            'distance_km' => 'required|numeric|min:0',
            'price_per_km' => 'required|numeric|min:0',
            'service_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['total_cost'] = $validated['distance_km'] * $validated['price_per_km'];
        $validated['status'] = 'pending';

        ThirdPartyCar::create($validated);

        return redirect()->route('third-party-cars.index')
            ->with('success', 'تم إضافة طلب سيارة خارجية بنجاح');
    }

    public function show(ThirdPartyCar $thirdPartyCar)
    {
        $thirdPartyCar->load(['route', 'supervisor']);
        return view('third_party_cars.show', compact('thirdPartyCar'));
    }

    public function edit(ThirdPartyCar $thirdPartyCar)
    {
        if ($thirdPartyCar->status !== 'pending') {
            return back()->with('error', 'لا يمكن تعديل الطلب بعد الموافقة عليه');
        }

        $routes = Route::where('is_active', true)->get();
        return view('third_party_cars.edit', compact('thirdPartyCar', 'routes'));
    }

    public function update(Request $request, ThirdPartyCar $thirdPartyCar)
    {
        if ($thirdPartyCar->status !== 'pending') {
            return back()->with('error', 'لا يمكن تعديل الطلب بعد الموافقة عليه');
        }

        $validated = $request->validate([
            'car_number' => 'required|string|max:50',
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'route_id' => 'required|exists:routes,id',
            'distance_km' => 'required|numeric|min:0',
            'price_per_km' => 'required|numeric|min:0',
            'service_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['total_cost'] = $validated['distance_km'] * $validated['price_per_km'];

        $thirdPartyCar->update($validated);

        return redirect()->route('third-party-cars.index')
            ->with('success', 'تم تحديث طلب السيارة الخارجية بنجاح');
    }

    public function destroy(ThirdPartyCar $thirdPartyCar)
    {
        if ($thirdPartyCar->status !== 'pending') {
            return back()->with('error', 'لا يمكن حذف الطلب بعد الموافقة عليه');
        }

        $thirdPartyCar->delete();

        return redirect()->route('third-party-cars.index')
            ->with('success', 'تم حذف طلب السيارة الخارجية بنجاح');
    }

    public function approve(ThirdPartyCar $thirdPartyCar)
    {
        if (!auth()->user()->can('approve_third_party_cars')) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }

        $thirdPartyCar->update([
            'status' => 'approved',
            'supervisor_id' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'تم اعتماد طلب السيارة الخارجية بنجاح');
    }

    public function reject(ThirdPartyCar $thirdPartyCar)
    {
        if (!auth()->user()->can('approve_third_party_cars')) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }

        $thirdPartyCar->update([
            'status' => 'cancelled',
            'supervisor_id' => auth()->id(),
        ]);

        return back()->with('success', 'تم رفض طلب السيارة الخارجية بنجاح');
    }

    public function complete(ThirdPartyCar $thirdPartyCar)
    {
        if (!auth()->user()->can('approve_third_party_cars')) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }

        $thirdPartyCar->update([
            'status' => 'completed',
        ]);

        return back()->with('success', 'تم إكمال خدمة السيارة الخارجية بنجاح');
    }
}
