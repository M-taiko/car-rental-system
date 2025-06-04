@extends('layouts.master')
@section('title', __('messages.manage_rentals'))
@section('css')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"  rel="stylesheet">
    <style>
        .modal-dialog { max-width: 900px; }
    </style>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card mt-3">
            <div class="card-header justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="card-title mb-0">{{ __('messages.manage_rentals') }}</h4>
                </div>
                <div class="d-flex align-items-center">
                    <a href="{{ route('rentals.create') }}" class="btn btn-primary">
                        {{ __('messages.add_rental') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="rentals-table" class="table table-bordered text-nowrap">
                        <thead>
                            <tr>
                                <th>{{ __('messages.car') }}</th>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.start_time') }}</th>
                                <th>{{ __('messages.expected_end_time') }}</th>
                                <th>{{ __('messages.paid_amount') }}</th>
                                <th>{{ __('messages.total_amount') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal لإرجاع السيارة -->
<div class="modal fade" id="return-rental-modal" tabindex="-1" role="dialog"
     aria-labelledby="returnRentalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="returnRentalForm" method="POST">
                @csrf
                <input type="hidden" name="rental_id" id="return_rental_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="returnRentalLabel">{{ __('messages.return_car') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- End Time -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_time">{{ __('messages.end_time') }}</label>
                                <input type="datetime-local" name="end_time" id="end_time" class="form-control" required>
                            </div>
                        </div>
                        <!-- Additional Payment -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="additional_payment">{{ __('messages.additional_payment') }}</label>
                                <input type="number" step="0.01" min="0" name="additional_payment" id="additional_payment" class="form-control">
                            </div>
                        </div>
                        <!-- Days -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.days') }}</label>
                                <input type="text" id="rental_days" class="form-control" readonly>
                            </div>
                        </div>
                        <!-- Base Cost -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.base_cost') }}</label>
                                <input type="text" id="base_cost" class="form-control" readonly>
                            </div>
                        </div>
                        <!-- Driver Cost -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.driver_cost') }}  </label> 
                                <p id="driver_cost_lable" class="text-muted mb-0"></p>
                                <input type="text" id="driver_cost" class="form-control" readonly>
                            </div>
                        </div>
                        <!-- Percentage -->
                        <div class="col-md-6 d-none">
                            <div class="form-group">
                                <label>{{ __('messages.percentage') }}</label>
                                <input type="text" id="rental_percentage" class="form-control" readonly>
                            </div>
                        </div>

                        <!-- Total Cost -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.total_amount') }}</label>
                                <input type="text" id="total_amount" class="form-control" readonly>
                            </div>
                        </div>
                        <!-- Paid & Remaining -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.paid_amount') }}</label>
                                <input type="text" id="paid_amount" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.remaining_amount') }}</label>
                                <input type="text" id="remaining_amount" class="form-control" readonly>
                            </div>
                        </div>
                        <!-- Notes -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes">{{ __('messages.notes') }}</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('messages.confirm_return') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> 
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<script>
$(document).ready(function () {
    $('#rentals-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: "{{ route('rentals.getRentalsData') }}",
        columns: [
            { data: 'car_plate', name: 'car.plate_number' },
            { data: 'customer_name', name: 'customer.name' },
            { data: 'start_time', name: 'start_time' },
            { data: 'end_time', name: 'expected_end_time' },
            { data: 'paid_amount', name: 'paid_amount' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'status_badge', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json" 
        }
    });

    // عند النقر على زر "إرجاع السيارة"
    $(document).on('click', '.return-rental', function () {
        const rentalId = $(this).data('id');
        $.ajax({
            url: "{{ route('rentals.showReturnForm') }}",
            method: 'GET',
            data: { rental_id: rentalId },
            success: function (response) {
                if (response.success) {
                    const details = response.details;
                    // console.log(details);   
                    const formatNumber = (value) => parseFloat(value || 0).toFixed(2);
                    $('#return_rental_id').val(details.id || '');
                    // $('#rental_days').val(details.days || '0');
                    // $('#base_cost').val(formatNumber(details.base_cost));
                    $('#driver_cost').val(formatNumber(details.driver_cost));
                    // $('#rental_percentage').val(formatNumber(details.rental_percentage) + '%');
                    // $('#total_amount').val(formatNumber(details.total_cost || details.total_amount));
                    $('#paid_amount').val(formatNumber(details.paid_amount));
                    // $('#remaining_amount').val(formatNumber(details.remaining_amount));
                    // $('#notes').val(details.notes);
                    $('#return-rental-modal').modal('show');
                    console.log(details);
                } else {
                    alert(response.message || '{{ __("messages.error_occurred") }}');
                }
            },
            error: function () {
                alert('{{ __("messages.error_loading_data") }}');
            }
        });
    });

    // عند تغيير تاريخ الإرجاع ← إعادة الحساب
    $('#end_time').on('change', function () {
    const rentalId = $('#return_rental_id').val();
    const endTime = $(this).val();
    if (!rentalId || !endTime) return;

    $.ajax({
        url: "{{ route('rentals.calculateReturnDetails') }}",
        method: 'GET',
        data: { rental_id: rentalId, end_time: endTime },
        success: function (response) {
            if (response.success) {
                const details = response.details;
                const formatNumber = (value) => parseFloat(value || 0).toFixed(2);

                console.log(response);

                const days = details.days ;
                const baseCost = parseFloat(details.base_cost || 0);
                const driverCost = parseFloat(details.driver_cost / days || 0);
                const totalAmount = details.rental_type === 'normal'
                    ? (baseCost + driverCost) * days
                    : baseCost;

                $('#rental_days').val(days);
                $('#base_cost').val(formatNumber(baseCost));
                $('#driver_cost_lable').text(driverCost + " * " + days );
                $('#driver_cost').val(formatNumber(driverCost * days));
                $('#rental_percentage').val(formatNumber(details.percentage_amount) + '%');
                $('#total_amount').val(formatNumber(totalAmount));
                $('#paid_amount').val(formatNumber(details.paid_amount));
                $('#remaining_amount').val(formatNumber(totalAmount - details.paid_amount));
            }
        },
        error: function () {
            alert('{{ __("messages.error_loading_data") }}');
        }
    });
});


    // تقديم نموذج الإرجاع
    $('#returnRentalForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const rentalId = $('#return_rental_id').val();

        $.ajax({
            url: "{{ route('rentals.return', ['rental' => ':id']) }}".replace(':id', rentalId),
            method: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    $('#return-rental-modal').modal('hide');
                    location.reload();
                    toastr.success(response.message ?? '{{ __("messages.rental_returned") }}');
                } else {
                    alert(response.message ?? '{{ __("messages.something_went_wrong") }}');
                }
            },
            error: function () {
                alert('{{ __("messages.error_occurred") }}');
            }
        });
    });
});
</script>
@endsection