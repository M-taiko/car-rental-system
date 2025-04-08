@extends('layouts.master')

@section('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #invoiceModal .modal-content, #invoiceModal .modal-content * {
            visibility: visible;
        }
        #invoiceModal {
            position: absolute;
            left: 0;
            top: 0;
        }
    }
</style>
@endsection

@section('title')
    {{ __('messages.rentals') }} - {{ __('messages.BIKE_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.rentals') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.manage_rentals') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <button class="btn btn-success" data-toggle="modal" data-target="#addRentalModal">
            <i class="fas fa-plus"></i> {{ __('messages.add_rental') }}
        </button>
    </div>
</div>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.rentals_list') }}</h3>
            </div>
            <div class="card-body">
                <table id="rentalsTable" class="table table-center table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.bike_name') }}</th>
                            <th>{{ __('messages.user_name') }}</th>
                            <th>{{ __('messages.price_per_hour') }}</th>
                            <th>{{ __('messages.start_date') }}</th>
                            <th>{{ __('messages.start_time') }}</th>
                            <th>{{ __('messages.end_date') }}</th>
                            <th>{{ __('messages.end_time') }}</th>
                            <th>{{ __('messages.hours') }}</th>
                            <th>{{ __('messages.total_cost') }}</th>
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

<!-- Add Rental Modal -->
<div class="modal fade" id="addRentalModal" tabindex="-1" role="dialog" aria-labelledby="addRentalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('rentals.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addRentalModalLabel">{{ __('messages.add_rental_modal_title') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bikeId">{{ __('messages.bike') }}</label>
                        <select name="bike_id" class="form-control" id="bikeId" required>
                            <option value="">{{ __('messages.select_bike') }}</option>
                            @foreach($bikes as $bike)
                                <option value="{{ $bike->id }}">{{ $bike->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="customerId">{{ __('messages.customer') }}</label>
                        <div class="input-group">
                            <select name="customer_id" class="form-control" id="customerId">
                                <option value="">{{ __('messages.select_customer') }}</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCustomerModal">
                                    <i class="fas fa-plus"></i> {{ __('messages.add_customer') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.start_date') }}</label>
                        <input type="text" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="startTime">{{ __('messages.start_time') }}</label>
                        <input type="time" name="start_time" class="form-control" id="startTime" required>
                    </div>
                    <div class="form-group">
                        <label for="pricePerHour">{{ __('messages.price_per_hour') }}</label>
                        <input type="number" step="0.01" name="price_per_hour" class="form-control" id="pricePerHour" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">{{ __('messages.add_customer') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="customerName">{{ __('messages.customer_name') }}</label>
                    <input type="text" class="form-control" id="customerName" required>
                </div>
                <div class="form-group">
                    <label for="customerPhone">{{ __('messages.customer_phone') }}</label>
                    <input type="text" class="form-control" id="customerPhone">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                <button type="button" class="btn btn-primary" id="saveCustomerBtn">{{ __('messages.save') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Return Bike Modal -->
<div class="modal fade" id="returnBikeModal" tabindex="-1" role="dialog" aria-labelledby="returnBikeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnBikeModalLabel">{{ __('messages.return_bike') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="returnBikeForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="rental_id" id="rentalId">
                    <div class="form-group">
                        <label>{{ __('messages.end_date') }}</label>
                        <input type="text" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="endTime">{{ __('messages.end_time') }}</label>
                        <input type="time" name="end_time" class="form-control" id="endTime" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.confirm') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        var table = $('#rentalsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('rentals.data') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'bike_name', name: 'bike_name' },
                { data: 'user_name', name: 'user_name' },
                { data: 'price_per_hour', name: 'price_per_hour' },
                { data: 'start_date', name: 'start_date' },
                { data: 'start_time', name: 'start_time' },
                { data: 'end_date', name: 'end_date' },
                { data: 'end_time', name: 'end_time' },
                { data: 'hours', name: 'hours' },
                { data: 'total_cost', name: 'total_cost' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                search: "{{ __('messages.search_rentals') }}",
                lengthMenu: "{{ __('messages.show_entries') }}",
                zeroRecords: "{{ __('messages.no_rentals_found') }}",
                info: "{{ __('messages.showing_info') }}",
                infoEmpty: "{{ __('messages.no_rentals_available') }}",
                processing: "{{ __('messages.processing') }}",
                paginate: {
                    next: "{{ __('messages.next') }}",
                    previous: "{{ __('messages.previous') }}"
                }
            },
            order: [[4, 'desc']]
        });

        // Handle Return Bike Button Click
        $(document).on('click', '.return-bike-btn', function() {
            var rentalId = $(this).data('id');
            $('#rentalId').val(rentalId);
            $('#returnBikeForm').attr('action', "{{ route('rentals.return', ':id') }}".replace(':id', rentalId));
            $('#returnBikeModal').modal('show');
        });

        // Handle Add Customer
        $('#saveCustomerBtn').on('click', function() {
            var name = $('#customerName').val();
            var phone = $('#customerPhone').val();

            if (!name) {
                alert('{{ __('messages.customer_name_required') }}');
                return;
            }

            $.ajax({
                url: "{{ route('rentals.storeCustomer') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name,
                    phone: phone,
                },
                success: function(response) {
                    if (response.success) {
                        $('#customerId').append('<option value="' + response.customer.id + '">' + response.customer.name + '</option>');
                        $('#customerId').val(response.customer.id);
                        $('#addCustomerModal').modal('hide');
                        $('#customerName').val('');
                        $('#customerPhone').val('');
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        });
    });
</script>
@endsection
