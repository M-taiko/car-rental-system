@extends('layouts.app')

@section('title', 'تقرير السيارات الخارجية')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
<style>
    .filter-card {
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
    }
    .filter-card .card-header {
        background-color: #f8f9fa;
        padding: 10px 15px;
        font-weight: 600;
    }
    .filter-card .card-body {
        padding: 15px;
    }
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        white-space: nowrap;
    }
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    .export-buttons .btn {
        margin-left: 5px;
    }
    .status-badge {
        min-width: 80px;
        display: inline-block;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-truck-pickup me-2"></i>تقرير السيارات الخارجية</h5>
                    <div class="export-buttons">
                        <a href="{{ route('reports.export', ['type' => 'third-party-cars', 'format' => 'excel']) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel me-1"></i> تصدير إكسل
                        </a>
                        <a href="{{ route('reports.export', ['type' => 'third-party-cars', 'format' => 'pdf']) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                           class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf me-1"></i> تصدير PDF
                        </a>
                        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> رجوع
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('reports.third-party-cars') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="filter-card">
                                    <div class="card-header">
                                        <i class="fas fa-filter me-1"></i>تصفية النتائج
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">حالة الخدمة</label>
                                            <select name="status" id="status" class="form-select">
                                                <option value="">الكل</option>
                                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="route_id" class="form-label">خط السير</label>
                                            <select name="route_id" id="route_id" class="form-select">
                                                <option value="">الكل</option>
                                                @foreach($routes as $route)
                                                    <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>
                                                        {{ $route->name }} ({{ $route->start_point }} - {{ $route->end_point }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="date_range" class="form-label">الفترة الزمنية</label>
                                            <input type="text" class="form-control" id="date_range" name="date_range" 
                                                   value="{{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('Y-m-d') . ' - ' . \Carbon\Carbon::parse(request('end_date'))->format('Y-m-d') : '' }}" 
                                                   placeholder="اختر الفترة">
                                            <input type="hidden" name="start_date" id="start_date" value="{{ request('start_date') }}">
                                            <input type="hidden" name="end_date" id="end_date" value="{{ request('end_date') }}">
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search me-1"></i> بحث
                                            </button>
                                            <a href="{{ route('reports.third-party-cars') }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-undo me-1"></i> إعادة تعيين
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="card">
                                    <div class="card-body">
                                        @if($thirdPartyCars->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>رقم السيارة</th>
                                                            <th>اسم السائق</th>
                                                            <th>خط السير</th>
                                                            <th>التاريخ</th>
                                                            <th>المسافة (كم)</th>
                                                            <th>التكلفة</th>
                                                            <th>الحالة</th>
                                                            <th>الإجراءات</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($thirdPartyCars as $car)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $car->car_number }}</td>
                                                                <td>{{ $car->driver_name }}</td>
                                                                <td>
                                                                    {{ $car->route->name ?? 'N/A' }}
                                                                    @if($car->route)
                                                                        <small class="d-block text-muted">
                                                                            {{ $car->route->start_point }} - {{ $car->route->end_point }}
                                                                        </small>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $car->service_date->format('Y-m-d') }}</td>
                                                                <td>{{ number_format($car->distance_km, 2) }}</td>
                                                                <td>
                                                                    {{ number_format($car->total_cost, 2) }}
                                                                    <span class="text-muted">{{ config('settings.currency_symbol', 'ر.س') }}</span>
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $statusClasses = [
                                                                            'pending' => 'bg-warning',
                                                                            'approved' => 'bg-info',
                                                                            'completed' => 'bg-success',
                                                                            'cancelled' => 'bg-danger'
                                                                        ];
                                                                        $statusLabels = [
                                                                            'pending' => 'قيد الانتظار',
                                                                            'approved' => 'معتمد',
                                                                            'completed' => 'مكتمل',
                                                                            'cancelled' => 'ملغي'
                                                                        ];
                                                                    @endphp
                                                                    <span class="badge status-badge {{ $statusClasses[$car->status] ?? 'bg-secondary' }}">
                                                                        {{ $statusLabels[$car->status] ?? $car->status }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('third-party-cars.show', $car->id) }}" class="btn btn-sm btn-info" title="عرض">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    @if($car->status === 'pending')
                                                                        <a href="{{ route('third-party-cars.edit', $car->id) }}" class="btn btn-sm btn-primary" title="تعديل">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="5" class="text-end">المجموع:</th>
                                                            <th>{{ number_format($thirdPartyCars->sum('distance_km'), 2) }} كم</th>
                                                            <th colspan="3">
                                                                {{ number_format($thirdPartyCars->sum('total_cost'), 2) }}
                                                                <span class="text-muted">{{ config('settings.currency_symbol', 'ر.س') }}</span>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                            <div class="mt-3">
                                                {{ $thirdPartyCars->withQueryString()->links() }}
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <h5>لا توجد نتائج</h5>
                                                <p class="text-muted">لم يتم العثور على سيارات خارجية تطابق معايير البحث المحددة</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if(isset($summary))
                                    <div class="card mt-4">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>ملخص النتائج</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3 col-6 mb-3">
                                                    <div class="border rounded p-3 text-center">
                                                        <div class="text-muted mb-1">إجمالي السيارات</div>
                                                        <h4 class="mb-0">{{ $summary['total_cars'] }}</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6 mb-3">
                                                    <div class="border rounded p-3 text-center">
                                                        <div class="text-muted mb-1">إجمالي المسافة</div>
                                                        <h4 class="mb-0">{{ number_format($summary['total_distance'], 2) }} كم</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6 mb-3">
                                                    <div class="border rounded p-3 text-center">
                                                        <div class="text-muted mb-1">متوسط التكلفة</div>
                                                        <h4 class="mb-0">
                                                            {{ number_format($summary['average_cost'], 2) }}
                                                            <small class="text-muted">{{ config('settings.currency_symbol', 'ر.س') }}</small>
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6 mb-3">
                                                    <div class="border rounded p-3 text-center">
                                                        <div class="text-muted mb-1">إجمالي التكاليف</div>
                                                        <h4 class="mb-0">
                                                            {{ number_format($summary['total_cost'], 2) }}
                                                            <small class="text-muted">{{ config('settings.currency_symbol', 'ر.س') }}</small>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
    });
</script>
@endpush
