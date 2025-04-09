@extends('layouts.master')

@section('title')
    {{ __('messages.maintenance') }} - {{ __('messages.BIKE_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.maintenance') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.maintenance_list') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        @can('create-maintenance')
            <button class="btn btn-primary" data-toggle="modal" data-target="#addMaintenanceModal">{{ __('messages.add_maintenance') }}</button>
        @endcan
    </div>
</div>
@endsection

@section('content')

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    @endif

@can('view-maintenance')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.maintenance_list') }}</h3>
            </div>
            <div class="card-body">
                <table id="maintenanceTable" class="table table-bordered table-center table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.id') }}</th>
                            <th>{{ __('messages.bike') }}</th>
                            <th>{{ __('messages.type') }}</th>
                            <th>{{ __('messages.customer') }}</th>
                            <th>{{ __('messages.phone') }}</th>
                            <th>{{ __('messages.cost') }}</th>
                            <th>{{ __('messages.description') }}</th>
                            <th>{{ __('messages.date') }}</th>
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
@else
    <div class="alert alert-warning">
        {{ __('messages.unauthorized_access') }}
    </div>
@endcan

<!-- Modal for Adding Maintenance -->
@can('create-maintenance')
<div class="modal fade" id="addMaintenanceModal" tabindex="-1" role="dialog" aria-labelledby="addMaintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaintenanceModalLabel">{{ __('messages.add_maintenance') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="addMaintenanceForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('messages.type') }}</label>
                        <select name="type" class="form-control" id="maintenanceType" required>
                            <option value="">{{ __('messages.select_type') }}</option>
                            <option value="internal">{{ __('messages.internal') }}</option>
                            <option value="customer">{{ __('messages.customer') }}</option>
                        </select>
                    </div>
                    <div class="form-group" id="bikeField" style="display: none;">
                        <label>{{ __('messages.bike') }}</label>
                        <select name="bike_id" class="form-control">
                            <option value="">{{ __('messages.select_bike') }}</option>
                            @foreach($bikes as $bike)
                                <option value="{{ $bike->id }}">{{ $bike->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="customerField" style="display: none;">
                        <label>{{ __('messages.customer') }}</label>
                        <select name="customer_id" id="customerSelect" class="form-control">
                            <option value="">{{ __('messages.select_customer') }}</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                            @endforeach
                        </select>
                        <small><a href="#" data-toggle="modal" data-target="#addCustomerModal">{{ __('messages.add_new_customer') }}</a></small>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.use_spare_parts') }}</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="use_parts" id="usePartsYes" value="yes" required>
                            <label class="form-check-label" for="usePartsYes">{{ __('messages.yes') }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="use_parts" id="usePartsNo" value="no" checked>
                            <label class="form-check-label" for="usePartsNo">{{ __('messages.no') }}</label>
                        </div>
                    </div>
                    <div class="form-group" id="partsField" style="display: none;">
                        <label>{{ __('messages.spare_parts') }}</label>
                        <table class="table table-bordered" id="partsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.part_name') }}</th>
                                    <th>{{ __('messages.quantity') }}</th>
                                    <th>{{ __('messages.selling_price') }}</th>
                                    <th>{{ __('messages.total') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="partsTableBody">
                                <tr class="part-row">
                                    <td>
                                        <select name="parts[0][spare_part_id]" class="form-control spare-part-select">
                                            <option value="">{{ __('messages.select_part') }}</option>
                                            @foreach($spareParts as $part)
                                                <option value="{{ $part->id }}" data-price="{{ $part->selling_price }}" data-quantity="{{ $part->quantity }}">{{ $part->name }} ({{ $part->quantity }} {{ __('messages.available') }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="parts[0][quantity]" class="form-control part-quantity" min="1"></td>
                                    <td><span class="part-price">0.00</span></td>
                                    <td><span class="part-total">0.00</span></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-part">{{ __('messages.remove') }}</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary btn-sm" id="addPart">{{ __('messages.add_part') }}</button>
                        <div class="mt-2">
                            <strong>{{ __('messages.total_parts_cost') }}: </strong>
                            <span id="totalPartsCost">0.00</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.maintenance_cost') }}</label>
                        <input type="number" name="cost" id="maintenanceCost" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.total_cost') }}</label>
                        <input type="text" id="totalCost" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.description') }}</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.date') }}</label>
                        <input type="datetime-local" name="date" class="form-control" required>
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
@endcan

<!-- Modal for Adding Customer -->
@can('create-maintenance') <!-- إضافة عميل جديد مرتبط بإضافة صيانة -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">{{ __('messages.add_new_customer') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="addCustomerForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.phone') }}</label>
                        <input type="text" name="phone" class="form-control" required>
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
@endcan

@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
    console.log('Script is loaded'); // Debugging

    $(document).ready(function () {
        console.log('jQuery is loaded and document is ready'); // Debugging

        // Show/hide fields based on type
        $('#maintenanceType').change(function () {
            var type = $(this).val();
            console.log('Maintenance Type Changed:', type); // Debugging
            if (type === 'internal') {
                $('#bikeField').show();
                $('#bikeField select').attr('required', true);
                $('#customerField').hide();
                $('#customerField select').removeAttr('required');
            } else if (type === 'customer') {
                $('#bikeField').show();
                $('#bikeField select').removeAttr('required');
                $('#customerField').show();
                $('#customerField select').attr('required', true);
            } else {
                $('#bikeField').hide();
                $('#bikeField select').removeAttr('required');
                $('#customerField').hide();
                $('#customerField select').removeAttr('required');
            }
        });

        // Show/hide parts table based on radio button
        $('input[name="use_parts"]').change(function () {
            if ($(this).val() === 'yes') {
                $('#partsField').show();
                $('#partsTableBody .part-row').each(function () {
                    $(this).find('.spare-part-select').attr('required', true);
                    $(this).find('.part-quantity').attr('required', true);
                });
            } else {
                $('#partsField').hide();
                $('#partsTableBody .part-row').each(function () {
                    $(this).find('.spare-part-select').removeAttr('required');
                    $(this).find('.part-quantity').removeAttr('required');
                });
                $('#totalPartsCost').text('0.00');
                updateTotalCost();
            }
        });

        // Add new part row
        let partIndex = 1;
        $('#addPart').click(function () {
            const newRow = `
                <tr class="part-row">
                    <td>
                        <select name="parts[${partIndex}][spare_part_id]" class="form-control spare-part-select" required>
                            <option value="">{{ __('messages.select_part') }}</option>
                            @foreach($spareParts as $part)
                                <option value="{{ $part->id }}" data-price="{{ $part->selling_price }}" data-quantity="{{ $part->quantity }}">{{ $part->name }} ({{ $part->quantity }} {{ __('messages.available') }})</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="parts[${partIndex}][quantity]" class="form-control part-quantity" min="1" required></td>
                    <td><span class="part-price">0.00</span></td>
                    <td><span class="part-total">0.00</span></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-part">{{ __('messages.remove') }}</button></td>
                </tr>
            `;
            $('#partsTableBody').append(newRow);
            partIndex++;
            updateTotalPartsCost();
        });

        // Remove part row
        $(document).on('click', '.remove-part', function () {
            $(this).closest('tr').remove();
            updateTotalPartsCost();
        });

        // Update part price and total on selection
        $(document).on('change', '.spare-part-select', function () {
            const row = $(this).closest('tr');
            const price = $(this).find('option:selected').data('price') || 0;
            const quantityAvailable = $(this).find('option:selected').data('quantity') || 0;
            row.find('.part-price').text(parseFloat(price).toFixed(2));
            const quantity = parseInt(row.find('.part-quantity').val()) || 0;
            if (quantity > quantityAvailable) {
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __("messages.insufficient_quantity_alert") }}',
                    text: '{{ __("messages.insufficient_quantity_message") }}',
                });
                row.find('.part-quantity').val(quantityAvailable);
            }
            row.find('.part-total').text((price * quantity).toFixed(2));
            updateTotalPartsCost();
        });

        // Update part total on quantity change
        $(document).on('input', '.part-quantity', function () {
            const row = $(this).closest('tr');
            const price = parseFloat(row.find('.part-price').text()) || 0;
            const quantity = parseInt($(this).val()) || 0;
            const quantityAvailable = parseInt(row.find('.spare-part-select option:selected').data('quantity')) || 0;
            if (quantity > quantityAvailable) {
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __("messages.insufficient_quantity_alert") }}',
                    text: '{{ __("messages.insufficient_quantity_message") }}',
                });
                $(this).val(quantityAvailable);
            }
            row.find('.part-total').text((price * quantity).toFixed(2));
            updateTotalPartsCost();
        });

        // Update total parts cost
        function updateTotalPartsCost() {
            let totalPartsCost = 0;
            $('.part-total').each(function () {
                const cost = parseFloat($(this).text()) || 0;
                totalPartsCost += cost;
            });
            $('#totalPartsCost').text(totalPartsCost.toFixed(2));
            updateTotalCost();
        }

        // Update total cost (maintenance + parts)
        function updateTotalCost() {
            const maintenanceCost = parseFloat($('#maintenanceCost').val()) || 0;
            const totalPartsCost = parseFloat($('#totalPartsCost').text()) || 0;
            const totalCost = maintenanceCost + totalPartsCost;
            $('#totalCost').val(totalCost.toFixed(2));
        }

        // Update total cost when maintenance cost changes
        $('#maintenanceCost').on('input', function () {
            updateTotalCost();
        });

        // DataTable
        console.log('Initializing DataTable'); // Debugging
        var table = $('#maintenanceTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("maintenance.data") }}',
                type: 'GET',
                error: function (xhr, error, thrown) {
                    console.log('DataTable AJAX Error:', xhr, error, thrown); // Debugging
                    if (xhr.status === 401) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Unauthorized',
                            text: 'Please log in to view maintenance records.',
                            confirmButtonText: 'Login',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('login') }}";
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while fetching data: ' + (xhr.responseJSON?.error || thrown),
                        });
                    }
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'bike_name', name: 'bike_name' },
                { data: 'type', name: 'type' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'customer_phone', name: 'customer_phone' },
                { data: 'cost', name: 'cost' },
                { data: 'description', name: 'description' },
                { data: 'date', name: 'date' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                "emptyTable": "{{ __('messages.no_data_available') }}",
                search: "{{ __('messages.search_maintenance') }}",
                lengthMenu: "{{ __('messages.show_entries') }}",
                zeroRecords: "{{ __('messages.no_maintenance_found') }}",
                info: "{{ __('messages.showing_info') }}",
                infoEmpty: "{{ __('messages.no_maintenance_available') }}",
                processing: "{{ __('messages.processing') }}",
                paginate: {
                    next: "{{ __('messages.next') }}",
                    previous: "{{ __('messages.previous') }}"
                }
            },
            order: [[7, 'desc']] // Order by date descending
        });

        // Add Maintenance
        $('#addMaintenanceForm').submit(function (e) {
            e.preventDefault();
            console.log('Add Maintenance Form Submitted'); // Debugging
            console.log('Form Data:', $(this).serialize()); // Debugging
            $.ajax({
                url: '{{ route("maintenance.store") }}',
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    console.log('Add Maintenance Success:', response); // Debugging
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || '{{ __("messages.maintenance_added_successfully") }}',
                        });
                        $('#addMaintenanceModal').modal('hide');
                        table.ajax.reload();
                    }
                },
                error: function (xhr) {
                    console.log('Add Maintenance Error:', xhr); // Debugging
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || '{{ __("messages.error_adding_maintenance") }}',
                    });
                }
            });
        });

        // Add Customer
        $('#addCustomerForm').submit(function (e) {
            e.preventDefault();
            console.log('Add Customer Form Submitted'); // Debugging
            console.log('Customer Form Data:', $(this).serialize()); // Debugging
            $.ajax({
                url: '{{ route("maintenance.storeCustomer") }}',
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    console.log('Add Customer Success:', response); // Debugging
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || '{{ __("messages.customer_added_successfully") }}',
                        });
                        $('#addCustomerModal').modal('hide');
                        $('#addCustomerForm')[0].reset();
                        $('#customerSelect').append('<option value="' + response.customer.id + '">' + response.customer.name + ' (' + response.customer.phone + ')</option>');
                        $('#customerSelect').val(response.customer.id);
                    }
                },
                error: function (xhr) {
                    console.log('Add Customer Error:', xhr); // Debugging
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || '{{ __("messages.error_adding_customer") }}',
                    });
                }
            });
        });

        // Complete Maintenance
        $(document).on('click', '.complete-maintenance', function () {
            var id = $(this).data('id');
            console.log('Complete Maintenance Clicked, ID:', id); // Debugging
            Swal.fire({
                title: '{{ __("messages.confirm_complete_maintenance") }}',
                text: '{{ __("messages.complete_maintenance_confirmation") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __("messages.complete") }}',
                cancelButtonText: '{{ __("messages.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("maintenance") }}/' + id + '/complete',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            console.log('Complete Maintenance Success:', response); // Debugging
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message || '{{ __("messages.maintenance_completed_successfully") }}',
                                });
                                table.ajax.reload();
                            }
                        },
                        error: function (xhr) {
                            console.log('Complete Maintenance Error:', xhr); // Debugging
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || '{{ __("messages.error_completing_maintenance") }}',
                            });
                        }
                    });
                }
            });
        });

        // Delete Maintenance
        $(document).on('click', '.delete-maintenance', function () {
            var id = $(this).data('id');
            console.log('Delete Maintenance Clicked, ID:', id); // Debugging
            Swal.fire({
                title: '{{ __("messages.confirm_delete_maintenance") }}',
                text: '{{ __("messages.delete_maintenance_confirmation") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __("messages.delete") }}',
                cancelButtonText: '{{ __("messages.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("maintenance") }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            console.log('Delete Maintenance Success:', response); // Debugging
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted',
                                    text: response.message || '{{ __("messages.maintenance_deleted_successfully") }}',
                                });
                                table.ajax.reload();
                            }
                        },
                        error: function (xhr) {
                            console.log('Delete Maintenance Error:', xhr); // Debugging
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || '{{ __("messages.error_deleting_maintenance") }}',
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
