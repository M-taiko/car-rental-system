@extends('layouts.master')
@section('title', __('messages.dashboard'))

@section('content')

<!-- Breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="left-side">
        <h4 class="content-title mb-0 my-auto">{{ __('messages.dashboard') }}</h4>
        <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.overview') }}</span>
    </div>
    <div class="right-side">
        <div class="main-dashboard-header-right">
            <div>
                <label class="tx-13">{{ __('messages.total_revenue') }}</label>
                <h5>{{ number_format($totalRevenue, 2) }}    </h5>
            </div>
            <div>
                <label class="tx-13">{{ __('messages.total_expenses') }}</label>
                <h5>{{ number_format($totalExpenses, 2) }}    </h5>
            </div>
            <div>
                <label class="tx-13">{{ __('messages.net_balance') }}</label>
                <h5>{{ number_format($totalRevenue - $totalExpenses, 2) }} </h5>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mt-4">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h4 class="mb-0">{{ number_format($activeRentals, 0) }}</h4>
                <p class="mb-0">{{ __('messages.active_rentals') }}</p>
                <small class="text-light">{{ __('messages.total_rentals') }}: {{ App\Models\Rental::count() }}</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h4 class="mb-0">{{ number_format($availableCars, 0) }}</h4>
                <p class="mb-0">{{ __('messages.available_cars') }}</p>
                <small class="text-light">{{ __('messages.total_cars') }}: {{ App\Models\Car::count() }}</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h4 class="mb-0">{{ number_format($totalCustomers, 0) }}</h4>
                <p class="mb-0">{{ __('messages.total_customers') }}</p>
                <small class="text-light">{{ __('messages.new_customers') }}: {{ $newCustomers }}</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h4 class="mb-0">{{ number_format($availableDrivers, 0) }}</h4>
                <p class="mb-0">{{ __('messages.available_drivers') }}</p>
                <small class="text-light">{{ __('messages.total_drivers') }}: {{ App\Models\Driver::count() }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Recent Rentals -->
<div class="row mt-4">
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">{{ __('messages.recent_rentals') }}</h4>
                <a href="{{ route('rentals.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('messages.view_all') }}
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.car') }}</th>
                                <th>{{ __('messages.start_time') }}</th>
                                <th>{{ __('messages.end_time') }}</th>
                                <th>{{ __('messages.total_amount') }}</th>
                                <th>{{ __('messages.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentRentals as $rental)
                                <tr>
                                    <td>{{ $rental->customer?->name ?? '-' }}</td>
                                    <td>{{ $rental->car?->plate_number ?? '-' }}</td>
                                    <td>{{ optional($rental->start_time)->format('Y-m-d H:i') }}</td>
                                    <td>{{ optional($rental->expected_end_time)->format('Y-m-d H:i') }}</td>
                                    <td>{{ number_format($rental->total_amount, 2) }} </td>
                                    <td>
                                        <span class="badge badge-{{ $rental->status === 'active' ? 'success' : ($rental->status === 'completed' ? 'secondary' : 'danger') }}">
                                            {{ __("messages.{$rental->status}") }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Accounts -->
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">{{ __('messages.recent_accounts') }}</h4>
                <a href="{{ route('accounts.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('messages.view_all') }}
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.type') }}</th>
                                <th>{{ __('messages.amount') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentAccounts as $account)
                                <tr>
                                    <td>
                                        <span class="badge badge-{{ $account->type === 'income' ? 'success' : 'danger' }}">
                                            {{ $account->type === 'income' ? __('messages.income') : __('messages.expense') }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($account->amount, 2) }}</td>
                                    <td>{{ $account->date }}</td>
                                    <td>{{ $account->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Income vs Expenses Chart -->
<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __('messages.monthly_income_vs_expense') }}</h5>
                <canvas id="incomeExpenseChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
<!-- Monthly Rentals Chart -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __('messages.monthly_rentals_chart') }}</h5>
                <canvas id="monthlyRentalsChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>




@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
<script>
$(document).ready(function () {
    // رسم بياني للإيرادات والمصروفات الشهرية
    const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
    const incomeValues = @json($incomeData);
    const expenseValues = @json($expenseData);
    const labels = @json($dates);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: '{{ __("messages.income") }}',
                    data: incomeValues,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: '{{ __("messages.expenses") }}',
                    data: expenseValues,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: '{{ __("messages.financial_chart") }}' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value, index, values) {
                            return index % 3 === 0 ? this.getLabelForValue(value) : '';
                        }
                    }
                },
                x: {
                    title: { display: true, text: '{{ __("messages.date") }}' }
                }
            }
        }
    });

    // رسم بياني لعدد الإيجارات اليومية
    const monthlyCtx = document.getElementById('monthlyRentalsChart').getContext('2d');

    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: @json($dates),
            datasets: [{
                label: '{{ __("messages.daily_rentals") }}',
                data: @json($rentalCounts),
                backgroundColor: '#007bff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: '{{ __("messages.rentals_over_30_days") }}' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {    
                        callback: function (value, index, values) {
                            return index % 3 === 0 ? this.getLabelForValue(value) : '';
                        }
                    }
                },
                x: {
                    title: { display: true, text: '{{ __("messages.date") }}' }
                }
            }
        }
    });
});
</script>
@endsection