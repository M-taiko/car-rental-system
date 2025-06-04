@extends('layouts.master')
@section('title', __('messages.add_rental'))
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"  rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"> 
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('rentals.store') }}" method="POST" id="rentalForm">
                    @csrf

                    <!-- Car -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="car_id">{{ __('messages.car') }}</label>
                                <select name="car_id" id="car_id" class="form-control select2" required>
                                    <option value="">{{ __('messages.select_car') }}</option>
                                    @foreach($cars as $car)
                                        <option value="{{ $car->id }}"
                                                data-daily-rate="{{ $car->daily_rate }}"
                                                data-weekly-rate="{{ $car->weekly_rate }}"
                                                data-monthly-rate="{{ $car->monthly_rate }}">
                                            {{ $car->brand }} / {{ $car->model }} - {{ $car->plate_number }}
                                        </option>
                                    @endforeach
                                    
                                </select>
                            </div>
                        </div>

                        <!-- Rental Mode -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('messages.rental_type') }}</label>
                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                    <label class="btn btn-outline-primary active">
                                        <input type="radio" name="rental_mode" value="normal" checked autocomplete="off">
                                        {{ __('messages.normal_rental') }}
                                    </label>
                                    <label class="btn btn-outline-primary">
                                        <input type="radio" name="rental_mode" value="route" autocomplete="off">
                                        {{ __('messages.route_based') }}
                                    </label>
                                  
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Normal Rental Options -->
                    <div id="normal-rental-section">
                        <div class="row">
                            <!-- Rental Period -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('messages.rental_period') }}</label>
                                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                        <label class="btn btn-outline-primary active">
                                            <input type="radio" name="rental_period" value="daily" checked autocomplete="off">
                                            {{ __('messages.daily') }}
                                        </label>
                                        <label class="btn btn-outline-primary">
                                            <input type="radio" name="rental_period" value="weekly" autocomplete="off">
                                            {{ __('messages.weekly') }}
                                        </label>
                                        <label class="btn btn-outline-primary">
                                            <input type="radio" name="rental_period" value="monthly" autocomplete="off">
                                            {{ __('messages.monthly') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Driver Option -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('messages.driver_option') }}</label>
                                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                        <label class="btn btn-outline-primary active">
                                            <input type="radio" name="driver_option" value="with_driver" checked autocomplete="off">
                                            {{ __('messages.with_driver') }}
                                        </label>
                                        <label class="btn btn-outline-primary">
                                            <input type="radio" name="driver_option" value="without_driver" autocomplete="off">
                                            {{ __('messages.without_driver') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Route Selection -->
                    <div id="route-rental-section" style="display: none;">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="route_id">{{ __('messages.select_route') }}</label>
                                    <select name="route_id" id="route_id" class="form-control select2">
                                        <option value="">{{ __('messages.select_route') }}</option>
                                        @foreach($routes as $route)
                                            <option value="{{ $route->id }}" data-cost="{{ $route->internal_cost }}">
                                                {{ $route->name }} - {{ $route->start_point }} → {{ $route->end_point }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer & Driver -->
                    <div class="row mt-3">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="customer_id">{{ __('messages.customer') }}</label>
                                <select name="customer_id" id="customer_id" class="form-control select2" required>
                                    <option value="">{{ __('messages.select_customer') }}</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="driver_id">{{ __('messages.driver') }}</label>
                                <select name="driver_id" id="driver_id" class="form-control select2">
                                    <option value="">{{ __('messages.select_driver') }}</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}" data-price="{{ $driver->daily_rate }}">
                                            {{ $driver->name }} - {{ $driver->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="row mt-3">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="start_time">{{ __('messages.start_time') }}</label>
                                <input type="text" name="start_time" id="start_time" class="form-control datetimepicker" required>
                            </div>
                        </div>

                        <div class="col-lg-6" id="end-time-container">
                            <div class="form-group">
                                <label for="expected_end_time">{{ __('messages.expected_end_time') }}</label>
                                <input type="text" name="expected_end_time" id="expected_end_time" class="form-control datetimepicker" required>
                            </div>
                        </div>
                    </div>

                    <!-- Paid Amount -->
                    <div class="row mt-3">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="paid_amount">{{ __('messages.paid_amount') }}</label>
                                <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount"
                                       class="form-control" placeholder="0.00" value="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th>{{ __('messages.rental_period') }}</th>
                                        <td id="rental_period_text">-</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.base_amount') }}</th>
                                        <td><input type="text" id="base_amount" class="form-control text-end" readonly value="0.00"></td>
                                    </tr>
                                    <tr id="driver_cost_row" style="display: none;">
                                        <th>{{ __('messages.driver_cost') }} <p id="driver_cost_per_day" class="text-muted" value=""></p></th>
                                        <td><input type="text" id="driver_cost" class="form-control text-end" readonly value="0.00"></td>
                                    </tr>
                                    <tr class="table-active">
                                        <th>{{ __('messages.total_amount') }}</th>
                                        <td><input type="text" id="total_amount" name="total_amount" class="form-control fw-bold text-end" readonly value="0.00"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" name="price_per_day" id="price_per_day_input" value="0.00">
                    <input type="hidden" name="driver_price_per_day" id="driver_price_input" value="0.00">

                    <!-- Submit Button -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary float-start">
                                <i class="fas fa-save"></i> {{ __('messages.save_rental') }}
                            </button>
                            <a href="{{ route('rentals.index') }}" class="btn btn-light">{{ __('messages.cancel') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> 
<script>
$(document).ready(function () {
    let selectedCar = null;
    let selectedDriver = null;

    // Init Date Picker
    $('.datetimepicker').flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        locale: "ar",
        minDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            if (instance.id === 'start_time') {
                const endTimePicker = document.getElementById('expected_end_time')._flatpickr;
                if ($('#rental_mode_input').val() === 'normal' && endTimePicker) {
                    endTimePicker.set('minDate', dateStr);
                }
            }
            calculateTotal();
        }
    });
});

// تحديث واجهة الإيجار بناءً على نوعه
function updateRentalModeUI() {
    const mode = $('input[name="rental_mode"]:checked').val();

    if (mode === 'normal') {
        $('#normal-rental-section').show();
        $('#end-time-container').show();
    } else {
        $('#normal-rental-section').hide();
        $('#end-time-container').hide();

        // تعيين expected_end_time بنفس start_time
        const startTime = $('#start_time').val();
        $('#expected_end_time').val(startTime);
    }
}

// Car Change Handler
$('#car_id').on('change', function () {
    const carId = $(this).val();
    if (!carId) return;

    const option = $(this).find('option:selected');
    selectedCar = {
        dailyRate: parseFloat(option.data('daily-rate') || 0),
        weeklyRate: parseFloat(option.data('weekly-rate') || 0),
        monthlyRate: parseFloat(option.data('monthly-rate') || 0)
    };

    updateRentalModeUI();
    calculateTotal();
});

// Rental Mode Change
$('input[name="rental_mode"]').on('change', function () {
    updateRentalModeUI();
    calculateTotal();
});

// Route ID Change
$('#route_id').on('change', function () {
    const routeId = $(this).val();
    const option = $(this).find('option:selected');
    const cost = parseFloat(option.data('cost') || 0);

    $('#base_amount').val(cost.toFixed(2));
    $('#total_amount').val(cost.toFixed(2));
    $('#rental_period_text').text("خط سير");
});

// Driver ID Change
$('#driver_id').on('change', function () {
    const driverId = $(this).val();
    if (!driverId) return;

    const option = $(this).find('option:selected');
    selectedDriver = {
        dailyRate: parseFloat(option.data('price') || 0)
    };
    calculateTotal();
});

// Calculate Total Amount
function calculateTotal() {
    if (!selectedCar) return;

    const start = new Date($('#start_time').val());
    const end = new Date($('#expected_end_time').val());

    if (isNaN(start) || isNaN(end) || end <= start) return;

    let diffDays = 1; 
    if (start.getTime() === end.getTime()) {
        diffDays = 1;
    } else {
        diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
    }
    let basePrice = 0;
    let periodText = `${diffDays} يوم`;

    const mode = $('input[name="rental_mode"]:checked').val();

    if (mode === 'normal') {
        const period = $('input[name="rental_period"]:checked').val();
        switch (period) {
            case 'weekly':
                basePrice = selectedCar.weeklyRate / 7 * diffDays;
                break;
            case 'monthly':
                basePrice = selectedCar.monthlyRate / 30 * diffDays;
                break;
            default:
                basePrice = selectedCar.dailyRate * diffDays;
        }

        // Driver Cost
        let driverCost = 0;
        if ($('input[name="driver_option"]:checked').val() === 'with_driver' && $('#driver_id').val()) {
            driverCost = selectedDriver?.dailyRate ;
            console.log(driverCost);
            console.log(diffDays);
            // $('#driver_cost').text(driverCost.toFixed(2) * diffDays + " * " +driverCost * diffDays);
            $('#driver_cost').val(driverCost);
            $('#driver_cost_row').show();
            $('#driver_price_input').val(driverCost.toFixed(2) );     
            $('#driver_cost_per_day').text(driverCost.toFixed(2) + " * " +  diffDays  + " = " + driverCost * diffDays);
        } else {
            $('#driver_cost_row').hide();
            $('#driver_price_input').val(0);     
        }

        // Total
        const total = basePrice + (driverCost *diffDays);
        $('#base_amount').val(basePrice.toFixed(2));
        $('#total_amount').val(total.toFixed(2));
        $('#rental_period_text').text(periodText);

        $('#price_per_day_input').val(selectedCar.dailyRate);
    } else {
        const routeOption = $('#route_id').find('option:selected');
        const routeCost = parseFloat(routeOption.data('cost') || 0);

        $('#base_amount').val(routeCost.toFixed(2));
        $('#total_amount').val(routeCost.toFixed(2));
        $('#rental_period_text').text("خط سير");
    }
}


    $(document).ready(function() {
            $('#route-rental-section').hide();
            $('input[name="rental_mode"]').change(function() {
                if ($(this).val() === 'route') {
                    $('#route-rental-section').show();
                } else {
                    $('#route-rental-section').hide();
                }
            });
        });
        // لما اختار نوع الايجار يكون خط سير يكون قيمة تاريخ النهاية هيا قيمة تاريخ البدايه
        $('#start_time').change(function() {
            if ($('input[name="rental_mode"]:checked').val() === 'route') {
                $('#expected_end_time').val($('#start_time').val());
                console.log($('#expected_end_time').val());
            }
        }); 
</script>
<style>
.route-item { border: 1px solid #ddd; padding: 8px; margin-bottom: 10px; }
</style>
@endsection