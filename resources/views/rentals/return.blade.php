@extends('layouts.master')

@section('title')
    {{ __('messages.return_car') }} - {{ __('messages.CAR_RENTAL_SYSTEM') }}
@endsection

@section('css')
    <!-- Datetime Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.rentals') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.return_car') }}</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-body">
                <form id="returnForm">
                    @csrf
                    <input type="hidden" name="rental_id" value="{{ $rental->id }}">

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('messages.car') }}</label>
                                <p class="form-control-static">{{ $rental->car->name }} - {{ $rental->car->plate_number }}</p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('messages.customer') }}</label>
                                <p class="form-control-static">{{ $rental->customer->name }} - {{ $rental->customer->phone }}</p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('messages.driver') }}</label>
                                <p class="form-control-static">
                                    {{ $rental->driver ? $rental->driver->name . ' - ' . $rental->driver->phone : __('messages.no_driver') }}
                                </p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('messages.rental_period') }}</label>
                                <p class="form-control-static">
                                    {{ $rental->start_time->format('Y-m-d H:i') }} - {{ $rental->end_time->format('Y-m-d H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('messages.daily_rate') }}</label>
                                <p class="form-control-static">
                                    {{ number_format($rental->daily_rate, 2) }} {{ __('messages.sar') }}
                                </p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('messages.return_date') }}</label>
                                <input type="text" name="return_date" class="form-control datetimepicker" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('messages.total_days') }}</label>
                                <input type="text" name="total_days" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('messages.total_amount') }}</label>
                                <input type="text" name="total_amount" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('messages.paid_amount') }}</label>
                                <input type="text" name="paid_amount" class="form-control" value="{{ $rental->paid_amount }}" required>
                                <p class="form-control-static" id="actual_days">-</p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('messages.actual_amount') }}</label>
                                <p class="form-control-static" id="actual_amount">-</p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('messages.remaining_amount') }}</label>
                                <p class="form-control-static" id="remaining_amount">-</p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('messages.refund_amount') }}</label>
                                <p class="form-control-static" id="refund_amount">-</p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="additional_payment">{{ __('messages.additional_payment') }}</label>
                                <input type="number" class="form-control" id="additional_payment" name="additional_payment"
                                    step="0.01" min="0" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0 mt-3 justify-content-end">
                        <button type="submit" class="btn btn-primary">{{ __('messages.complete_return') }}</button>
                        <a href="{{ route('rentals.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <!-- Datetime Picker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // تهيئة Flatpickr
            var actualEndTime = flatpickr('#actual_end_time', {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "{{ $rental->start_time->format('Y-m-d H:i') }}",
                defaultDate: "{{ now()->format('Y-m-d H:i') }}"
            });

            // حساب التكلفة عند تغيير وقت الإرجاع
            $('#actual_end_time').on('change', calculateCost);

            function calculateCost() {
                var actualEndTime = $('#actual_end_time').val();
                if (!actualEndTime) return;

                $.ajax({
                    url: "{{ route('rentals.calculateCost', $rental->id) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        actual_end_time: actualEndTime
                    },
                    success: function(response) {
                        $('#actual_days').text(response.days);
                        $('#actual_amount').text(response.amount.toFixed(2));
                        $('#remaining_amount').text(response.remaining_amount.toFixed(2));
                        $('#refund_amount').text(response.refund_amount.toFixed(2));
                    }
                });
            }

            // إرجاع السيارة
            $('#returnForm').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: "{{ __('messages.are_you_sure') }}",
                    text: "{{ __('messages.complete_return_warning') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('messages.yes_complete') }}",
                    cancelButtonText: "{{ __('messages.cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('rentals.return', $rental->id) }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                actual_end_time: $('#actual_end_time').val(),
                                additional_payment: $('#additional_payment').val()
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: "{{ __('messages.success') }}",
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        window.location.href = "{{ route('rentals.index') }}";
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: "{{ __('messages.error') }}",
                                    text: xhr.responseJSON.message
                                });
                            }
                        });
                    }
                });
            });

            // حساب التكلفة الأولية
            calculateCost();
        });
    </script>
@endsection
