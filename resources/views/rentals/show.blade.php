@extends('layouts.master')

@section('css')
<!-- Internal Select2 css -->
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection

@section('js')
<!-- Internal Select2 js-->
<script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date picker
    const returnDateInput = document.getElementById('return_date');
    if (returnDateInput) {
        returnDateInput.addEventListener('change', function() {
            console.log('Selected return date:', this.value);
            calculateRentalDetails();
        });
    }

    // Get rental details from the rental record
    function calculateRentalDetails() {
        const rental = @json($rental);
        console.log('Rental data:', rental);
        
        try {
            // Helper function to safely set input value
            function setElementValue(id, value) {
                const element = document.getElementById(id);
                if (element) {
                    element.value = value;
                }
            }
            
            // Display rental dates
            setElementValue('start_date', new Date(rental.start_time).toLocaleDateString());
            setElementValue('expected_end_date', new Date(rental.expected_end_time).toLocaleDateString());
            
            // Rental period
            setElementValue('rental_days', rental.duration);
            
            // Daily rate
            setElementValue('daily_rate', rental.price_per_day);
            
            // Driver daily rate
            setElementValue('driver_daily_rate', rental.driver_price_per_day);
            
            // Rental percentage
            setElementValue('rental_percentage', rental.rental_percentage || 100);
            
            // Calculate total before tax
            const dailyRate = parseFloat(rental.price_per_day);
            const totalBeforeTax = dailyRate * rental.duration;
            setElementValue('total_before_tax', totalBeforeTax.toFixed(2));
            
            // Tax calculations
            const taxPercentage = parseFloat(rental.tax_percentage || 15);
            setElementValue('tax_percentage', taxPercentage);
            const taxAmount = (totalBeforeTax * taxPercentage) / 100;
            setElementValue('tax_amount', taxAmount.toFixed(2));
            
            // Total after tax
            const totalAfterTax = totalBeforeTax + taxAmount;
            setElementValue('total_after_tax', totalAfterTax.toFixed(2));
            
            // Paid amount
            setElementValue('paid_amount', rental.paid_amount);
            
            // Calculate remaining amount
            const remainingAmount = totalAfterTax - parseFloat(rental.paid_amount);
            setElementValue('remaining_amount', remainingAmount.toFixed(2));

            // Update return date input with rental end date
            const returnDateInput = document.getElementById('return_date');
            if (returnDateInput) {
                const returnDate = new Date(rental.expected_end_time);
                returnDateInput.value = returnDate.toISOString().split('T')[0];
            }

            // Add event listener for tax percentage change
            const taxPercentageInput = document.getElementById('tax_percentage');
            if (taxPercentageInput) {
                taxPercentageInput.addEventListener('input', function() {
                    const newTaxPercentage = parseFloat(this.value);
                    if (!isNaN(newTaxPercentage)) {
                        const totalBeforeTax = parseFloat(document.getElementById('total_before_tax').value);
                        const taxAmount = (totalBeforeTax * newTaxPercentage) / 100;
                        setElementValue('tax_amount', taxAmount.toFixed(2));
                        
                        const totalAfterTax = totalBeforeTax + taxAmount;
                        setElementValue('total_after_tax', totalAfterTax.toFixed(2));
                        
                        const paidAmount = parseFloat(document.getElementById('paid_amount').value);
                        const remainingAmount = totalAfterTax - paidAmount;
                        setElementValue('remaining_amount', remainingAmount.toFixed(2));
                    }
                });
            }
        } catch (error) {
            console.error('Error in calculateRentalDetails:', error);
        }
    }

    // Initialize modal buttons
    const returnRentalButtons = document.querySelectorAll('.return-rental');
    returnRentalButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('return-rental-modal'));
            modal.show();
        });
    });

    // Delete rental modal
    const deleteRentalButtons = document.querySelectorAll('.delete-rental');
    deleteRentalButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('delete-rental-modal'));
            modal.show();
        });
    });
});
</script>
@endsection

@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.rentals') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.details') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <div class="mb-3 mb-xl-0">
            <a href="{{ route('rentals.index') }}" class="btn btn-primary">
                <i class="mdi mdi-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection

