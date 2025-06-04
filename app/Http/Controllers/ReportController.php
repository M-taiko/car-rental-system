<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarType;
use App\Models\Rental;
use App\Models\ThirdPartyCar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:report-view-rentals');
    }

    public function rentalReport(Request $request)
    {
        $cars = Car::where('status', 'available')->get();
        $carTypes = CarType::all();

        return view('reports.rentals', compact('cars', 'carTypes'));
    }

    /**
     * Get rental data for DataTable
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRentalData(Request $request)
    {
        try {
            $draw = $request->get('draw');
            $start = $request->get('start', 0);
            $length = $request->get('length', 10);
            $search = $request->get('search');
            $searchValue = $search['value'] ?? '';
            
            // Base query with relationships including soft-deleted
            $query = Rental::withTrashed()->with([
                'car' => function($q) {
                    $q->withTrashed()->select('id', 'name', 'plate_number', 'car_type_id');
                },
                'car.carType' => function($q) {
                    $q->withTrashed()->select('id', 'name');
                },
                'customer' => function($q) {
                    $q->withTrashed()->select('id', 'name', 'phone', 'email', 'id_number');
                },
                'driver' => function($q) {
                    $q->withTrashed()->select('id', 'name', 'phone');
                }
            ]);
            
            // Apply search filter
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    // Search by rental ID or other rental fields
                    $q->where('rentals.id', 'like', '%' . $searchValue . '%')
                      ->orWhere('rentals.status', 'like', '%' . $searchValue . '%')
                      ->orWhere('rentals.start_time', 'like', '%' . $searchValue . '%')
                      ->orWhere('rentals.expected_end_time', 'like', '%' . $searchValue . '%')
                      ->orWhere('rentals.expected_amount', 'like', '%' . $searchValue . '%')
                      
                      // Search by car details
                      ->orWhereHas('car', function($q) use ($searchValue) {
                          $q->where('name', 'like', '%' . $searchValue . '%')
                            ->orWhere('plate_number', 'like', '%' . $searchValue . '%')
                            ->orWhere('chassis_number', 'like', '%' . $searchValue . '%');
                      })
                      
                      // Search by customer details
                      ->orWhereHas('customer', function($q) use ($searchValue) {
                          $q->where('name', 'like', '%' . $searchValue . '%')
                            ->orWhere('phone', 'like', '%' . $searchValue . '%')
                            ->orWhere('email', 'like', '%' . $searchValue . '%')
                            ->orWhere('id_number', 'like', '%' . $searchValue . '%');
                      })
                      
                      // Search by driver details if exists
                      ->orWhereHas('driver', function($q) use ($searchValue) {
                          $q->where('name', 'like', '%' . $searchValue . '%')
                            ->orWhere('phone', 'like', '%' . $searchValue . '%');
                      });
                });
            }
            
            // Apply status filter
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
            
            // Apply car type filter
            if ($request->filled('car_type_id') && $request->car_type_id !== 'all') {
                $query->whereHas('car', function($q) use ($request) {
                    $q->where('car_type_id', $request->car_type_id);
                });
            }
            
            // Apply date range filter
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                
                $query->where(function($q) use ($startDate, $endDate) {
                    // Check if rental period overlaps with the selected date range
                    $q->whereBetween('start_time', [$startDate, $endDate])
                      ->orWhereBetween('expected_end_time', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_time', '<=', $startDate)
                            ->where('expected_end_time', '>=', $endDate);
                      });
                });
            }
            
            // Get total records count before filtering
            $totalRecords = Rental::count();
            
            // Clone the query for filtered count
            $filteredQuery = clone $query;
            $filteredCount = $filteredQuery->count();
            
            // Apply ordering
            $orderColumn = $request->get('order')[0]['column'] ?? 1; // Default to ID column
            $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
            
            $columnMap = [
                0 => 'rentals.id',
                1 => 'rentals.id',
                2 => 'cars.name',
                3 => 'customers.name',
                4 => 'rentals.start_time',
                5 => 'rentals.expected_end_time',
                6 => 'rentals.expected_amount',
                7 => 'rentals.status'
            ];
            
            // Apply sorting
            if (isset($columnMap[$orderColumn])) {
                $query->orderBy($columnMap[$orderColumn], $orderDir);
            } else {
                $query->orderBy('rentals.id', 'desc');
            }
            
            // Eager load relationships and paginate
            $rentals = $query->skip($start)->take($length)->get();
            
            // Prepare data for DataTables
            $data = [];
            foreach ($rentals as $index => $rental) {
                $carInfo = $rental->car ? 
                    $rental->car->name . ' (' . $rental->car->plate_number . ')' : 
                    'N/A';
                
                $customerInfo = $rental->customer ? 
                    $rental->customer->name . ($rental->customer->phone ? ' - ' . $rental->customer->phone : '') : 
                    'N/A';
                
                $data[] = [
                    'DT_RowIndex' => $start + $index + 1,
                    'id' => $rental->id,
                    'car' => $carInfo,
                    'customer_name' => $customerInfo,
                    'start_date' => $rental->start_time ? $rental->start_time->format('Y-m-d H:i') : 'N/A',
                    'end_date' => $rental->expected_end_time ? $rental->expected_end_time->format('Y-m-d H:i') : 'N/A',
                    'total_cost' => $rental->expected_amount ?? 0,
                    'status' => $rental->status,
                    'action' => view('components.rental-actions', ['rental' => $rental])->render()
                ];
            }
            
            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredCount,
                'data' => $data,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getRentalData: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            
            return response()->json([
                'draw' => $request->get('draw', 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'حدث خطأ أثناء تحميل البيانات. الرجاء المحاولة مرة أخرى لاحقاً.'
            ], 500);
        }
    }

    public function thirdPartyReport(Request $request)
    {
        $query = ThirdPartyCar::with(['route', 'supervisor'])
            ->when($request->filled('date_from'), function ($q) use ($request) {
                return $q->whereDate('service_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                return $q->whereDate('service_date', '<=', $request->date_to);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                return $q->where('status', $request->status);
            });

        $thirdPartyCars = $query->latest()->paginate(15);

        $summary = [
            'total_cost' => $thirdPartyCars->sum('total_cost'),
            'total_distance' => $thirdPartyCars->sum('distance_km'),
            'completed' => $thirdPartyCars->where('status', 'completed')->count(),
            'pending' => $thirdPartyCars->where('status', 'pending')->count(),
            'approved' => $thirdPartyCars->where('status', 'approved')->count(),
        ];

        return view('reports.third_party', compact('thirdPartyCars', 'summary'));
    }

    public function carTypes(Request $request)
    {
        $query = CarType::with(['cars.rentals' => function ($q) use ($request) {
            $q->when($request->filled('date_from'), function ($q) use ($request) {
                return $q->whereDate('start_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                return $q->whereDate('start_date', '<=', $request->date_to);
            });
        }])
        ->whereHas('cars.rentals');

        $carTypes = $query->get();

        $reportData = $carTypes->map(function ($carType) {
            $totalRentals = 0;
            $totalRevenue = 0;
            $totalHours = 0;

            foreach ($carType->cars as $car) {
                $totalRentals += $car->rentals->count();
                $totalRevenue += $car->rentals->sum('total_cost');
                
                foreach ($car->rentals as $rental) {
                    $start = Carbon::parse($rental->start_date);
                    $end = $rental->actual_end_date ? Carbon::parse($rental->actual_end_date) : now();
                    $totalHours += $end->diffInHours($start);
                }
            }

            return [
                'id' => $carType->id,
                'name' => $carType->name,
                'total_cars' => $carType->cars->count(),
                'total_rentals' => $totalRentals,
                'total_revenue' => $totalRevenue,
                'avg_utilization' => $totalRentals > 0 ? ($totalHours / ($carType->cars->count() * 24 * 30)) * 100 : 0,
            ];
        });

        return view('reports.car_types', compact('reportData'));
    }
}
