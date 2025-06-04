@extends('layouts.app')

@section('title', 'لوحة التقارير')

@push('styles')
<!-- Internal Chart-Mor css -->
<link href="{{ asset('assets/plugins/chart-mor/css/morris.css') }}" rel="stylesheet">
<!-- Internal Chart.js css -->
<link href="{{ asset('assets/plugins/chart.js/Chart.min.css') }}" rel="stylesheet">
@endpush

@section('content')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <span class="main-content-title mg-b-0 mg-b-lg-1">لوحة التقارير</span>
    </div>
    <div class="justify-content-center mt-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active" aria-current="page">التقارير</li>
        </ol>
    </div>
</div>
<!-- /breadcrumb -->

<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mg-b-0">
                        <i class="fe fe-pie-chart me-2"></i>نظرة عامة على التقارير
                    </h4>
                </div>
                <p class="tx-12 tx-gray-500 mb-0">عرض شامل لإحصائيات وتقارير النظام</p>
            </div>

                <div class="card-body">
                <div class="row">
                    <!-- إحصائيات سريعة -->
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">إجمالي الإيجارات</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-0">{{ number_format($stats['total_rentals']) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 my-auto">
                                    <div class="card-body text-center">
                                        <i class="fe fe-car tx-40 text-primary"></i>
                                    </div>
                                </div>
                                <a href="{{ route('reports.rentals') }}" class="text-muted">
                                    <div class="card-footer text-center bg-primary text-white">
                                        عرض التفاصيل <i class="fe fe-arrow-left me-1"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">إجمالي السيارات الخارجية</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-0">{{ number_format($stats['total_third_party_cars']) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 my-auto">
                                    <div class="card-body text-center">
                                        <i class="fe fe-truck tx-40 text-success"></i>
                                    </div>
                                </div>
                                <a href="{{ route('reports.third-party-cars') }}" class="text-muted">
                                    <div class="card-footer text-center bg-success text-white">
                                        عرض التفاصيل <i class="fe fe-arrow-left me-1"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                        <div class="card sales-card">
                            <div class="row">
                                <div class="col-8">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <div class="">
                                            <h6 class="mb-2 tx-12">إجمالي الإيرادات</h6>
                                        </div>
                                        <div class="pb-0 mt-0">
                                            <div class="d-flex">
                                                <h4 class="tx-20 font-weight-semibold mb-0">{{ number_format($stats['total_revenue'], 2) }} {{ config('settings.currency_symbol', 'ر.س') }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 my-auto">
                                    <div class="card-body text-center">
                                        <i class="fe fe-dollar-sign tx-40 text-info"></i>
                                    </div>
                                </div>
                                <a href="{{ route('reports.revenue') }}" class="text-muted">
                                    <div class="card-footer text-center bg-info text-white">
                                        عرض التفاصيل <i class="fe fe-arrow-left me-1"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    </div>

                </div>

                <div class="row">
                    <!-- تقارير سريعة -->
                    <div class="col-xl-6 col-lg-12">
                        <div class="card custom-card">
                            <div class="card-header border-bottom-0 pb-0">
                                <h5 class="card-title mb-0">
                                    <i class="fe fe-file-text me-2"></i>تقارير الإيجارات
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="{{ route('reports.rentals', ['status' => 'active']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span><i class="fe fe-activity text-primary me-2"></i> الإيجارات النشطة</span>
                                        <span class="badge bg-primary rounded-pill">{{ $stats['active_rentals'] }}</span>
                                    </a>
                                    <a href="{{ route('reports.rentals', ['status' => 'upcoming']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span><i class="fe fe-clock text-warning me-2"></i> الإيجارات القادمة</span>
                                        <span class="badge bg-warning text-dark rounded-pill">{{ $stats['upcoming_rentals'] }}</span>
                                    </a>
                                    <a href="{{ route('reports.rentals', ['status' => 'completed']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span><i class="fe fe-check-circle text-success me-2"></i> الإيجارات المكتملة</span>
                                        <span class="badge bg-success rounded-pill">{{ $stats['completed_rentals'] }}</span>
                                    </a>
                                    <a href="{{ route('reports.rentals', ['status' => 'cancelled']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span><i class="fe fe-x-circle text-danger me-2"></i> الإيجارات الملغاة</span>
                                        <span class="badge bg-danger rounded-pill">{{ $stats['cancelled_rentals'] }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-12">
                        <div class="card custom-card">
                            <div class="card-header border-bottom-0 pb-0">
                                <h5 class="card-title mb-0">
                                    <i class="fe fe-truck me-2"></i>تقارير السيارات الخارجية
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="{{ route('reports.third-party-cars', ['status' => 'pending']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span><i class="fe fe-clock text-warning me-2"></i> بانتظار الموافقة</span>
                                        <span class="badge bg-warning text-dark rounded-pill">{{ $stats['pending_third_party_cars'] }}</span>
                                    </a>
                                    <a href="{{ route('reports.third-party-cars', ['status' => 'approved']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span><i class="fe fe-check text-info me-2"></i> المعتمدة</span>
                                        <span class="badge bg-info rounded-pill">{{ $stats['approved_third_party_cars'] }}</span>
                                    </a>
                                    <a href="{{ route('reports.third-party-cars', ['status' => 'completed']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span><i class="fe fe-check-circle text-success me-2"></i> المكتملة</span>
                                        <span class="badge bg-success rounded-pill">{{ $stats['completed_third_party_cars'] }}</span>
                                    </a>
                                    <a href="{{ route('reports.third-party-cars', ['status' => 'cancelled']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span><i class="fe fe-x-circle text-danger me-2"></i> الملغاة</span>
                                        <span class="badge bg-danger rounded-pill">{{ $stats['cancelled_third_party_cars'] }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom-0 pb-0">
                                <h5 class="card-title mb-0">
                                    <i class="fe fe-file-text me-2"></i>تقارير متقدمة
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-md-6">
                                        <div class="card custom-card">
                                            <div class="card-body text-center p-4">
                                                <div class="mb-3">
                                                    <span class="avatar avatar-lg rounded-circle bg-primary-transparent text-primary">
                                                        <i class="fe fe-truck fs-20"></i>
                                                    </span>
                                                </div>
                                                <h5 class="mb-2">تقرير السيارات حسب النوع</h5>
                                                <p class="text-muted mb-3">عرض إحصائيات الإيجارات حسب نوع السيارة</p>
                                                <a href="{{ route('reports.car-types') }}" class="btn btn-primary btn-sm">
                                                    <i class="fe fe-eye me-1"></i>عرض التقرير
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-6">
                                        <div class="card custom-card">
                                            <div class="card-body text-center p-4">
                                                <div class="mb-3">
                                                    <span class="avatar avatar-lg rounded-circle bg-success-transparent text-success">
                                                        <i class="fe fe-dollar-sign fs-20"></i>
                                                    </span>
                                                </div>
                                                <h5 class="mb-2">تقرير الإيرادات الشهرية</h5>
                                                <p class="text-muted mb-3">عرض الإيرادات والمقارنة بين الأشهر</p>
                                                <a href="{{ route('reports.monthly-revenue') }}" class="btn btn-success btn-sm">
                                                    <i class="fe fe-eye me-1"></i>عرض التقرير
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-6">
                                        <div class="card custom-card">
                                            <div class="card-body text-center p-4">
                                                <div class="mb-3">
                                                    <span class="avatar avatar-lg rounded-circle bg-info-transparent text-info">
                                                        <i class="fe fe-download-cloud fs-20"></i>
                                                    </span>
                                                </div>
                                                <h5 class="mb-2">تصدير البيانات</h5>
                                                <p class="text-muted mb-3">تصدير بيانات التقارير بصيغة Excel أو PDF</p>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fe fe-download me-1"></i>تصدير
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('reports.export', ['type' => 'rentals', 'format' => 'excel']) }}">
                                                                <i class="fe fe-download-cloud text-success me-2"></i>إكسل
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('reports.export', ['type' => 'rentals', 'format' => 'pdf']) }}">
                                                                <i class="fe fe-file-text text-danger me-2"></i>PDF
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: all 0.3s ease;
        border: 0;
        border-radius: 8px;
        box-shadow: 0 0 5px rgba(28, 39, 60, 0.08);
        margin-bottom: 20px;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(28, 39, 60, 0.1);
    }
    
    .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 20px 20px 10px;
    }
    
    .card-title {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 0;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .list-group-item {
        border-left: 0;
        border-right: 0;
        padding: 12px 20px;
        border-color: rgba(0, 0, 0, 0.03);
    }
    
    .list-group-item:first-child {
        border-top: 0;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        border-radius: 50px;
    }
    
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }
    
    .avatar-lg {
        width: 60px;
        height: 60px;
        font-size: 1.75rem;
    }
    
    .text-primary-transparent {
        color: rgba(98, 89, 202, 0.2);
    }
    
    .text-success-transparent {
        color: rgba(45, 206, 137, 0.2);
    }
    
    .text-info-transparent {
        color: rgba(23, 162, 184, 0.2);
    }
    
    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
    }
    
    .dropdown-menu {
        border: 0;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 10px 0;
    }
    
    .dropdown-item {
        padding: 8px 20px;
        font-size: 0.85rem;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
