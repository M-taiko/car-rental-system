@extends('layouts.app')

@section('title', 'تعديل نوع السيارة: ' . $carType->name)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">تعديل نوع السيارة: {{ $carType->name }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('car-types.update', $carType) }}" id="carTypeForm">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label for="name" class="col-md-3 col-form-label text-md-end">اسم النوع</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $carType->name) }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="description" class="col-md-3 col-form-label text-md-end">الوصف (اختياري)</label>
                            <div class="col-md-6">
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $carType->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', $carType->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        نشط
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">أسعار الورديات</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="shiftRatesTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>اسم الوردية</th>
                                                <th>وقت البداية</th>
                                                <th>وقت النهاية</th>
                                                <th>السعر ({{ config('settings.currency_symbol', 'ر.س') }})</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($workShifts as $index => $shift)
                                                @php
                                                    $rate = $carType->shiftRates->firstWhere('work_shift_id', $shift->id);
                                                    $oldRate = old('shift_rates.' . $index . '.rate', $rate ? $rate->rate : 0);
                                                @endphp
                                                <tr>
                                                    <td>{{ $shift->name }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                                                    <td>
                                                        <input type="hidden" name="shift_rates[{{ $index }}][work_shift_id]" value="{{ $shift->id }}">
                                                        <input type="number" 
                                                               step="0.01" 
                                                               min="0" 
                                                               class="form-control @error('shift_rates.' . $index . '.rate') is-invalid @enderror" 
                                                               name="shift_rates[{{ $index }}][rate]" 
                                                               value="{{ $oldRate }}" 
                                                               required>
                                                        @error('shift_rates.' . $index . '.rate')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    حفظ التغييرات
                                </button>
                                <a href="{{ route('car-types.index') }}" class="btn btn-secondary">
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
        // Form validation
        const form = document.getElementById('carTypeForm');
        const requiredRates = document.querySelectorAll('input[name^="shift_rates"][name$="[rate]"]');
        
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate rates
            requiredRates.forEach(input => {
                if (!input.value || parseFloat(input.value) <= 0) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('الرجاء إدخال أسعار صحيحة لجميع الورديات');
            }
        });
    });
</script>
@endpush
@endsection
