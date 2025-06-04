@extends('layouts.app')

@section('title', 'تفاصيل السيارة الخارجية: ' . $thirdPartyCar->car_number)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>تفاصيل السيارة الخارجية: {{ $thirdPartyCar->car_number }}</span>
                    <div>
                        <a href="{{ route('third-party-cars.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-right"></i> رجوع للقائمة
                        </a>
                        @if($thirdPartyCar->status === 'pending')
                            <a href="{{ route('third-party-cars.edit', $thirdPartyCar) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                        @endif
                        @can('approve_third_party_cars')
                            @if($thirdPartyCar->status === 'pending')
                                <form action="{{ route('third-party-cars.approve', $thirdPartyCar) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من اعتماد هذه السيارة؟')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> اعتماد
                                    </button>
                                </form>
                                <form action="{{ route('third-party-cars.reject', $thirdPartyCar) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من رفض هذه السيارة؟')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-times"></i> رفض
                                    </button>
                                </form>
                            @elseif($thirdPartyCar->status === 'approved')
                                <form action="{{ route('third-party-cars.complete', $thirdPartyCar) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إكمال خدمة هذه السيارة؟')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check-double"></i> إكمال الخدمة
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">معلومات السيارة</h5>
                                <span class="badge bg-{{ $thirdPartyCar->status === 'pending' ? 'warning' : ($thirdPartyCar->status === 'approved' ? 'info' : ($thirdPartyCar->status === 'completed' ? 'success' : 'danger')) }}">
                                    {{ $thirdPartyCar->status_label }}
                                </span>
                            </div>
                            <hr>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <h6 class="text-muted mb-1">رقم السيارة</h6>
                                <p class="mb-0">{{ $thirdPartyCar->car_number }}</p>
                            </div>

                            <div class="info-item mb-3">
                                <h6 class="text-muted mb-1">اسم السائق</h6>
                                <p class="mb-0">{{ $thirdPartyCar->driver_name }}</p>
                            </div>

                            <div class="info-item mb-3">
                                <h6 class="text-muted mb-1">رقم هاتف السائق</h6>
                                <p class="mb-0">{{ $thirdPartyCar->driver_phone }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <h6 class="text-muted mb-1">تاريخ الخدمة</h6>
                                <p class="mb-0">{{ $thirdPartyCar->service_date->format('Y-m-d') }}</p>
                            </div>

                            <div class="info-item mb-3">
                                <h6 class="text-muted mb-1">تاريخ الإنشاء</h6>
                                <p class="mb-0">{{ $thirdPartyCar->created_at->format('Y-m-d h:i A') }}</p>
                            </div>

                            @if($thirdPartyCar->status !== 'pending')
                                <div class="info-item mb-3">
                                    <h6 class="text-muted mb-1">تاريخ التحديث</h6>
                                    <p class="mb-0">{{ $thirdPartyCar->updated_at->format('Y-m-d h:i A') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">تفاصيل الرحلة</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>خط السير</th>
                                            <th>المسافة (كم)</th>
                                            <th>سعر الكيلومتر ({{ config('settings.currency_symbol', 'ر.س') }})</th>
                                            <th>إجمالي التكلفة ({{ config('settings.currency_symbol', 'ر.س') }})</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                {{ $thirdPartyCar->route->name ?? 'N/A' }}
                                                @if($thirdPartyCar->route)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $thirdPartyCar->route->start_point }} - {{ $thirdPartyCar->route->end_point }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>{{ number_format($thirdPartyCar->distance_km, 2) }}</td>
                                            <td>{{ number_format($thirdPartyCar->price_per_km, 2) }}</td>
                                            <td>{{ number_format($thirdPartyCar->total_cost, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($thirdPartyCar->status !== 'pending')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card border-{{ $thirdPartyCar->status === 'approved' ? 'info' : ($thirdPartyCar->status === 'completed' ? 'success' : 'danger') }}">
                                    <div class="card-header bg-{{ $thirdPartyCar->status === 'approved' ? 'info' : ($thirdPartyCar->status === 'completed' ? 'success' : 'danger') }} text-white">
                                        <h5 class="mb-0">
                                            @if($thirdPartyCar->status === 'approved')
                                                <i class="fas fa-check-circle"></i> تفاصيل الاعتماد
                                            @elseif($thirdPartyCar->status === 'completed')
                                                <i class="fas fa-check-double"></i> تفاصيل الإكمال
                                            @else
                                                <i class="fas fa-times-circle"></i> تفاصيل الرفض
                                            @endif
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-item mb-3">
                                                    <h6 class="text-muted mb-1">بواسطة</h6>
                                                    <p class="mb-0">{{ $thirdPartyCar->supervisor->name ?? 'غير معروف' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-item mb-3">
                                                    <h6 class="text-muted mb-1">التاريخ والوقت</h6>
                                                    <p class="mb-0">
                                                        @if($thirdPartyCar->status === 'approved')
                                                            {{ $thirdPartyCar->approved_at ? $thirdPartyCar->approved_at->format('Y-m-d h:i A') : 'غير معروف' }}
                                                        @elseif($thirdPartyCar->status === 'completed')
                                                            {{ $thirdPartyCar->completed_at ? $thirdPartyCar->completed_at->format('Y-m-d h:i A') : 'غير معروف' }}
                                                        @else
                                                            {{ $thirdPartyCar->cancelled_at ? $thirdPartyCar->cancelled_at->format('Y-m-d h:i A') : 'غير معروف' }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            @if($thirdPartyCar->status === 'cancelled' && $thirdPartyCar->cancellation_reason)
                                                <div class="col-12">
                                                    <div class="info-item">
                                                        <h6 class="text-muted mb-1">سبب الرفض</h6>
                                                        <p class="mb-0">{{ $thirdPartyCar->cancellation_reason }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($thirdPartyCar->notes)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">ملاحظات إضافية</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $thirdPartyCar->notes }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card-footer text-muted">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small>تم الإنشاء في {{ $thirdPartyCar->created_at->format('Y-m-d h:i A') }}</small>
                        </div>
                        <div>
                            <small>آخر تحديث {{ $thirdPartyCar->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .info-item {
        margin-bottom: 1rem;
    }
    .info-item h6 {
        font-size: 0.85rem;
        font-weight: 600;
    }
    .info-item p {
        font-size: 1rem;
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Print button functionality
        const printButton = document.getElementById('printBtn');
        if (printButton) {
            printButton.addEventListener('click', function() {
                window.print();
            });
        }
    });
</script>
@endpush
@endsection
