@extends('layouts.master', ['title' => 'تقرير الإيجارات'])

@push('styles')
<!-- Internal Data table css -->
<link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/plugins/datatable/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" />

<!-- Daterangepicker css -->
<link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

<!-- Select2 css -->
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

<!-- Internal Sweet-Alert css-->
<link rel="stylesheet" href="{{ asset('assets/plugins/sweet-alert/sweetalert.css') }}">

<style>
    .filter-card {
        margin-bottom: 20px;
        border: 1px solid #e8e8f7;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 0 10px rgba(28, 39, 60, 0.05);
    }
    .filter-card .card-header {
        background-color: #f8f9fa;
        padding: 12px 20px;
        font-weight: 600;
        border-bottom: 1px solid #e8e8f7;
    }
    .filter-card .card-body {
        padding: 20px;
    }
    .table > :not(caption) > * > * {
        padding: 12px 15px;
    }
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        font-size: 85%;
    }
    .btn-icon {
        width: 30px;
        height: 30px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .export-buttons .btn {
        margin-left: 5px;
        margin-bottom: 5px;
    }
    .card {
        border: 1px solid #e8e8f7;
        box-shadow: 0 0 10px rgba(28, 39, 60, 0.05);
    }
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #e8e8f7;
    }
</style>
@endpush

@section('content')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <span class="main-content-title mg-b-0 mg-b-lg-1">تقرير الإيجارات</span>
    </div>
    <div class="justify-content-center mt-2">
        <div class="btn-group" role="group">
            <a href="{{ route('reports.export', ['type' => 'rentals', 'format' => 'excel']) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
               class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="تصدير إكسل">
                <i class="fe fe-download me-1"></i> إكسل
            </a>
            <a href="{{ route('reports.export', ['type' => 'rentals', 'format' => 'pdf']) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
               class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="تصدير PDF">
                <i class="fe fe-file-text me-1"></i> PDF
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="رجوع">
                <i class="fe fe-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
</div>
<!-- /breadcrumb -->

