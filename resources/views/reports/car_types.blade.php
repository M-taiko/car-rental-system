@extends('layouts.app')

@section('title', 'تقرير أنواع السيارات')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/chart.js/Chart.min.css') }}">
<style>
    .card {
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
    }
    .card-header {
        background-color: #f8f9fa;
        padding: 15px 20px;
        font-weight: 600;
    }
    .card-body {
        padding: 20px;
    }
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .chart-container {
        position: relative;
        margin: auto;
        height: 300px;
    }
    .summary-card {
        border-left: 4px solid #0d6efd;
        transition: all 0.3s ease;
    }
    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    .summary-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: #0d6efd;
    }
    .summary-label {
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">
                    <i class="fas fa-car-side me-2"></i>تقرير أنواع السيارات
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
                        <li class="breadcrumb-item active">تقرير أنواع السيارات</li>
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
                    <div>
                        <a href="{{ route('reports.export', ['type' => 'car-types', 'format' => 'excel']) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel me-1"></i> تصدير إكسل
                        </a>
                        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> رجوع
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.car-types') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date_range" class="form-label">الفترة الزمنية</label>
                                    <input type="text" class="form-control" id="date_range" name="date_range" 
                                           value="{{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('Y-m-d') . ' - ' . \Carbon\Carbon::parse(request('end_date'))->format('Y-m-d') : '' }}" 
                                           placeholder="اختر الفترة">
                                    <input type="hidden" name="start_date" id="start_date" value="{{ request('start_date') }}">
                                    <input type="hidden" name="end_date" id="end_date" value="{{ request('end_date') }}">
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
                                    <label for="status" class="form-label">حالة الإيجار</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">الكل</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> تطبيق الفلتر
                                </button>
                                <a href="{{ route('reports.car-types') }}" class="btn btn-outline-secondary">
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
            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="summary-label">إجمالي أنواع السيارات</h6>
                            <div class="summary-value">{{ $stats['total_car_types'] }}</div>
                        </div>
                        <div class="bg-soft-primary rounded p-3">
                            <i class="fas fa-car-side text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="summary-label">إجمالي السيارات</h6>
                            <div class="summary-value">{{ $stats['total_cars'] }}</div>
                        </div>
                        <div class="bg-soft-success rounded p-3">
                            <i class="fas fa-car text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="summary-label">متوسط الإيجار/نوع</h6>
                            <div class="summary-value">{{ number_format($stats['avg_rentals_per_type'], 1) }}</div>
                        </div>
                        <div class="bg-soft-info rounded p-3">
                            <i class="fas fa-chart-line text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="summary-label">متوسط الإيراد/نوع</h6>
                            <div class="summary-value">
                                {{ number_format($stats['avg_revenue_per_type'], 2) }}
                                <small class="text-muted">{{ config('settings.currency_symbol', 'ر.س') }}</small>
                            </div>
                        </div>
                        <div class="bg-soft-warning rounded p-3">
                            <i class="fas fa-money-bill-wave text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>توزيع الإيرادات حسب نوع السيارة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>أعلى 5 أنواع من حيث الإيرادات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="topTypesChart"></canvas>
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
                        <i class="fas fa-table me-2"></i>تفاصيل أنواع السيارات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>نوع السيارة</th>
                                    <th>عدد السيارات</th>
                                    <th>عدد الإيجارات</th>
                                    <th>متوسط مدة الإيجار</th>
                                    <th>متوسط السعر/يوم</th>
                                    <th>إجمالي الإيرادات</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carTypesData as $carType)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $carType->name }}
                                            @if($carType->description)
                                                <small class="d-block text-muted">{{ Str::limit($carType->description, 30) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $carType->cars_count ?? 0 }}</td>
                                        <td>{{ $carType->rentals_count ?? 0 }}</td>
                                        <td>{{ number_format($carType->avg_rental_days ?? 0, 1) }} يوم</td>
                                        <td>
                                            {{ number_format($carType->avg_daily_rate ?? 0, 2) }}
                                            <span class="text-muted">{{ config('settings.currency_symbol', 'ر.س') }}</span>
                                        </td>
                                        <td>
                                            {{ number_format($carType->total_revenue ?? 0, 2) }}
                                            <span class="text-muted">{{ config('settings.currency_symbol', 'ر.س') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $carType->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $carType->is_active ? 'نشط' : 'غير نشط' }}
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
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
<script src="{{ asset('vendor/daterangepicker/daterangepicker.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize date range picker
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

        // Initialize any existing date range
        @if(request('start_date') && request('end_date'))
            $('#date_range').val('{{ request('start_date') }} - {{ request('end_date') }}');
        @endif

        // Revenue Distribution Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData['revenue_labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['revenue_data']) !!},
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#5a5c69', '#858796', '#3a3b45', '#1cc88a', '#36b9cc'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        rtl: true,
                        labels: {
                            font: {
                                family: 'Tajawal',
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Top Types Chart
        const topTypesCtx = document.getElementById('topTypesChart').getContext('2d');
        const topTypesChart = new Chart(topTypesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['top_types_labels']) !!},
                datasets: [{
                    label: 'إجمالي الإيرادات',
                    data: {!! json_encode($chartData['top_types_data']) !!},
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' ' + '{{ config('settings.currency_symbol', 'ر.س') }}';
                            }
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                family: 'Tajawal',
                                size: 12
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw.toLocaleString() + ' ' + '{{ config('settings.currency_symbol', 'ر.س') }}';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
