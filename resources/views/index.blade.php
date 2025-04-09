@extends('layouts.master')

@section('css')
<!-- Owl-carousel css -->
<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
<!-- Select2 css (for dropdown) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Bootstrap css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">{{ __('messages.welcome') }}</h2>
                <p>{{ __('messages.dashboard') }}</p>
            </div>
        </div>
        <div class="main-dashboard-header-right">
            <div>
                <label class="tx-13">{{ __('messages.total_income') }}</label>
                <h5>{{ $accounts->where('type', 'income')->sum('amount') }}</h5>
            </div>
            <div>
                <label class="tx-13">{{ __('messages.total_expenses') }}</label>
                <h5>{{ $accounts->where('type', 'expense')->sum('amount') }}</h5>
            </div>
            <div>
                <label class="tx-13">{{ __('messages.balance') }}</label>
                <h5>{{ $accounts->where('type', 'income')->sum('amount') - $accounts->where('type', 'expense')->sum('amount') }}</h5>
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->
@endsection

@section('content')
    <div class="row row-sm">
        <!-- Sales Report -->
        <div class="col-xl-6 col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0">{{ __('messages.spare_part_sales') }} ({{ __('messages.last_30_days') }})</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Sales Table -->
                    <table class="table table-bordered" id="salesTable">
                        <thead>
                            <tr>
                                <th>{{ __('messages.spare_part') }}</th>
                                <th>{{ __('messages.quantity') }}</th>
                                <th>{{ __('messages.total_price') }}</th>
                                <th>{{ __('messages.sale_date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                                <tr>
                                    <td>{{ $sale->sparePart->name }}</td>
                                    <td>{{ $sale->quantity }}</td>
                                    <td>{{ $sale->total_price }}</td>
                                    <td>{{ date('Y-m-d', strtotime($sale->sale_date)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Sales Chart -->
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Spare Parts in Stock -->
        <div class="col-xl-6 col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0">{{ __('messages.spare_parts') }} ({{ __('messages.in_stock') }})</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Spare Parts Table -->
                    <table class="table table-bordered" id="sparePartsTable">
                        <thead>
                            <tr>
                                <th>{{ __('messages.spare_part_name') }}</th>
                                <th>{{ __('messages.quantity') }}</th>
                                <th>{{ __('messages.purchase_price') }}</th>
                                <th>{{ __('messages.selling_price') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($spareParts as $sparePart)
                                <tr>
                                    <td>{{ $sparePart->name }}</td>
                                    <td>{{ $sparePart->quantity }}</td>
                                    <td>{{ $sparePart->purchase_price }}</td>
                                    <td>{{ $sparePart->selling_price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Spare Parts Chart -->
                    <canvas id="sparePartsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Rentals Report -->
        <div class="col-xl-6 col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0">{{ __('messages.rentals') }} ({{ __('messages.last_30_days') }})</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Rentals Table -->
                    <table class="table table-bordered" id="rentalsTable">
                        <thead>
                            <tr>
                                <th>{{ __('messages.user_name') }}</th>
                                <th>{{ __('messages.bike') }}</th>
                                <th>{{ __('messages.start_time') }}</th>
                                <th>{{ __('messages.end_time') }}</th>
                                <th>{{ __('messages.total_cost') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rentals as $rental)
                                <tr>
                                    <td>{{ $rental->customer ? $rental->customer->name : 'N/A' }}</td>
                                    <td>{{ $rental->bike->name }}</td>
                                    <td>{{ $rental->start_time }}</td>
                                    <td>{{ $rental->end_time ? date('Y-m-d H:i:s', strtotime($rental->end_time)) : 'N/A' }}</td>
                                    <td>{{ $rental->total_cost ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Rentals Chart -->
                    <canvas id="rentalsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Expenses vs Income -->
        <div class="col-xl-6 col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0">{{ __('messages.accounts') }} ({{ __('messages.last_30_days') }})</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Accounts Table -->
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
                            @foreach($accounts as $account)
                                <tr>
                                    <td>{{ $account->type }}</td>
                                    <td>{{ $account->description }}</td>
                                    <td>{{ $account->amount }}</td>
                                    <td>{{ date('Y-m-d', strtotime($account->date)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Income vs Expense Chart -->
                    <canvas id="accountsChart" height="100"></canvas>
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
<!-- Select2 JS (for dropdown) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTables
        $('#salesTable').DataTable({
            "language": {
                "url": "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.6/i18n/en.json' }}"
            }
        });

        $('#sparePartsTable').DataTable({
            "language": {
                "url": "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.6/i18n/en.json' }}"
            }
        });

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

        // Sales Chart
        const salesChartCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesChartCtx, {
            type: 'line',
            data: {
                labels: @json(array_keys($salesChartData->toArray())),
                datasets: [{
                    label: '{{ __('messages.total_price') }}',
                    data: @json(array_values($salesChartData->toArray())),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: '{{ __('messages.date') }}' } },
                    y: { title: { display: true, text: '{{ __('messages.total_price') }}' } }
                }
            }
        });

        // Spare Parts Chart
        const sparePartsChartCtx = document.getElementById('sparePartsChart').getContext('2d');
        new Chart(sparePartsChartCtx, {
            type: 'bar',
            data: {
                labels: @json(array_keys($sparePartsChartData->toArray())),
                datasets: [{
                    label: '{{ __('messages.quantity') }}',
                    data: @json(array_values($sparePartsChartData->toArray())),
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: '{{ __('messages.spare_part') }}' } },
                    y: { title: { display: true, text: '{{ __('messages.quantity') }}' } }
                }
            }
        });

        // Rentals Chart
        const rentalsChartCtx = document.getElementById('rentalsChart').getContext('2d');
        new Chart(rentalsChartCtx, {
            type: 'line',
            data: {
                labels: @json(array_keys($rentalsChartData->toArray())),
                datasets: [{
                    label: '{{ __('messages.rentals') }}',
                    data: @json(array_values($rentalsChartData->toArray())),
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
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
                labels: @json(array_keys($incomeChartData->toArray())),
                datasets: [
                    {
                        label: '{{ __('messages.income') }}',
                        data: @json(array_values($incomeChartData->toArray())),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                    },
                    {
                        label: '{{ __('messages.expense') }}',
                        data: @json(array_values($expenseChartData->toArray())),
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
