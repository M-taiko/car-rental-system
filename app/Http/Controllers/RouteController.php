<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RouteController extends Controller
{
    /**
     * Display a listing of the routes.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $routes = Route::all(); 
            
        return view('routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new route.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('routes.create');
    }

    /**
     * Store a newly created route in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Log the incoming request data for debugging
        Log::info('Route creation request data:', $request->all());
        
        try {
            // Validate the incoming request data
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:routes,name',
                'start_point' => 'required|string|max:255',
                'end_point' => 'required|string|max:255',
                'distance_km' => 'required|numeric|min:0.01',
                'external_cost' => 'required|numeric|min:0',
                'internal_cost' => 'nullable|numeric|min:0',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);
            
            // Ensure is_active is set
            $validated['is_active'] = $request->has('is_active') ? true : false;
            
            Log::info('Validated data:', $validated);

            // Create the route
            $route = Route::create($validated);

            // Log success
            Log::info('Route created successfully with ID: ' . $route->id);


            return redirect()
                ->route('routes.index')
                ->with('success', __('messages.route_created'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors
            Log::error('Route validation failed: ' . $e->getMessage());
            Log::error('Validation errors: ', $e->errors());
            
            return back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            // Log other errors
            Log::error('Route creation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'حدث خطأ أثناء محاولة حفظ البيانات. الرجاء المحاولة مرة أخرى.'
                ]);
        }
    }

    /**
     * Show the form for editing the specified route.
     *
     * @param  \App\Models\Route  $route
     * @return \Illuminate\View\View
     */
    public function edit(Route $route)
    {
        return view('routes.edit', compact('route'));
    }

    /**
     * Update the specified route in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Route  $route
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('routes')->ignore($route->id)
            ],
            'start_point' => 'required|string|max:255',
            'end_point' => 'required|string|max:255',
            'distance_km' => 'required|numeric|min:0.01',
            'external_cost' => 'required|numeric|min:0',
            'internal_cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $route->update($validated);
            
            DB::commit();
            
            return redirect()
                ->route('routes.index')
                ->with('success', __('messages.route_updated'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', __('messages.error_occurred') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified route from storage.
     *
     * @param  \App\Models\Route  $route
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Route $route)
    {
        // Check if route is used in any rentals
        if ($route->rentals()->exists()) {
            return back()->with('error', __('messages.route_in_use'));
        }

        try {
            DB::beginTransaction();
            
            $route->delete();
            
            DB::commit();
            
            return redirect()
                ->route('routes.index')
                ->with('success', __('messages.route_deleted'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', __('messages.error_occurred') . ': ' . $e->getMessage());
        }
    }

    /**
     * Toggle the active status of the specified route.
     *
     * @param  \App\Models\Route  $route
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Route $route)
    {
        try {
            $route->update([
                'is_active' => !$route->is_active
            ]);
            
            return response()->json([
                'success' => true,
                'message' => __('messages.route_updated'),
                'is_active' => $route->is_active
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get routes for select2 dropdown
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoutesForSelect(Request $request)
    {
        $search = $request->input('q');
        
        $routes = Route::when($search, function($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%")
                           ->orWhere('start_point', 'like', "%{$search}%")
                           ->orWhere('end_point', 'like', "%{$search}%");
            })
            ->active()
            ->limit(10)
            ->get();
            
        $formattedRoutes = $routes->map(function($route) {
            return [
                'id' => $route->id,
                'text' => "{$route->name} ({$route->start_point} - {$route->end_point}) - " . 
                         number_format($route->external_cost, 2) . ' ' . config('settings.currency_symbol'),
                'name' => $route->name,
                'start_point' => $route->start_point,
                'end_point' => $route->end_point,
                'external_cost' => $route->external_cost,
                'price' => $route->external_cost,
                'formatted_price' => number_format($route->external_cost, 2) . ' ' . config('settings.currency_symbol', 'SAR')
            ];
        });
        
        return response()->json($formattedRoutes);
    }
}