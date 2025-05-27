@extends('layouts.master')

@section('css')
<!-- Owl-carousel css -->
<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
<!-- Select2 css -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">{{ __('messages.dashboard_welcome') }}</h2>
                <p>{{ __('messages.dashboard') }}</p>
            </div>
        </div>
        <div class="main-dashboard-header-right">
            <div>
                <label class="tx-13">{{ __('messages.total_revenue') }}</label>
                <h5>{{ $accounts->where('type', 'income')->sum('amount') }}</h5>
            </div>
            <div>
                <label class="tx-13">{{ __('messages.total_expenses') }}</label>
                <h5>{{ $accounts->where('type', 'expense')->sum('amount') }}</h5>
            </div>
            <div>
                <label class="tx-13">{{ __('messages.financial_balance') }}</label>
                <h5>{{ $accounts->where('type', 'income')->sum('amount') - $accounts->where('type', 'expense')->sum('amount') }}</h5>
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="row row-sm mb-4">
        <!-- Cars Stats -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-3">{{ __('messages.available_vehicles') }}</h4>
                        <i class="mdi mdi-car text-primary fs-3"></i>
                    </div>
                    <div class="d-flex align-items-center">
                        <div>
                            <h4 class="mb-0">{{ App\Models\Car::where('status', 'available')->count() }}</h4>
                            <p class="mb-0 text-muted">{{ __('messages.total_cars') }}: {{ App\Models\Car::count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drivers Stats -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-3">{{ __('messages.available_drivers') }}</h4>
                        <i class="mdi mdi-account text-success fs-3"></i>
                    </div>
                    <div class="d-flex align-items-center">
                        <div>
                            <h4 class="mb-0">{{ App\Models\Driver::where('status', 'available')->count() }}</h4>
                            <p class="mb-0 text-muted">{{ __('messages.total_drivers') }}: {{ App\Models\Driver::count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Rentals -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-3">{{ __('messages.active_rentals') }}</h4>
                        <i class="mdi mdi-clipboard-list text-info fs-3"></i>
                    </div>
                    <div class="d-flex align-items-center">
                        <div>
                            <h4 class="mb-0">{{ App\Models\Rental::where('status', 'active')->count() }}</h4>
                            <p class="mb-0 text-muted">{{ __('messages.total_rentals') }}: {{ App\Models\Rental::count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-3">{{ __('messages.customers') }}</h4>
                        <i class="mdi mdi-account-group text-warning fs-3"></i>
                    </div>
                    <div class="d-flex align-items-center">
                        <div>
                            <h4 class="mb-0">{{ App\Models\Customer::count() }}</h4>
                            <p class="mb-0 text-muted">{{ __('messages.total_customers') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-sm">
        <!-- Recent Rentals -->
        <div class="col-xl-6 col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0">{{ __('messages.recent_rentals') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="rentalsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.customer') }}</th>
                                    <th>{{ __('messages.car') }}</th>
                                    <th>{{ __('messages.driver') }}</th>
                                    <th>{{ __('messages.start_date') }}</th>
                                    <th>{{ __('messages.total_cost') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(App\Models\Rental::with(['customer', 'car', 'driver'])->latest()->take(5)->get() as $rental)
                                <tr>
                                    <td>{{ $rental->customer->name }}</td>
                                    <td>{{ $rental->car->brand }} {{ $rental->car->model }}</td>
                                    <td>{{ $rental->driver ? $rental->driver->name : __('messages.no_driver') }}</td>
                                    <td>{{ $rental->start_time->format('Y-m-d H:i') }}</td>
                                    <td>{{ $rental->total_cost ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $rental->status == 'active' ? 'success' : ($rental->status == 'completed' ? 'info' : 'danger') }}">
                                            {{ ucfirst($rental->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Rentals Chart -->
                    <canvas id="rentalsChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Income vs Expenses -->
        <div class="col-xl-6 col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0">{{ __('messages.income_vs_expenses') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="accountsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.type') }}</th>
                                    <th>{{ __('messages.description') }}</th>
                                    <th>{{ __('messages.amount') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($accounts->take(5) as $account)
                                <tr>
                                    <td>{{ ucfirst($account->type) }}</td>
                                    <td>{{ $account->description }}</td>
                                    <td>{{ $account->amount }}</td>
                                    <td>{{ date('Y-m-d', strtotime($account->date)) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Income vs Expense Chart -->
                    <canvas id="accountsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- Chart.js -->
<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTables
        $('#rentalsTable').DataTable({
            "language": {
                "url": "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.6/i18n/en.json' }}"
            }
        });

        $('#accountsTable').DataTable({
            "language": {
                "url": "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.6/i18n/en.json' }}"
            }
        });

        // Rentals Chart
        const rentalsChartCtx = document.getElementById('rentalsChart').getContext('2d');
        new Chart(rentalsChartCtx, {
            type: 'line',
            data: {
                labels: @json($rentalsChartData->keys()),
                datasets: [{
                    label: '{{ __('messages.rentals') }}',
                    data: @json($rentalsChartData->values()),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: '{{ __('messages.date') }}' } },
                    y: { title: { display: true, text: '{{ __('messages.rentals') }}' } }
                }
            }
        });

        // Income vs Expense Chart
        const accountsChartCtx = document.getElementById('accountsChart').getContext('2d');
        new Chart(accountsChartCtx, {
            type: 'line',
            data: {
                labels: @json($accountsChartData->keys()),
                datasets: [
                    {
                        label: '{{ __('messages.income') }}',
                        data: @json($incomeData->values()),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                    },
                    {
                        label: '{{ __('messages.expenses') }}',
                        data: @json($expenseData->values()),
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true,
                    }
                ]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: '{{ __('messages.date') }}' } },
                    y: { title: { display: true, text: '{{ __('messages.amount') }}' } }
                }
            }
        });
    });
</script>
@endsection
