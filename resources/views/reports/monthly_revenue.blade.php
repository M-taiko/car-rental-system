@extends('layouts.app')

@section('title', 'تقرير الإيرادات الشهرية')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/chart.js/Chart.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
<style>
    .revenue-card {
        border-radius: 10px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,.125);
        overflow: hidden;
    }
    .revenue-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    .revenue-card .card-body {
        padding: 1.5rem;
    }
    .revenue-card .card-title {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    .revenue-card .card-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .revenue-card .card-change {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
    }
    .card-change.positive {
        color: #1cc88a;
    }
    .card-change.negative {
        color: #e74a3b;
    }
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 20px;
    }
    .table th {
        white-space: nowrap;
    }
    .badge-outline {
        background-color: transparent;
        border: 1px solid #ddd;
        color: #4e73df;
    }
    .export-buttons .btn {
        margin-left: 5px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>تقرير الإيرادات الشهرية
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
                        <li class="breadcrumb-item active">تقرير الإيرادات الشهرية</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>تصفية النتائج
                    </h5>
                    <div class="export-buttons">
                        <a href="{{ route('reports.export', ['type' => 'monthly-revenue', 'format' => 'excel']) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel me-1"></i> تصدير إكسل
                        </a>
                        <a href="{{ route('reports.export', ['type' => 'monthly-revenue', 'format' => 'pdf']) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                           class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf me-1"></i> تصدير PDF
                        </a>
                        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> رجوع
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.monthly-revenue') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="year" class="form-label">السنة</label>
                                    <select name="year" id="year" class="form-select">
                                        @for($y = date('Y'); $y >= 2020; $y--)
                                            <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="car_type_id" class="form-label">نوع السيارة</label>
                                    <select name="car_type_id" id="car_type_id" class="form-select">
                                        <option value="">الكل</option>
                                        @foreach($carTypes as $type)
                                            <option value="{{ $type->id }}" {{ request('car_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="branch_id" class="form-label">الفرع</label>
                                    <select name="branch_id" id="branch_id" class="form-select">
                                        <option value="">الكل</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> تطبيق الفلتر
                                </button>
                                <a href="{{ route('reports.monthly-revenue') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i> إعادة تعيين
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card revenue-card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">إجمالي الإيرادات</h5>
                    <div class="card-value">{{ number_format($summary['total_revenue'], 2) }} <small>{{ config('settings.currency_symbol', 'ر.س') }}</small></div>
                    <div class="card-change {{ $summary['revenue_change'] >= 0 ? 'positive' : 'negative' }}">
                        @if($summary['revenue_change'] != 0)
                            <i class="fas {{ $summary['revenue_change'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} me-1"></i>
                            {{ abs($summary['revenue_change']) }}% عن العام الماضي
                        @else
                            <i class="fas fa-equals me-1"></i> بدون تغيير
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card revenue-card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">متوسط الإيراد الشهري</h5>
                    <div class="card-value">{{ number_format($summary['avg_monthly_revenue'], 2) }} <small>{{ config('settings.currency_symbol', 'ر.س') }}</small></div>
                    <div class="card-change {{ $summary['avg_monthly_change'] >= 0 ? 'positive' : 'negative' }}">
                        @if($summary['avg_monthly_change'] != 0)
                            <i class="fas {{ $summary['avg_monthly_change'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} me-1"></i>
                            {{ abs($summary['avg_monthly_change']) }}% عن العام الماضي
                        @else
                            <i class="fas fa-equals me-1"></i> بدون تغيير
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card revenue-card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">أعلى شهر</h5>
                    <div class="card-value">{{ number_format($summary['highest_month']['revenue'] ?? 0, 2) }} <small>{{ config('settings.currency_symbol', 'ر.س') }}</small></div>
                    <div class="card-text">
                        <small>{{ $summary['highest_month']['month_name'] ?? 'لا توجد بيانات' }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card revenue-card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">أدنى شهر</h5>
                    <div class="card-value">{{ number_format($summary['lowest_month']['revenue'] ?? 0, 2) }} <small>{{ config('settings.currency_symbol', 'ر.س') }}</small></div>
                    <div class="card-text">
                        <small>{{ $summary['lowest_month']['month_name'] ?? 'لا توجد بيانات' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>مخطط الإيرادات الشهرية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>تفاصيل الإيرادات الشهرية
                    </h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="showDetails">
                        <label class="form-check-label" for="showDetails">عرض التفاصيل</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="revenueTable">
                            <thead class="table-light">
                                <tr>
                                    <th>الشهر</th>
                                    <th class="text-end">عدد الإيجارات</th>
                                    <th class="text-end">إجمالي الإيرادات</th>
                                    <th class="text-end">متوسط قيمة الإيجار</th>
                                    <th class="text-end">مقارنة بالشهر السابق</th>
                                    <th class="text-end">مقارنة بنفس الشهر العام الماضي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyData as $month => $data)
                                    <tr>
                                        <td>
                                            <strong>{{ $data['month_name'] }}</strong>
                                            <div class="month-details" style="display: none;">
                                                <small class="text-muted">
                                                    @if($data['car_type_name'])
                                                        <span class="badge bg-info">{{ $data['car_type_name'] }}</span>
                                                    @endif
                                                    @if($data['branch_name'])
                                                        <span class="badge bg-secondary">{{ $data['branch_name'] }}</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </td>
                                        <td class="text-end">{{ number_format($data['rentals_count']) }}</td>
                                        <td class="text-end">
                                            {{ number_format($data['revenue'], 2) }}
                                            <span class="text-muted">{{ config('settings.currency_symbol', 'ر.س') }}</span>
                                        </td>
                                        <td class="text-end">
                                            {{ $data['rentals_count'] > 0 ? number_format($data['revenue'] / $data['rentals_count'], 2) : '0.00' }}
                                            <span class="text-muted">{{ config('settings.currency_symbol', 'ر.س') }}</span>
                                        </td>
                                        <td class="text-end">
                                            @if(isset($data['previous_month_change']))
                                                <span class="badge {{ $data['previous_month_change'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                    <i class="fas {{ $data['previous_month_change'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} me-1"></i>
                                                    {{ abs($data['previous_month_change']) }}%
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">لا توجد بيانات</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if(isset($data['year_over_year_change']))
                                                <span class="badge {{ $data['year_over_year_change'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                    <i class="fas {{ $data['year_over_year_change'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} me-1"></i>
                                                    {{ abs($data['year_over_year_change']) }}%
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">لا توجد بيانات</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>المجموع/المتوسط</th>
                                    <th class="text-end">{{ number_format($summary['total_rentals']) }}</th>
                                    <th class="text-end">
                                        {{ number_format($summary['total_revenue'], 2) }}
                                        <span class="text-muted">{{ config('settings.currency_symbol', 'ر.س') }}</span>
                                    </th>
                                    <th class="text-end">
                                        {{ $summary['total_rentals'] > 0 ? number_format($summary['total_revenue'] / $summary['total_rentals'], 2) : '0.00' }}
                                        <span class="text-muted">{{ config('settings.currency_symbol', 'ر.س') }}</span>
                                    </th>
                                    <th colspan="2" class="text-center">
                                        <span class="badge bg-primary">
                                            <i class="fas {{ $summary['revenue_change'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} me-1"></i>
                                            {{ $summary['revenue_change'] }}% عن العام الماضي
                                        </span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($topPerformers) && (count($topPerformers['cars']) > 0 || count($topPerformers['customers']) > 0))
    <div class="row">
        @if(count($topPerformers['cars']) > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy text-warning me-2"></i>أفضل السيارات أداءً
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>السيارة</th>
                                    <th class="text-end">عدد الإيجارات</th>
                                    <th class="text-end">إجمالي الإيرادات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPerformers['cars'] as $index => $car)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ $car->name }}
                                            <div>
                                                <small class="text-muted">{{ $car->car_number }}</small>
                                                <span class="badge bg-info">{{ $car->carType->name ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end">{{ $car->rentals_count }}</td>
                                        <td class="text-end">
                                            {{ number_format($car->total_revenue, 2) }}
                                            <span class="text-muted">{{ config('settings.currency_symbol', 'ر.س') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(count($topPerformers['customers']) > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users text-primary me-2"></i>أفضل العملاء
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>العميل</th>
                                    <th class="text-end">عدد الإيجارات</th>
                                    <th class="text-end">إجمالي الإنفاق</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPerformers['customers'] as $index => $customer)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ $customer->name }}
                                            <div>
                                                <small class="text-muted">{{ $customer->phone }}</small>
                                                <span class="badge bg-secondary">{{ $customer->email }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end">{{ $customer->rentals_count }}</td>
                                        <td class="text-end">
                                            {{ number_format($customer->total_spent, 2) }}
                                            <span class="text-muted">{{ config('settings.currency_symbol', 'ر.س') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
<script src="{{ asset('vendor/daterangepicker/daterangepicker.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle month details
        const showDetailsCheckbox = document.getElementById('showDetails');
        const monthDetails = document.querySelectorAll('.month-details');
        
        showDetailsCheckbox.addEventListener('change', function() {
            monthDetails.forEach(detail => {
                detail.style.display = this.checked ? 'block' : 'none';
            });
        });

        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['months']) !!},
                datasets: [
                    {
                        label: 'الإيرادات الشهرية',
                        data: {!! json_encode($chartData['revenues']) !!},
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'العام الماضي',
                        data: {!! json_encode($chartData['previous_year_revenues'] ?? []) !!},
                        borderColor: 'rgba(108, 117, 125, 0.5)',
                        borderDash: [5, 5],
                        pointRadius: 0,
                        borderWidth: 1,
                        fill: false
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        rtl: true,
                        labels: {
                            font: {
                                family: 'Tajawal',
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14,
                            weight: 'bold',
                            family: 'Tajawal'
                        },
                        bodyFont: {
                            size: 13,
                            family: 'Tajawal'
                        },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('ar-SA', {
                                        style: 'decimal',
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }).format(context.parsed.y) + ' ' + '{{ config('settings.currency_symbol', 'ر.س') }}';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Tajawal'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                family: 'Tajawal'
                            },
                            callback: function(value) {
                                return new Intl.NumberFormat('ar-SA').format(value) + ' ' + '{{ config('settings.currency_symbol', 'ر.س') }}';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                family: 'Tajawal'
                            }
                        }
                    },
                    tooltip: {
                        titleFont: {
                            family: 'Tajawal',
                            weight: 'bold'
                        },
                        bodyFont: {
                            family: 'Tajawal'
                        },
                        footerFont: {
                            family: 'Tajawal'
                        }
                    }
                }
            }
        });

        // Initialize date range picker if needed
        if (document.getElementById('date_range')) {
            $('#date_range').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD',
                    applyLabel: 'تأكيد',
                    cancelLabel: 'إلغاء',
                    fromLabel: 'من',
                    toLabel: 'إلى',
                    customRangeLabel: 'مخصص',
                    daysOfWeek: ['أحد', 'إثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة', 'سبت'],
                    monthNames: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
                    firstDay: 6 // Start week on Saturday
                },
                opens: 'right',
                autoUpdateInput: false
            });

            // Update the hidden inputs when dates are selected
            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
                $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
            });

            // Clear the inputs when the clear button is clicked
            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('#start_date').val('');
                $('#end_date').val('');
            });
        }
    });
</script>
@endpush