@section('content')
<!-- row -->
<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mg-b-0">{{ __('messages.rental_details') }}</h4>
                    <div class="btn-group">
                        <a href="{{ route('rentals.invoice', ['rental' => $rental->id]) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-file-invoice"></i> {{ __('messages.invoice') }}
                        </a>
                        @if($rental->status == 'active')
                        <button type="button" class="btn btn-success btn-sm return-rental" data-id="{{ $rental->id }}">
                            <i class="fas fa-check"></i> {{ __('messages.return') }}
                        </button>
                        @endif
                        <button type="button" class="btn btn-danger btn-sm delete-rental" data-id="{{ $rental->id }}">
                            <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Rental Status -->
                    <div class="col-md-12 mb-4">
                        <div class="alert alert-{{ $rental->status == 'active' ? 'success' : ($rental->status == 'completed' ? 'info' : 'danger') }}">
                            <h4 class="alert-heading">{{ __('messages.status') }}: {{ __('messages.' . $rental->status) }}</h4>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <h5 class="mb-3">{{ __('messages.customer_information') }}</h5>
                                <table class="table table-striped mg-b-0">
                                    <tbody>
                                        <tr>
                                            <td>{{ __('messages.name') }}:</td>
                                            <td>{{ $rental->customer->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.phone') }}:</td>
                                            <td>{{ $rental->customer->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.email') }}:</td>
                                            <td>{{ $rental->customer->email }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.id_number') }}:</td>
                                            <td>{{ $rental->customer->id_number }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Car Information -->
                    <div class="col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <h5 class="mb-3">{{ __('messages.car_information') }}</h5>
                                <table class="table table-striped mg-b-0">
                                    <tbody>
                                        <tr>
                                            <td>{{ __('messages.brand_model') }}:</td>
                                            <td>{{ $rental->car->brand }} {{ $rental->car->model }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.plate_number') }}:</td>
                                            <td>{{ $rental->car->plate_number }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.year') }}:</td>
                                            <td>{{ $rental->car->year }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.color') }}:</td>
                                            <td>{{ $rental->car->color }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Driver Information -->
                    @if($rental->driver)
                    <div class="col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <h5 class="mb-3">{{ __('messages.driver_information') }}</h5>
                                <table class="table table-striped mg-b-0">
                                    <tbody>
                                        <tr>
                                            <td>{{ __('messages.name') }}:</td>
                                            <td>{{ $rental->driver->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.phone') }}:</td>
                                            <td>{{ $rental->driver->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.license_number') }}:</td>
                                            <td>{{ $rental->driver->license_number }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Rental Details -->
                    <div class="col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <h5 class="mb-3">{{ __('messages.rental_information') }}</h5>
                                <table class="table table-striped mg-b-0">
                                    <tbody>
                                        <tr>
                                            <td>{{ __('messages.rental_type') }}:</td>
                                            <td>{{ __('messages.' . $rental->rental_type) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.start_time') }}:</td>
                                            <td>{{ $rental->start_time->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.end_time') }}:</td>
                                            <td>{{ $rental->end_time ? $rental->end_time->format('Y-m-d H:i') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.total_cost') }}:</td>
                                            <td>{{ number_format($rental->total_cost, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.paid_amount') }}:</td>
                                            <td>{{ number_format($rental->paid_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('messages.remaining_amount') }}:</td>
                                            <td>{{ number_format($rental->calculateRemainingAmount(), 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($rental->notes)
                    <div class="col-md-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <h5 class="mb-3">{{ __('messages.notes') }}</h5>
                                <p class="card-text">{{ $rental->notes }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- row closed -->

<!-- Return Rental Modal -->
<div class="modal" id="return-rental-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">{{ __('messages.return_rental') }}</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="rental-details mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.start_date') }}</label>
                                <input type="text" class="form-control" id="start_date" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.expected_end_date') }}</label>
                                <input type="text" class="form-control" id="expected_end_date" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.rental_period') }}</label>
                                <input type="text" class="form-control" id="rental_days" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.daily_rate') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ __('messages.sar') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="daily_rate" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.driver_daily_rate') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ __('messages.sar') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="driver_daily_rate" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.rental_percentage') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="rental_percentage" step="0.1" min="0" max="100">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.tax_percentage') }}</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="tax_percentage" step="0.1" min="0" max="100">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.tax_amount') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ __('messages.sar') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="tax_amount" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.total_before_tax') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ __('messages.sar') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="total_before_tax" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.total_after_tax') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ __('messages.sar') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="total_after_tax" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.paid_amount') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ __('messages.sar') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="paid_amount" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.remaining_amount') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ __('messages.sar') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="remaining_amount" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="{{ route('rentals.return', $rental->id) }}" method="POST" id="returnRentalForm">
                    @csrf
                    <div class="form-group">
                        <label>{{ __('messages.return_date') }}</label>
                        <input type="date" class="form-control" name="end_time" id="return_date" required
                            min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.additional_payment') }}</label>
                        <input type="number" class="form-control" name="additional_payment" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.notes') }}</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="submit" form="returnRentalForm" class="btn btn-primary">{{ __('messages.return') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Rental Modal -->
<div class="modal" id="delete-rental-modal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">{{ __('messages.delete_rental') }}</h6>
                <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="delete-rental-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>{{ __('messages.confirm_delete_rental') }}</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn ripple btn-danger" type="submit">{{ __('messages.delete') }}</button>
                    <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">{{ __('messages.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Initialize return date with current time
    document.addEventListener('DOMContentLoaded', function() {
        var returnDate = new Date().toISOString().slice(0, 16);
        document.getElementById('return_date').value = returnDate;
        
        // Calculate initial rental details
        calculateRentalDetails();
    });

    // Calculate rental details when return date changes
    $('#return_date').on('change', function() {
        calculateRentalDetails();
    });

    function calculateRentalDetails() {
        var returnDate = $('#return_date').val();
        
        // Calculate rental details
        $.ajax({
            url: '{{ route('rentals.return', $rental->id) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                return_date: returnDate,
                calculate_only: true
            },
            success: function(response) {
                if (response.success) {
                    // Display rental details
                    $('#rental_days').val(response.details.days);
                    $('#car_cost').val(response.details.car_cost.toFixed(2));
                    $('#driver_cost').val(response.details.driver_cost.toFixed(2));
                    $('#rental_percentage').val(response.details.rental_percentage);
                    $('#tax_percentage').val(response.details.tax_percentage);
                    $('#tax_amount').val(response.details.tax_amount.toFixed(2));
                    $('#base_cost').val(response.details.base_cost.toFixed(2));
                    $('#total_amount').val(response.details.total_cost.toFixed(2));
                    $('#paid_amount').val(response.details.paid_amount.toFixed(2));
                    $('#remaining_amount').val(response.details.remaining_amount.toFixed(2));
                } else {
                    alert(response.message || '{{ __("messages.error_occurred") }}');
                }
            }
        });
    }

    // Return rental button click
    $('.return-rental').click(function() {
        $('#return-rental-modal').modal('show');
    });

    // Delete Rental
    $('.delete-rental').click(function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#delete-rental-form').attr('action', `/rentals/${id}`);
        $('#delete-rental-modal').modal('show');
    });

</script>
@endsection
