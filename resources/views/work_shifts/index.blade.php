@extends('layouts.app')

@section('title', 'إدارة الورديات')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>إدارة الورديات</span>
                    <a href="{{ route('work-shifts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة وردية جديدة
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
                                    <th>اسم الوردية</th>
                                    <th>وقت البداية</th>
                                    <th>وقت النهاية</th>
                                    <th>المدة</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($shifts as $shift)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $shift->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                                        <td>
                                            @php
                                                $start = \Carbon\Carbon::parse($shift->start_time);
                                                $end = \Carbon\Carbon::parse($shift->end_time);
                                                $duration = $start->diff($end);
                                                echo $duration->format('%h ساعة %i دقيقة');
                                            @endphp
                                        </td>
                                        <td>
                                            <span class="badge {{ $shift->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $shift->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('work-shifts.edit', $shift) }}" class="btn btn-sm btn-info" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('work-shifts.destroy', $shift) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الوردية؟')">
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
                                        <td colspan="7" class="text-center">لا توجد ورديات مضافة حتى الآن</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $shifts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