<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mg-b-0">
                        <i class="fe fe-car me-2"></i>تقرير الإيجارات
                    </h4>
                </div>
                <p class="tx-12 tx-gray-500 mb-0">عرض وتصفية سجلات الإيجارات</p>
            </div>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('reports.rentals') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="filter-card">
                                <div class="card-header">
                                    <i class="fe fe-filter me-1"></i>تصفية النتائج
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="status" class="form-label">حالة الإيجار</label>
                                        <select name="status" id="status" class="form-control form-select">
                                            <option value="">الكل</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                        </select>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="car_type_id" class="form-label">نوع السيارة</label>
                                        <select name="car_type_id" id="car_type_id" class="form-control form-select select2">
                                            <option value="">الكل</option>
                                            @foreach($carTypes as $type)
                                                <option value="{{ $type->id }}" {{ request('car_type_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="date_range" class="form-label">الفترة الزمنية</label>
                                        <div class="input-group">
                                            <div class="input-group-text">
                                                <i class="fe fe-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control" id="date_range" name="date_range" 
                                                value="{{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('Y-m-d') . ' - ' . \Carbon\Carbon::parse(request('end_date'))->format('Y-m-d') : '' }}" 
                                                placeholder="اختر الفترة">
                                            <input type="hidden" name="start_date" id="start_date" value="{{ request('start_date') }}">
                                            <input type="hidden" name="end_date" id="end_date" value="{{ request('end_date') }}">
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fe fe-search me-1"></i> بحث
                                        </button>
                                        <a href="{{ route('reports.rentals') }}" class="btn btn-outline-secondary">
                                            <i class="fe fe-rotate-ccw me-1"></i> إعادة تعيين
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="col-md-9">
                                <div class="card">
                                    <div class="card-body">
                                        @if($rentals->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered text-nowrap border-bottom" id="rentals-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="wd-5p">#</th>
                                                            <th class="wd-10p">رقم الحجز</th>
                                                            <th class="wd-20p">السيارة</th>
                                                            <th class="wd-15p">العميل</th>
                                                            <th class="wd-10p">تاريخ البداية</th>
                                                            <th class="wd-10p">تاريخ النهاية</th>
                                                            <th class="wd-10p">المجموع</th>
                                                            <th class="wd-10p">الحالة</th>
                                                            <th class="wd-10p">الإجراءات</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($rentals as $rental)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>#{{ $rental->id }}</td>
                                                                <td>
                                                                    @if($rental->car)
                                                                        {{ $rental->car->brand }} {{ $rental->car->model }} ({{ $rental->car->plate_number }})
                                                                    @else
                                                                        - 
                                                                    @endif
                                                                </td>
                                                                <td>{{ $rental->customer_name ?? '-' }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($rental->start_date)->format('Y-m-d') }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($rental->end_date)->format('Y-m-d') }}</td>
                                                                <td>{{ number_format($rental->total_cost, 2) }} ر.س</td>
                                                                <td>
                                                                    @php
                                                                        $statusClass = [
                                                                            'pending' => 'badge bg-warning',
                                                                            'confirmed' => 'badge bg-info',
                                                                            'active' => 'badge bg-primary',
                                                                            'completed' => 'badge bg-success',
                                                                            'cancelled' => 'badge bg-danger'
                                                                        ][$rental->status] ?? 'badge bg-secondary';
                                                                    @endphp
                                                                    <span class="{{ $statusClass }}">
                                                                        {{ __(ucfirst($rental->status)) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-list">
                                                                        <a href="{{ route('rentals.show', $rental->id) }}" class="btn btn-sm btn-icon btn-info" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                                                            <i class="fe fe-eye"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="mt-3">
                                                <div class="d-flex justify-content-center">
                                                    {{ $rentals->withQueryString()->links() }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="fe fe-inbox fe-5x text-muted mb-3"></i>
                                                <h5>لا توجد نتائج</h5>
                                                <p class="text-muted">لم يتم العثور على إيجارات تطابق معايير البحث المحددة</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if(isset($summary))
                                    <div class="card mt-4">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="fe fe-pie-chart me-2"></i>ملخص الإحصائيات</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="card bg-light">
                                                        <div class="card-body text-center">
                                                            <h6 class="text-muted">إجمالي الإيرادات</h6>
                                                            <h4 class="mb-0">{{ number_format($summary['total_revenue'], 2) }} ر.س</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card bg-light">
                                                        <div class="card-body text-center">
                                                            <h6 class="text-muted">إجمالي الإيجارات</h6>
                                                            <h4 class="mb-0">{{ $summary['total_rentals'] }}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card bg-light">
                                                        <div class="card-body text-center">
                                                            <h6 class="text-muted">متوسط مدة الإيجار</h6>
                                                            <h4 class="mb-0">{{ $summary['avg_rental_days'] }} أيام</h4>
                                                        </div>
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
<!-- Internal Data tables -->
<script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/responsive.bootstrap5.min.js') }}"></script>

<!-- Daterangepicker js -->
<script src="{{ asset('assets/plugins/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

<!-- Select2 js -->
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/js/select2-lang/ar.js') }}"></script>

<!-- Internal Sweet-Alert js-->
<script src="{{ asset('assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>

<script>
    $(function() {
        'use strict';
        
        // Initialize Select2
        $('.select2').select2({
            placeholder: 'اختر...',
            allowClear: true,
            dir: 'rtl',
            language: 'ar',
            width: '100%'
        });

        // Date Range Picker
        $('#date_range').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: 'تطبيق',
                cancelLabel: 'إلغاء',
                fromLabel: 'من',
                toLabel: 'إلى',
                customRangeLabel: 'مخصص',
                weekLabel: 'أسبوع',
                daysOfWeek: ['أحد', 'إثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة', 'سبت'],
                monthNames: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
                firstDay: 6 // Start with Saturday
            },
            autoUpdateInput: false,
            autoApply: true,
            opens: 'right',
            startDate: '{{ request('start_date') ?: \\Carbon\\Carbon::now()->startOfMonth()->format('Y-m-d') }}',
            endDate: '{{ request('end_date') ?: \\Carbon\\Carbon::now()->format('Y-m-d') }}'
        });

        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
            $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
        });

        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#start_date').val('');
            $('#end_date').val('');
        });

        // Initialize DataTable
        var table = $('#rentals-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('reports.rentals.data') }}',
                type: 'GET',
                data: function (d) {
                    return {
                        status: $('#status').val(),
                        car_type_id: $('#car_type_id').val(),
                        start_date: $('#start_date').val(),
                        end_date: $('#end_date').val(),
                        draw: d.draw,
                        start: d.start,
                        length: d.length,
                        search: d.search,
                        order: d.order
                    };
                },
                dataSrc: function (json) {
                    if (json.error) {
                        console.error('Server error:', json.error);
                        swal({
                            title: 'خطأ',
                            text: json.error || 'حدث خطأ أثناء تحميل البيانات. يرجى المحاولة مرة أخرى.',
                            type: 'error',
                            confirmButtonText: 'حسناً'
                        });
                        return [];
                    }
                    return json.data || [];
                },
                error: function(xhr, error, code) {
                    console.error('DataTables error:', error);
                    console.error('Status:', xhr.status);
                    console.error('Response:', xhr.responseText);
                    
                    let errorMessage = 'حدث خطأ أثناء تحميل البيانات. يرجى المحاولة مرة أخرى.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMessage = response.error;
                        }
                    } catch (e) {
                        // If we can't parse the response, use the default error message
                    }
                    
                    swal({
                        title: 'خطأ',
                        text: errorMessage,
                        type: 'error',
                        confirmButtonText: 'حسناً'
                    });
                    
                    return [];
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'id', name: 'id'},
                {data: 'car', name: 'car'},
                {data: 'customer_name', name: 'customer_name'},
                {data: 'start_date', name: 'start_date'},
                {data: 'end_date', name: 'end_date'},
                {data: 'total_cost', name: 'total_cost', render: function(data) {
                    return parseFloat(data).toFixed(2) + ' ر.س';
                }},
                {data: 'status', name: 'status', render: function(data) {
                    var statusClasses = {
                        'pending': 'badge bg-warning',
                        'confirmed': 'badge bg-info',
                        'active': 'badge bg-success',
                        'completed': 'badge bg-primary',
                        'cancelled': 'badge bg-danger'
                    };
                    var statusText = {
                        'pending': 'قيد الانتظار',
                        'confirmed': 'مؤكد',
                        'active': 'نشط',
                        'completed': 'مكتمل',
                        'cancelled': 'ملغي'
                    };
                    return '<span class="' + (statusClasses[data] || 'badge bg-secondary') + '">' + (statusText[data] || data) + '</span>';
                }},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'}
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json',
                search: 'بحث:',
                lengthMenu: 'عرض _MENU_ سجل',
                info: 'عرض _START_ إلى _END_ من _TOTAL_ إدخال',
                infoEmpty: 'لا توجد سجلات متاحة',
                infoFiltered: '(تمت تصفيته من _MAX_ إدخال إجمالي)',
                paginate: {
                    first: 'الأول',
                    last: 'الأخير',
                    next: 'التالي',
                    previous: 'السابق'
                },
                emptyTable: 'لا توجد بيانات متاحة في الجدول',
                loadingRecords: 'جاري التحميل...',
                processing: 'جاري المعالجة...',
                zeroRecords: 'لم يتم العثور على سجلات متطابقة',
                searchPlaceholder: 'ابحث...',
                select: {
                    rows: {
                        _: '%d صفوف محددة',
                        0: 'انقر على صف لتحديده',
                        1: 'صف واحد محدد'
                    }
                }
            },
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'الكل']],
            pageLength: 10,
            responsive: true,
            order: [[1, 'desc']], // Default sort by ID desc
            drawCallback: function() {
                // Initialize tooltips after table is drawn
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            initComplete: function() {
                // Add any initialization complete logic here
                var api = this.api();
                
                // Add placeholder to search input
                $('.dataTables_filter input')
                    .attr('placeholder', 'ابحث...')
                    .addClass('form-control form-control-sm')
                    .css('width', '250px', 'display', 'inline-block');
                    
                // Add class to length select
                $('.dataTables_length select').addClass('form-select form-select-sm');
            }
        });

        // Filter form submit
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            table.draw();
        });

        // Reset filters
        $('#resetFilters').on('click', function() {
            $('#status').val('').trigger('change');
            $('#car_type_id').val('').trigger('change');
            $('#date_range').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            table.draw();
        });
        
        // Handle date range change
        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
            $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
            table.draw();
        });
        
        // Handle select changes
        $('#status, #car_type_id').on('change', function() {
            table.draw();
        });
        
        // Handle date range change
        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
            $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
            table.draw();
        });
        
        // Handle select changes
        $('#status, #car_type_id').on('change', function() {
            table.draw();
        });
    });
</script>
@endpush
