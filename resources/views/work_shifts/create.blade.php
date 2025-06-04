@extends('layouts.app')

@section('title', 'إضافة وردية جديدة')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">إضافة وردية جديدة</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('work-shifts.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">اسم الوردية</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="start_time" class="col-md-4 col-form-label text-md-end">وقت البداية</label>
                            <div class="col-md-6">
                                <input id="start_time" type="time" class="form-control @error('start_time') is-invalid @enderror" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="end_time" class="col-md-4 col-form-label text-md-end">وقت النهاية</label>
                            <div class="col-md-6">
                                <input id="end_time" type="time" class="form-control @error('end_time') is-invalid @enderror" name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="description" class="col-md-4 col-form-label text-md-end">الوصف (اختياري)</label>
                            <div class="col-md-6">
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    حفظ
                                </button>
                                <a href="{{ route('work-shifts.index') }}" class="btn btn-secondary">
                                    رجوع
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add validation to ensure end time is after start time
        const startTime = document.getElementById('start_time');
        const endTime = document.getElementById('end_time');
        const form = document.querySelector('form');

        function validateTimes() {
            if (startTime.value && endTime.value) {
                if (startTime.value >= endTime.value) {
                    endTime.setCustomValidity('يجب أن يكون وقت النهاية بعد وقت البداية');
                    return false;
                } else {
                    endTime.setCustomValidity('');
                    return true;
                }
            }
            return true;
        }

        startTime.addEventListener('change', validateTimes);
        endTime.addEventListener('change', validateTimes);

        form.addEventListener('submit', function(e) {
            if (!validateTimes()) {
                e.preventDefault();
                endTime.reportValidity();
            }
        });
    });
</script>
@endpush
@endsection
