@extends('layouts.master')

@section('css')
<!-- Internal Data table css -->
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection

@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.rentals') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.list') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <div class="mb-3 mb-xl-0">
            <a href="{{ route('rentals.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus"></i> {{ __('messages.new_rental') }}
            </a>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection

@section('content')
<!-- row opened -->
<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mg-b-0">{{ __('messages.rentals_list') }}</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-md-nowrap" id="rentals-table">
                        <thead>
                            <tr>
                                <th class="wd-5p border-bottom-0">{{ __('messages.id') }}</th>
                                <th class="wd-15p border-bottom-0">{{ __('messages.customer') }}</th>
                                <th class="wd-15p border-bottom-0">{{ __('messages.car') }}</th>
                                <th class="wd-10p border-bottom-0">{{ __('messages.driver') }}</th>
                                <th class="wd-10p border-bottom-0">{{ __('messages.start_date') }}</th>
                                <th class="wd-10p border-bottom-0">{{ __('messages.end_date') }}</th>
                                <th class="wd-10p border-bottom-0">{{ __('messages.duration') }}</th>
                                <th class="wd-10p border-bottom-0">{{ __('messages.total_cost') }}</th>
                                <th class="wd-10p border-bottom-0">{{ __('messages.paid_amount') }}</th>
                                <th class="wd-10p border-bottom-0">{{ __('messages.remaining_amount') }}</th>
                                <th class="wd-10p border-bottom-0">{{ __('messages.status') }}</th>
                                <th class="wd-15p border-bottom-0">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rentals as $rental)
                            <tr>
                                <td>{{ $rental->id }}</td>
                                <td>{{ $rental->customer->name }}</td>
                                <td>{{ $rental->car->brand }} {{ $rental->car->model }}</td>
                                <td>{{ $rental->driver ? $rental->driver->name : __('messages.no_driver') }}</td>
                                <td>{{ $rental->start_time->format('Y-m-d H:i') }}</td>
                                <td>{{ $rental->end_time ? $rental->end_time->format('Y-m-d H:i') : '-' }}</td>
                                <td>{{ $rental->duration }} {{ __('messages.days') }}</td>
                                <td>{{ number_format($rental->total_cost, 2) }}</td>
                                <td>{{ number_format($rental->paid_amount, 2) }}</td>
                                <td>{{ number_format($rental->calculateRemainingAmount(), 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $rental->status == 'active' ? 'success' : ($rental->status == 'completed' ? 'info' : 'danger') }}">
                                        {{ __('messages.' . $rental->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('rentals.show', $rental->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($rental->status == 'active')
                                        <a href="#" class="btn btn-sm btn-success return-rental" data-id="{{ $rental->id }}">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        @endif
                                        <a href="{{ route('rentals.invoice', ['rental' => $rental->id]) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-rental" data-id="{{ $rental->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /row -->

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
                                <label>{{ __('messages.rental_period') }}</label>
                                <input type="text" class="form-control" id="rental_days" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.car_daily_rate') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ config('app.currency_symbol') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="car_cost" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.driver_daily_rate') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ config('app.currency_symbol') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="driver_cost" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.rental_percentage') }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="rental_percentage" readonly>
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
                                    <input type="text" class="form-control" id="tax_percentage" readonly>
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
                                        <span class="input-group-text">{{ config('app.currency_symbol') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="tax_amount" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.base_cost') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ config('app.currency_symbol') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="base_cost" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.total_amount') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ config('app.currency_symbol') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="total_amount" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.paid_amount') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ config('app.currency_symbol') }}</span>
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
                                        <span class="input-group-text">{{ config('app.currency_symbol') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="remaining_amount" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="returnRentalForm" method="POST">
                    @csrf
                    <input type="hidden" name="rental_id" id="return_rental_id">
                    <div class="form-group">
                        <label>{{ __('messages.return_date') }}</label>
                        <input type="datetime-local" class="form-control" name="return_date" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.notes') }}</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                <button type="submit" form="returnRentalForm" class="btn btn-primary">{{ __('messages.return') }}</button>
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
<!-- Internal Data tables -->
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/jszip.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/pdfmake.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/vfs_fonts.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.html5.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.print.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js')}}"></script>
<!--Internal  Datatable js -->
<script src="{{URL::asset('assets/js/table-data.js')}}"></script>

<script>
$(function() {
    // Initialize DataTable
    $('#rentals-table').DataTable({
        language: {
            url: "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.6/i18n/en.json' }}"
        },
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });

    // Delete rental
    $('.delete-rental').click(function() {
        var rentalId = $(this).data('id');
        if (confirm('{{ __('messages.are_you_sure') }}')) {
            $.ajax({
                url: '{{ route('rentals.destroy', '') }}/' + rentalId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                }
            });
        }
    });

    // Return rental
    $('.return-rental').click(function() {
        var rentalId = $(this).data('id');
        var returnDate = new Date().toISOString().slice(0, 16);
        
        // Calculate rental details
        $.ajax({
            url: '{{ route('rentals.return', ['rental' => ':rental']) }}'.replace(':rental', rentalId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                return_date: returnDate
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
                    
                    $('#return_rental_id').val(rentalId);
                    $('#return-rental-modal').modal('show');
                } else {
                    alert(response.message || '{{ __("messages.error_occurred") }}');
                }
            }
        });
    });

    // Handle return rental form submission
    $('#returnRentalForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var rentalId = $('#return_rental_id').val();
        var url = '{{ route('rentals.return', ['rental' => ':rental']) }}'.replace(':rental', rentalId);

        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert(response.message);
                    // Close modal
                    $('#return-rental-modal').modal('hide');
                    // Reload page to show updated status
                    window.location.reload();
                } else {
                    alert(response.message || '{{ __("messages.error_occurred") }}');
                }
            },
            error: function(xhr) {
                alert('{{ __("messages.error_occurred") }}');
            }
        });
    });

    // Delete Rental
    $('.delete-rental').click(function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#delete-rental-form').attr('action', `/rentals/${id}`);
        $('#delete-rental-modal').modal('show');
    });
});
</script>
@endsection
