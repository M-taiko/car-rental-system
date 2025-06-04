@extends('layouts.app')

@section('title', 'إدارة السيارات الخارجية')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>إدارة السيارات الخارجية</span>
                    <a href="{{ route('third-party-cars.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة سيارة خارجية
                    </a>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('third-party-cars.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">الحالة</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">من تاريخ</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">إلى تاريخ</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter"></i> تصفية
                                </button>
                                <a href="{{ route('third-party-cars.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> إعادة تعيين
                                </a>
                            </div>
                        </div>
                    </form>

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

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
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
                                @forelse ($thirdPartyCars as $car)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $car->car_number }}</td>
                                        <td>{{ $car->driver_name }}</td>
                                        <td>{{ $car->route->name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($car->service_date)->format('Y-m-d') }}</td>
                                        <td>{{ number_format($car->distance_km, 2) }}</td>
                                        <td>{{ number_format($car->total_cost, 2) }} {{ config('settings.currency_symbol', 'ر.س') }}</td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'bg-warning',
                                                    'approved' => 'bg-info',
                                                    'completed' => 'bg-success',
                                                    'cancelled' => 'bg-danger'
                                                ];
                                                $statusClass = $statusClasses[$car->status] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $statusClass }}">
                                                {{ $car->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('third-party-cars.show', $car) }}" class="btn btn-sm btn-info" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($car->status === 'pending')
                                                    <a href="{{ route('third-party-cars.edit', $car) }}" class="btn btn-sm btn-primary" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @can('approve_third_party_cars')
                                                        <form action="{{ route('third-party-cars.approve', $car) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من اعتماد هذه السيارة؟')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success" title="اعتماد">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('third-party-cars.reject', $car) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من رفض هذه السيارة؟')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger" title="رفض">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @elseif($car->status === 'approved' && auth()->user()->can('approve_third_party_cars'))
                                                    <form action="{{ route('third-party-cars.complete', $car) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إكمال خدمة هذه السيارة؟')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" title="إكمال الخدمة">
                                                            <i class="fas fa-check-double"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($car->status === 'pending')
                                                    <form action="{{ route('third-party-cars.destroy', $car) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه السيارة؟')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">لا توجد سيارات خارجية مسجلة حتى الآن</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $thirdPartyCars->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
