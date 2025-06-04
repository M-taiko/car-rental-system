@extends('layouts.app')

@section('title', 'إضافة سيارة خارجية جديدة')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">إضافة سيارة خارجية جديدة</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('third-party-cars.store') }}" id="thirdPartyCarForm">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="car_number" class="form-label">رقم السيارة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('car_number') is-invalid @enderror" 
                                       id="car_number" name="car_number" value="{{ old('car_number') }}" required>
                                @error('car_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="driver_name" class="form-label">اسم السائق <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('driver_name') is-invalid @enderror" 
                                       id="driver_name" name="driver_name" value="{{ old('driver_name') }}" required>
                                @error('driver_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="driver_phone" class="form-label">رقم هاتف السائق <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('driver_phone') is-invalid @enderror" 
                                       id="driver_phone" name="driver_phone" value="{{ old('driver_phone') }}" required>
                                @error('driver_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="route_id" class="form-label">خط السير <span class="text-danger">*</span></label>
                                <select class="form-select @error('route_id') is-invalid @enderror" id="route_id" name="route_id" required>
                                    <option value="">اختر خط السير</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->id }}" 
                                            {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                            {{ $route->name }} ({{ $route->start_point }} - {{ $route->end_point }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('route_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="distance_km" class="form-label">المسافة (كم) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" class="form-control @error('distance_km') is-invalid @enderror" 
                                       id="distance_km" name="distance_km" value="{{ old('distance_km') }}" required>
                                @error('distance_km')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="price_per_km" class="form-label">سعر الكيلومتر ({{ config('settings.currency_symbol', 'ر.س') }}) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" class="form-control @error('price_per_km') is-invalid @enderror" 
                                       id="price_per_km" name="price_per_km" value="{{ old('price_per_km') }}" required>
                                @error('price_per_km')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="total_cost" class="form-label">إجمالي التكلفة ({{ config('settings.currency_symbol', 'ر.س') }})</label>
                                <input type="text" class="form-control bg-light" id="total_cost" readonly>
                                <input type="hidden" id="total_cost_hidden" name="total_cost">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="service_date" class="form-label">تاريخ الخدمة <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('service_date') is-invalid @enderror" 
                                       id="service_date" name="service_date" value="{{ old('service_date', now()->format('Y-m-d')) }}" required>
                                @error('service_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="notes" class="form-label">ملاحظات (اختياري)</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="1">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ
                                </button>
                                <a href="{{ route('third-party-cars.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-right"></i> رجوع
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
        const distanceInput = document.getElementById('distance_km');
        const pricePerKmInput = document.getElementById('price_per_km');
        const totalCostInput = document.getElementById('total_cost');
        const totalCostHidden = document.getElementById('total_cost_hidden');
        const form = document.getElementById('thirdPartyCarForm');
        
        function calculateTotalCost() {
            const distance = parseFloat(distanceInput.value) || 0;
            const pricePerKm = parseFloat(pricePerKmInput.value) || 0;
            const total = distance * pricePerKm;
            
            totalCostInput.value = total.toFixed(2);
            totalCostHidden.value = total.toFixed(2);
        }
        
        // Calculate on input change
        distanceInput.addEventListener('input', calculateTotalCost);
        pricePerKmInput.addEventListener('input', calculateTotalCost);
        
        // Initial calculation
        calculateTotalCost();
        
        // Form validation
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
        
        // Auto-fill price per km when route changes
        const routeSelect = document.getElementById('route_id');
        if (routeSelect) {
            const routes = @json($routes->keyBy('id'));
            
            routeSelect.addEventListener('change', function() {
                const routeId = parseInt(this.value);
                const route = routes[routeId];
                
                if (route) {
                    // Auto-fill distance from route
                    if (!distanceInput.value || distanceInput.dataset.autoFilled === 'true') {
                        distanceInput.value = route.distance_km;
                        distanceInput.dataset.autoFilled = 'true';
                    }
                    
                    // Auto-fill price per km from route external cost
                    if (!pricePerKmInput.value || pricePerKmInput.dataset.autoFilled === 'true') {
                        pricePerKmInput.value = route.external_cost;
                        pricePerKmInput.dataset.autoFilled = 'true';
                    }
                    
                    calculateTotalCost();
                }
            });
        }
    });
</script>
@endpush
@endsection
