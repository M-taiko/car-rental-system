@extends('layouts.master')

@section('title')
    {{ __('messages.add_rental') }} - {{ __('messages.CAR_RENTAL_SYSTEM') }}
@endsection

@section('css')
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Datetime Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.rentals') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.add_rental') }}</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('rentals.store') }}" method="POST" id="rentalForm">
                    @csrf

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="car_id">{{ __('messages.car') }}</label>
                                <select class="form-control select2 @error('car_id') is-invalid @enderror"
                                    id="car_id" name="car_id" required>
                                    <option value="">{{ __('messages.select_car') }}</option>
                                    @foreach($cars as $car)
                                        <option value="{{ $car->id }}" 
                                                data-price="{{ $car->daily_rate }}"
                                                data-has-percentage="{{ $car->has_rental_percentage }}"
                                                data-percentage="{{ $car->rental_percentage }}"
                                                {{ old('car_id') == $car->id ? 'selected' : '' }}>
                                            {{ $car->name }} - {{ $car->plate_number }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('car_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6" id="percentageInfo" style="display: none;">
                            <div class="form-group">
                                <label>{{ __('messages.rental_percentage') }}</label>
                                <div class="d-flex align-items-center">
                                    <span id="percentageValue" class="mr-2"></span>
                                    <span class="badge badge-info">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="customer_id">{{ __('messages.customer') }}</label>
                                <div class="input-group">
                                    <select class="form-control select2 @error('customer_id') is-invalid @enderror"
                                        id="customer_id" name="customer_id" required>
                                        <option value="">{{ __('messages.select_customer') }}</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} - {{ $customer->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCustomerModal">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('customer_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="driver_id">{{ __('messages.driver') }}</label>
                                <div class="input-group">
                                    <select class="form-control select2 @error('driver_id') is-invalid @enderror"
                                        id="driver_id" name="driver_id">
                                        <option value="">{{ __('messages.select_driver') }}</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}" data-price="{{ $driver->daily_rate }}"
                                                {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                                {{ $driver->name }} - {{ $driver->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addDriverModal">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('driver_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="start_time">{{ __('messages.start_time') }}</label>
                                <input type="text" class="form-control flatpickr @error('start_time') is-invalid @enderror"
                                    id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="expected_end_time">{{ __('messages.expected_end_time') }}</label>
                                <input type="text" class="form-control flatpickr @error('expected_end_time') is-invalid @enderror"
                                    id="expected_end_time" name="expected_end_time" value="{{ old('expected_end_time') }}" required>
                                @error('expected_end_time')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="price_per_day">{{ __('messages.price_per_day') }}</label>
                                <input type="number" class="form-control @error('price_per_day') is-invalid @enderror"
                                    id="price_per_day" name="price_per_day" value="{{ old('price_per_day') }}"
                                    step="0.01" min="0" required>
                                @error('price_per_day')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="driver_price_per_day">{{ __('messages.driver_price_per_day') }}</label>
                                <input type="number" class="form-control @error('driver_price_per_day') is-invalid @enderror"
                                    id="driver_price_per_day" name="driver_price_per_day" value="{{ old('driver_price_per_day') }}"
                                    step="0.01" min="0" readonly>
                                @error('driver_price_per_day')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="expected_days">{{ __('messages.expected_days') }}</label>
                                <input type="number" class="form-control" id="expected_days" readonly>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="expected_amount">{{ __('messages.expected_amount') }}</label>
                                <input type="number" class="form-control" id="expected_amount" readonly>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="paid_amount">{{ __('messages.paid_amount') }}</label>
                                <input type="number" class="form-control @error('paid_amount') is-invalid @enderror"
                                    id="paid_amount" name="paid_amount" value="{{ old('paid_amount') }}"
                                    step="0.01" min="0" required>
                                @error('paid_amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label for="notes">{{ __('messages.notes') }}</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                    id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0 mt-3 justify-content-end">
                        <button type="submit" class="btn btn-primary">{{ __('messages.add') }}</button>
                        <a href="{{ route('rentals.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- إضافة عميل جديد -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">{{ __('messages.add_customer') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addCustomerForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_name">{{ __('messages.name') }}</label>
                                <input type="text" class="form-control" id="customer_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_phone">{{ __('messages.phone') }}</label>
                                <input type="text" class="form-control" id="customer_phone" name="phone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_email">{{ __('messages.email') }}</label>
                                <input type="email" class="form-control" id="customer_email" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_id_type">{{ __('messages.id_type') }}</label>
                                <select class="form-control" id="customer_id_type" name="id_type" required>
                                    <option value="">{{ __('messages.select_id_type') }}</option>
                                    <option value="national_id">{{ __('messages.national_id') }}</option>
                                    <option value="iqama">{{ __('messages.iqama') }}</option>
                                    <option value="passport">{{ __('messages.passport') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_id_number">{{ __('messages.id_number') }}</label>
                                <input type="text" class="form-control" id="customer_id_number" name="id_number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_address">{{ __('messages.address') }}</label>
                                <input type="text" class="form-control" id="customer_address" name="address">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.add') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- إضافة سائق جديد -->
<div class="modal fade" id="addDriverModal" tabindex="-1" role="dialog" aria-labelledby="addDriverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDriverModalLabel">{{ __('messages.add_driver') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addDriverForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="driver_name">{{ __('messages.name') }}</label>
                                <input type="text" class="form-control" id="driver_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="driver_phone">{{ __('messages.phone') }}</label>
                                <input type="text" class="form-control" id="driver_phone" name="phone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="driver_email">{{ __('messages.email') }}</label>
                                <input type="email" class="form-control" id="driver_email" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="driver_id_type">{{ __('messages.id_type') }}</label>
                                <select class="form-control" id="driver_id_type" name="id_type" required>
                                    <option value="">{{ __('messages.select_id_type') }}</option>
                                    <option value="national_id">{{ __('messages.national_id') }}</option>
                                    <option value="iqama">{{ __('messages.iqama') }}</option>
                                    <option value="passport">{{ __('messages.passport') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="driver_id_number">{{ __('messages.id_number') }}</label>
                                <input type="text" class="form-control" id="driver_id_number" name="id_number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="driver_license_number">{{ __('messages.license_number') }}</label>
                                <input type="text" class="form-control" id="driver_license_number" name="license_number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="driver_license_expiry">{{ __('messages.license_expiry') }}</label>
                                <input type="text" class="form-control flatpickr-date" id="driver_license_expiry" name="license_expiry" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="driver_daily_rate">{{ __('messages.daily_rate') }}</label>
                                <input type="number" class="form-control" id="driver_daily_rate" name="daily_rate" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="driver_address">{{ __('messages.address') }}</label>
                                <input type="text" class="form-control" id="driver_address" name="address">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.add') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Datetime Picker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // تهيئة Select2
            $('.select2').select2();

            // تهيئة Flatpickr
            $('.flatpickr').flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today"
            });

            // تهيئة Flatpickr للتواريخ فقط
            $('.flatpickr-date').flatpickr({
                dateFormat: "Y-m-d",
                minDate: "today"
            });

            // عند اختيار سيارة
            $('#car_id').on('change', function() {
                var price = $(this).find(':selected').data('price');
                $('#price_per_day').val(price);
                calculateExpectedAmount();
            });

            // عند اختيار سائق
            $('#driver_id').on('change', function() {
                var price = $(this).find(':selected').data('price');
                $('#driver_price_per_day').val(price || 0);
                calculateExpectedAmount();
            });

            // عند تغيير التواريخ
            $('#start_time, #expected_end_time').on('change', function() {
                calculateExpectedAmount();
            });

            // حساب المبلغ المتوقع
            function calculateExpectedAmount() {
                var startTime = $('#start_time').val();
                var endTime = $('#expected_end_time').val();

                if (startTime && endTime) {
                    var start = new Date(startTime);
                    var end = new Date(endTime);
                    var days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));

                    if (days > 0) {
                        var carPrice = parseFloat($('#price_per_day').val()) || 0;
                        var driverPrice = parseFloat($('#driver_price_per_day').val()) || 0;
                        var totalAmount = (carPrice + driverPrice) * days;

                        $('#expected_days').val(days);
                        $('#expected_amount').val(totalAmount.toFixed(2));
                    }
                }
            }

            // إضافة عميل جديد
            $('#addCustomerForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('rentals.storeCustomer') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            var newOption = new Option(response.customer.name + ' - ' + response.customer.phone, response.customer.id, true, true);
                            $('#customer_id').append(newOption).trigger('change');

                            $('#addCustomerModal').modal('hide');
                            $('#addCustomerForm')[0].reset();

                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("messages.success") }}',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';

                        for (var key in errors) {
                            errorMessage += errors[key][0] + '\n';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("messages.error") }}',
                            text: errorMessage
                        });
                    }
                });
            });

            // إضافة سائق جديد
            $('#addDriverForm').on('submit', function(e) {
                e.preventDefault();

                var formData = {
                    name: $('#driver_name').val(),
                    phone: $('#driver_phone').val(),
                    email: $('#driver_email').val(),
                    id_type: $('#driver_id_type').val(),
                    id_number: $('#driver_id_number').val(),
                    license_number: $('#driver_license_number').val(),
                    license_expiry: $('#driver_license_expiry').val(),
                    daily_rate: $('#driver_daily_rate').val(),
                    address: $('#driver_address').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: "{{ route('rentals.storeDriver') }}",
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            var newOption = new Option(response.driver.name + ' - ' + response.driver.phone, response.driver.id, true, true);
                            $('#driver_id').append(newOption).trigger('change');

                            $('#addDriverModal').modal('hide');
                            $('#addDriverForm')[0].reset();

                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("messages.success") }}',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = '';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            for (var key in errors) {
                                errorMessage += errors[key][0] + '\n';
                            }
                        } else if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else {
                            errorMessage = '{{ __("messages.driver_create_failed") }}';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("messages.error") }}',
                            text: errorMessage
                        });
                    }
                });
            });

            // التحقق من صحة النموذج قبل الإرسال
            $('#rentalForm').on('submit', function(e) {
                e.preventDefault();

                // التحقق من اختيار سيارة
                if (!$('#car_id').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("messages.error") }}',
                        text: '{{ __("messages.please_select_car") }}'
                    });
                    return false;
                }

                // التحقق من اختيار عميل
                if (!$('#customer_id').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("messages.error") }}',
                        text: '{{ __("messages.please_select_customer") }}'
                    });
                    return false;
                }

                // التحقق من تواريخ الإيجار
                var startTime = new Date($('#start_time').val());
                var endTime = new Date($('#expected_end_time').val());

                if (endTime <= startTime) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("messages.error") }}',
                        text: '{{ __("messages.end_time_must_be_after_start_time") }}'
                    });
                    return false;
                }

                // التحقق من السعر اليومي
                if (!$('#price_per_day').val() || parseFloat($('#price_per_day').val()) <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("messages.error") }}',
                        text: '{{ __("messages.please_enter_valid_price") }}'
                    });
                    return false;
                }

                // التحقق من المبلغ المدفوع
                if (!$('#paid_amount').val() || parseFloat($('#paid_amount').val()) <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("messages.error") }}',
                        text: '{{ __("messages.please_enter_valid_paid_amount") }}'
                    });
                    return false;
                }

                // إذا تم اختيار سائق، تأكد من وجود سعر السائق
                if ($('#driver_id').val() && (!$('#driver_price_per_day').val() || parseFloat($('#driver_price_per_day').val()) <= 0)) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("messages.error") }}',
                        text: '{{ __("messages.please_enter_valid_driver_price") }}'
                    });
                    return false;
                }

                // إرسال النموذج
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        window.location.href = "{{ route('rentals.index') }}";
                    },
                    error: function(xhr) {
                        var errorMessage = '';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            for (var key in errors) {
                                errorMessage += errors[key][0] + '\n';
                            }
                        } else {
                            errorMessage = '{{ __("messages.rental_create_failed") }}';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("messages.error") }}',
                            text: errorMessage
                        });
                    }
                });
            });
        });
            // حساب المبلغ الإجمالي مع النسبة المئوية
            function calculateTotalAmount() {
                var carId = $('#car_id').val();
                var carOption = $('#car_id option:selected');
                var hasPercentage = carOption.data('has-percentage');
                var percentage = carOption.data('percentage');
                var basePrice = carOption.data('price');
                var days = $('#days').val();

                if (!carId || !basePrice || !days) {
                    return 0;
                }

                var total = basePrice * days;
                if (hasPercentage && percentage) {
                    total = total + (total * (percentage / 100));
                }

                return total;
            }

            // تحديث المبلغ الإجمالي
            function updateTotalAmount() {
                var total = calculateTotalAmount();
                $('#total_amount').val(total.toFixed(2));
            }

            // تحديث معلومات النسبة المئوية
            function updatePercentageInfo() {
                var carOption = $('#car_id option:selected');
                var hasPercentage = carOption.data('has-percentage');
                var percentage = carOption.data('percentage');

                if (hasPercentage && percentage) {
                    $('#percentageInfo').show();
                    $('#percentageValue').text(percentage);
                } else {
                    $('#percentageInfo').hide();
                }
            }

            // تحديث المبلغ الإجمالي عند تغيير السيارة أو عدد الأيام
            $('#car_id, #days').change(function() {
                updatePercentageInfo();
                updateTotalAmount();
            });

            // تحديث معلومات النسبة المئوية عند تحميل الصفحة
            $(document).ready(function() {
                updatePercentageInfo();
                updateTotalAmount();
            });

    </script>
@endsection
