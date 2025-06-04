@extends('layouts.app')

@section('title', 'إدارة أنواع السيارات')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>إدارة أنواع السيارات</span>
                    <a href="{{ route('car-types.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة نوع جديد
                    </a>
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

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم النوع</th>
                                    <th>عدد السيارات</th>
                                    <th>الأسعار حسب الوردية</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($carTypes as $carType)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $carType->name }}</td>
                                        <td>{{ $carType->cars_count ?? 0 }}</td>
                                        <td>
                                            @foreach($carType->shiftRates as $rate)
                                                <span class="badge bg-info text-dark me-1" title="{{ $rate->workShift->name }} ({{ $rate->workShift->start_time }} - {{ $rate->workShift->end_time }})">
                                                    {{ $rate->workShift->name }}: {{ number_format($rate->rate, 2) }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <span class="badge {{ $carType->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $carType->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('car-types.edit', $carType) }}" class="btn btn-sm btn-info" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('car-types.destroy', $carType) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا النوع؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">لا توجد أنواع سيارات مضافة حتى الآن</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $carTypes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
