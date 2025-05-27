@extends('layouts.master')

@section('title')
    {{ __('messages.maintenance') }} - {{ __('messages.CAR_RENTAL_SYSTEM') }}
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
                            <th>{{ __('messages.car') }}</th>
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
                    <div class="form-group" id="carField" style="display: none;">
                        <label>{{ __('messages.car') }}</label>
                        <select name="car_id" class="form-control">
                            <option value="">{{ __('messages.select_car') }}</option>
                            @foreach($cars as $car)
                                <option value="{{ $car->id }}">{{ $car->brand }} {{ $car->model }} ({{ $car->plate_number }})</option>
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
@can('create-maintenance')
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
                    <div class="form-group">
                        <label>{{ __('messages.address') }}</label>
                        <textarea name="address" class="form-control"></textarea>
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

        // تهيئة DataTables
        var table = $('#maintenanceTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('maintenance.data') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'car_info', name: 'car_info'},
                {data: 'type', name: 'type'},
                {data: 'customer_name', name: 'customer_name'},
                {data: 'customer_phone', name: 'customer_phone'},
                {data: 'cost', name: 'cost'},
                {data: 'description', name: 'description'},
                {data: 'start_date', name: 'start_date'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });

        // إظهار/إخفاء حقول العميل والسيارة حسب نوع الصيانة
        $('#maintenanceType').change(function() {
            var type = $(this).val();
            if (type === 'customer') {
                $('#customerField').show();
                $('#carField').show();
            } else if (type === 'internal') {
                $('#customerField').hide();
                $('#carField').show();
                $('#customerSelect').val('');
            } else {
                $('#customerField').hide();
                $('#carField').hide();
                $('#customerSelect').val('');
            }
        });

        // إظهار/إخفاء حقل قطع الغيار
        $('input[name="use_parts"]').change(function() {
            if ($(this).val() === 'yes') {
                $('#partsField').show();
            } else {
                $('#partsField').hide();
            }
            calculateTotalCost();
        });

        // إضافة صف جديد لقطع الغيار
        $('#addPart').click(function() {
            var rowCount = $('.part-row').length;
            var newRow = $('.part-row').first().clone();
            newRow.find('select').attr('name', 'parts[' + rowCount + '][spare_part_id]').val('');
            newRow.find('input').attr('name', 'parts[' + rowCount + '][quantity]').val('');
            newRow.find('.part-price').text('0.00');
            newRow.find('.part-total').text('0.00');
            $('#partsTableBody').append(newRow);
        });

        // حذف صف قطع الغيار
        $(document).on('click', '.remove-part', function() {
            if ($('.part-row').length > 1) {
                $(this).closest('tr').remove();
                calculateTotalCost();
            }
        });

        // حساب السعر الإجمالي عند تغيير الكمية أو القطعة
        $(document).on('change', '.spare-part-select, .part-quantity', function() {
            var row = $(this).closest('tr');
            var selectedOption = row.find('.spare-part-select option:selected');
            var price = parseFloat(selectedOption.data('price')) || 0;
            var quantity = parseInt(row.find('.part-quantity').val()) || 0;
            var maxQuantity = parseInt(selectedOption.data('quantity')) || 0;

            // التحقق من الكمية المتاحة
            if (quantity > maxQuantity) {
                alert('{{ __("messages.quantity_not_available") }}');
                row.find('.part-quantity').val(maxQuantity);
                quantity = maxQuantity;
            }

            row.find('.part-price').text(price.toFixed(2));
            row.find('.part-total').text((price * quantity).toFixed(2));
            calculateTotalCost();
        });

        // حساب التكلفة الإجمالية
        function calculateTotalCost() {
            var partsCost = 0;
            if ($('#usePartsYes').is(':checked')) {
                $('.part-total').each(function() {
                    partsCost += parseFloat($(this).text()) || 0;
                });
            }
            $('#totalPartsCost').text(partsCost.toFixed(2));

            var maintenanceCost = parseFloat($('#maintenanceCost').val()) || 0;
            var totalCost = maintenanceCost + partsCost;
            $('#totalCost').val(totalCost.toFixed(2));
        }

        // حساب التكلفة الإجمالية عند تغيير تكلفة الصيانة
        $('#maintenanceCost').on('input', calculateTotalCost);

        // إضافة عميل جديد
        $('#addCustomerForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('maintenance.storeCustomer') }}",
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        var newOption = new Option(response.customer.name + ' (' + response.customer.phone + ')', response.customer.id);
                        $('#customerSelect').append(newOption);
                        $('#customerSelect').val(response.customer.id);
                        $('#addCustomerModal').modal('hide');
                        $('#addCustomerForm')[0].reset();
                        alert('{{ __("messages.customer_added_successfully") }}');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    alert(Object.values(errors).flat().join('\n'));
                }
            });
        });

        // إضافة صيانة جديدة
        $('#addMaintenanceForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: "{{ route('maintenance.store') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#addMaintenanceModal').modal('hide');
                        $('#addMaintenanceForm')[0].reset();
                        table.ajax.reload();
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    alert(Object.values(errors).flat().join('\n'));
                }
            });
        });

        // إكمال الصيانة
        $(document).on('click', '.complete-maintenance', function() {
            var id = $(this).data('id');
            if (confirm('{{ __("messages.confirm_complete_maintenance") }}')) {
                $.ajax({
                    url: "{{ url('maintenance') }}/" + id + "/complete",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.message);
                    }
                });
            }
        });

        // حذف الصيانة
        $(document).on('click', '.delete-maintenance', function() {
            var id = $(this).data('id');
            if (confirm('{{ __("messages.confirm_delete_maintenance") }}')) {
                $.ajax({
                    url: "{{ url('maintenance') }}/" + id,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.message);
                    }
                });
            }
        });
    });
</script>
@endsection
