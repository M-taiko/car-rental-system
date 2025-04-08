@extends('layouts.master')

@section('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endsection

@section('title')
    {{ __('messages.spare_parts') }} - {{ __('messages.BIKE_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.spare_parts') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.manage_spare_parts') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <div class="row">
            <div class="col-6">
                <button class="btn btn-success" data-toggle="modal" data-target="#addSparePartModal">
                    <i class="fas fa-plus"></i> {{ __('messages.add_spare_part') }}
                </button>
            </div>
            <div class="col-6">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addSparePartSaleModal">
                    <i class="fas fa-plus"></i> {{ __('messages.add_spare_part_sale') }}
                </button>
            </div>
        </div>



    </div>
</div>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
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
                <h3 class="card-title">{{ __('messages.spare_parts_list') }}</h3>
            </div>
            <div class="card-body">
                <table id="sparePartsTable" class="table table-center table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.spare_part_name') }}</th>
                            <th>{{ __('messages.quantity') }}</th>
                            <th>{{ __('messages.purchase_price') }}</th>
                            <th>{{ __('messages.selling_price') }}</th>
                            <th>{{ __('messages.description') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Spare Part Modal -->
<div class="modal fade" id="addSparePartModal" tabindex="-1" role="dialog" aria-labelledby="addSparePartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="addSparePartForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addSparePartModalLabel">{{ __('messages.add_spare_part_modal_title') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="sparePartsTableAdd">
                        <thead>
                            <tr>
                                <th>{{ __('messages.spare_part_name') }}</th>
                                <th>{{ __('messages.quantity') }}</th>
                                <th>{{ __('messages.purchase_price') }}</th>
                                <th>{{ __('messages.selling_price') }}</th>
                                <th>{{ __('messages.description') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="sparePartsTableBody">
                            <tr class="spare-part-row">
                                <td>
                                    <div class="name-container">
                                        <select name="spare_parts[0][name]" class="form-control spare-part-name" required>
                                            <option value="">{{ __('messages.select_existing') }}</option>
                                            <option value="new">{{ __('messages.add_new') }}</option>
                                            @foreach($spareParts as $part)
                                                <option value="{{ $part->name }}"
                                                        data-quantity="{{ $part->quantity }}"
                                                        data-purchase-price="{{ $part->purchase_price }}"
                                                        data-selling-price="{{ $part->selling_price }}"
                                                        data-description="{{ $part->description }}">
                                                    {{ $part->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td><input type="number" name="spare_parts[0][quantity]" class="form-control spare-part-quantity" min="0" required></td>
                                <td><input type="number" step="0.01" name="spare_parts[0][purchase_price]" class="form-control spare-part-purchase-price" min="0" required></td>
                                <td><input type="number" step="0.01" name="spare_parts[0][selling_price]" class="form-control spare-part-selling-price" min="0" required></td>
                                <td><textarea name="spare_parts[0][description]" class="form-control spare-part-description"></textarea></td>
                                <td><button type="button" class="btn btn-danger btn-sm remove-spare-part">{{ __('messages.remove') }}</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary btn-sm" id="addSparePartRow">{{ __('messages.add_spare_part') }}</button>
                    <div class="mt-2">
                        <strong>{{ __('messages.total_purchase_cost') }}: </strong>
                        <span id="totalPurchaseCost">0.00</span>
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





<!-- Add Spare Part Sale Modal -->
<div class="modal fade" id="addSparePartSaleModal" tabindex="-1" role="dialog" aria-labelledby="addSparePartSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="addSparePartSaleForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addSparePartSaleModalLabel">{{ __('messages.add_spare_part_sale_modal_title') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="sparePartsSaleTable">
                        <thead>
                            <tr>
                                <th>{{ __('messages.spare_part') }}</th>
                                <th>{{ __('messages.quantity') }}</th>
                                <th>{{ __('messages.selling_price') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="sparePartsSaleTableBody">
                            <tr class="spare-part-sale-row">
                                <td>
                                    <div class="spare-part-container">
                                        <select name="spare_parts[0][spare_part_id]" class="form-control spare-part-id" required>
                                            <option value="" >{{ __('messages.select_spare_part') }}</option>
                                            <option value="new">{{ __('messages.add_new') }}</option>
                                            @foreach($spareParts as $sparePart)
                                                <option value="{{ $sparePart->id }}"
                                                        data-quantity="{{ $sparePart->quantity }}"
                                                        data-selling-price="{{ $sparePart->selling_price }}">
                                                    {{ $sparePart->name }} ({{ $sparePart->quantity }} {{ __('messages.available') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td><input type="number" name="spare_parts[0][quantity]" class="form-control sale-quantity" min="1" required></td>
                                <td><input type="number" step="0.01" name="spare_parts[0][selling_price]" class="form-control sale-selling-price" min="0" readonly></td>
                                <td><button type="button" class="btn btn-danger btn-sm remove-spare-part-sale">{{ __('messages.remove') }}</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary btn-sm" id="addSparePartSaleRow">{{ __('messages.add_spare_part') }}</button>
                    <div class="mt-2">
                        <strong>{{ __('messages.total_sale_cost') }}: </strong>
                        <span id="totalSaleCost">0.00</span>
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

<!-- Edit Spare Part Modal -->
<div class="modal fade" id="editSparePartModal" tabindex="-1" role="dialog" aria-labelledby="editSparePartModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editSparePartForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editSparePartModalLabel">{{ __('messages.edit_spare_part') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editSparePartId">
                    <div class="form-group">
                        <label for="editSparePartName">{{ __('messages.spare_part_name') }}</label>
                        <input type="text" name="name" class="form-control" id="editSparePartName" required>
                    </div>
                    <div class="form-group">
                        <label for="editSparePartQuantity">{{ __('messages.quantity') }}</label>
                        <input type="number" name="quantity" class="form-control" id="editSparePartQuantity" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="editSparePartPurchasePrice">{{ __('messages.purchase_price') }}</label>
                        <input type="number" step="0.01" name="purchase_price" class="form-control" id="editSparePartPurchasePrice" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="editSparePartSellingPrice">{{ __('messages.selling_price') }}</label>
                        <input type="number" step="0.01" name="selling_price" class="form-control" id="editSparePartSellingPrice" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="editSparePartDescription">{{ __('messages.description') }}</label>
                        <textarea name="description" class="form-control" id="editSparePartDescription"></textarea>
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
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        var table = $('#sparePartsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('spare-parts.data') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'quantity', name: 'quantity' },
                { data: 'purchase_price', name: 'purchase_price' },
                { data: 'selling_price', name: 'selling_price' },
                { data: 'description', name: 'description' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                search: "{{ __('messages.search_spare_parts') }}",
                lengthMenu: "{{ __('messages.show_entries') }}",
                zeroRecords: "{{ __('messages.no_spare_parts_found') }}",
                info: "{{ __('messages.showing_info') }}",
                infoEmpty: "{{ __('messages.no_spare_parts_available') }}",
                processing: "{{ __('messages.processing') }}",
                paginate: {
                    next: "{{ __('messages.next') }}",
                    previous: "{{ __('messages.previous') }}"
                }
            },
            order: [[1, 'asc']]
        });

        // Handle spare part selection
        $(document).on('change', '.spare-part-name', function () {
            const row = $(this).closest('tr');
            const nameContainer = row.find('.name-container');
            const selectedValue = $(this).val();

            if (selectedValue === 'new') {
                // Replace select with input text
                nameContainer.find('.spare-part-name').replaceWith(`
                    <input type="text" name="spare_parts[${row.index()}][name]" class="form-control spare-part-name" required>
                `);
                row.find('.spare-part-quantity').val('');
                row.find('.spare-part-purchase-price').val('');
                row.find('.spare-part-selling-price').val('');
                row.find('.spare-part-description').val('');
            } else if (selectedValue !== '') {
                // Existing spare part selected
                const selectedOption = $(this).find('option:selected');
                row.find('.spare-part-quantity').val(1); // Default quantity to 1
                row.find('.spare-part-purchase-price').val(selectedOption.data('purchase-price'));
                row.find('.spare-part-selling-price').val(selectedOption.data('selling-price'));
                row.find('.spare-part-description').val(selectedOption.data('description'));
            } else {
                row.find('.spare-part-quantity').val('');
                row.find('.spare-part-purchase-price').val('');
                row.find('.spare-part-selling-price').val('');
                row.find('.spare-part-description').val('');
            }
            updateTotalPurchaseCost();
        });

        // Handle input text change (if empty, revert to select)
        $(document).on('input', '.spare-part-name', function () {
            const row = $(this).closest('tr');
            const nameContainer = row.find('.name-container');
            if ($(this).is('input') && $(this).val() === '') {
                // Replace input text with select
                nameContainer.find('.spare-part-name').replaceWith(`
                    <select name="spare_parts[${row.index()}][name]" class="form-control spare-part-name" required>
                        <option value="new">{{ __('messages.add_new') }}</option>
                        <option value="" disabled>{{ __('messages.select_existing') }}</option>
                        @foreach($spareParts as $part)
                            <option value="{{ $part->name }}"
                                    data-quantity="{{ $part->quantity }}"
                                    data-purchase-price="{{ $part->purchase_price }}"
                                    data-selling-price="{{ $part->selling_price }}"
                                    data-description="{{ $part->description }}">
                                {{ $part->name }}
                            </option>
                        @endforeach
                    </select>
                `);
                row.find('.spare-part-quantity').val('');
                row.find('.spare-part-purchase-price').val('');
                row.find('.spare-part-selling-price').val('');
                row.find('.spare-part-description').val('');
                updateTotalPurchaseCost();
            }
        });

        // Add new spare part row in the modal
        let sparePartIndex = 1;
        $('#addSparePartRow').click(function () {
            const newRow = `
                <tr class="spare-part-row">
                    <td>
                        <div class="name-container">
                            <select name="spare_parts[${sparePartIndex}][name]" class="form-control spare-part-name" required>
                                <option value="new">{{ __('messages.add_new') }}</option>
                                <option value="" disabled>{{ __('messages.select_existing') }}</option>
                                @foreach($spareParts as $part)
                                    <option value="{{ $part->name }}"
                                            data-quantity="{{ $part->quantity }}"
                                            data-purchase-price="{{ $part->purchase_price }}"
                                            data-selling-price="{{ $part->selling_price }}"
                                            data-description="{{ $part->description }}">
                                        {{ $part->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td><input type="number" name="spare_parts[${sparePartIndex}][quantity]" class="form-control spare-part-quantity" min="0" required></td>
                    <td><input type="number" step="0.01" name="spare_parts[${sparePartIndex}][purchase_price]" class="form-control spare-part-purchase-price" min="0" required></td>
                    <td><input type="number" step="0.01" name="spare_parts[${sparePartIndex}][selling_price]" class="form-control spare-part-selling-price" min="0" required></td>
                    <td><textarea name="spare_parts[${sparePartIndex}][description]" class="form-control spare-part-description"></textarea></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-spare-part">{{ __('messages.remove') }}</button></td>
                </tr>
            `;
            $('#sparePartsTableBody').append(newRow);
            sparePartIndex++;
            updateTotalPurchaseCost();
        });

        // Remove spare part row
        $(document).on('click', '.remove-spare-part', function () {
            $(this).closest('tr').remove();
            updateTotalPurchaseCost();
        });

        // Update total purchase cost on quantity or purchase price change
        $(document).on('input', '.spare-part-quantity, .spare-part-purchase-price', function () {
            updateTotalPurchaseCost();
        });

        // Function to calculate total purchase cost
        function updateTotalPurchaseCost() {
            let totalPurchaseCost = 0;
            $('.spare-part-row').each(function () {
                const quantity = parseInt($(this).find('.spare-part-quantity').val()) || 0;
                const purchasePrice = parseFloat($(this).find('.spare-part-purchase-price').val()) || 0;
                totalPurchaseCost += quantity * purchasePrice;
            });
            $('#totalPurchaseCost').text(totalPurchaseCost.toFixed(2));
        }

        // Submit Add Spare Part Form via AJAX
        $('#addSparePartForm').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route("spare-parts.store") }}',
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        $('#addSparePartModal').modal('hide');
                        $('#addSparePartForm')[0].reset();
                        $('#sparePartsTableBody').html(`
                            <tr class="spare-part-row">
                                <td>
                                    <div class="name-container">
                                        <select name="spare_parts[0][name]" class="form-control spare-part-name" required>
                                            <option value="new">{{ __('messages.add_new') }}</option>
                                            <option value="" disabled>{{ __('messages.select_existing') }}</option>
                                            @foreach($spareParts as $part)
                                                <option value="{{ $part->name }}"
                                                        data-quantity="{{ $part->quantity }}"
                                                        data-purchase-price="{{ $part->purchase_price }}"
                                                        data-selling-price="{{ $part->selling_price }}"
                                                        data-description="{{ $part->description }}">
                                                    {{ $part->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td><input type="number" name="spare_parts[0][quantity]" class="form-control spare-part-quantity" min="0" required></td>
                                <td><input type="number" step="0.01" name="spare_parts[0][purchase_price]" class="form-control spare-part-purchase-price" min="0" required></td>
                                <td><input type="number" step="0.01" name="spare_parts[0][selling_price]" class="form-control spare-part-selling-price" min="0" required></td>
                                <td><textarea name="spare_parts[0][description]" class="form-control spare-part-description"></textarea></td>
                                <td><button type="button" class="btn btn-danger btn-sm remove-spare-part">{{ __('messages.remove') }}</button></td>
                            </tr>
                        `);
                        sparePartIndex = 1;
                        $('#totalPurchaseCost').text('0.00');
                        table.ajax.reload();
                    }
                },
                error: function (xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        });

        // Edit Spare Part
        $(document).on('click', '.edit-spare-part', function () {
            var id = $(this).data('id');
            $.ajax({
                url: '{{ url("spare-parts") }}/' + id + '/edit',
                type: 'GET',
                success: function (response) {
                    $('#editSparePartId').val(response.id);
                    $('#editSparePartName').val(response.name);
                    $('#editSparePartQuantity').val(response.quantity);
                    $('#editSparePartPurchasePrice').val(response.purchase_price);
                    $('#editSparePartSellingPrice').val(response.selling_price);
                    $('#editSparePartDescription').val(response.description);
                    $('#editSparePartForm').attr('action', '{{ url("spare-parts") }}/' + id);
                    $('#editSparePartModal').modal('show');
                },
                error: function (xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        });

        // Delete Spare Part
        $(document).on('click', '.delete-spare-part', function () {
            var id = $(this).data('id');
            if (confirm('{{ __("messages.confirm_delete_spare_part") }}')) {
                $.ajax({
                    url: '{{ url("spare-parts") }}/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            table.ajax.reload();
                        }
                    },
                    error: function (xhr) {
                        alert('Error: ' + xhr.responseJSON.message);
                    }
                });
            }
        });
    });



    $(document).ready(function () {
        // Handle spare part selection in Sale Modal
        $(document).on('change', '.spare-part-id', function () {
            const row = $(this).closest('tr');
            const sparePartContainer = row.find('.spare-part-container');
            const selectedValue = $(this).val();

            if (selectedValue === 'new') {
                // Replace select with input text
                sparePartContainer.find('.spare-part-id').replaceWith(`
                    <input type="text" name="spare_parts[${row.index()}][spare_part_name]" class="form-control spare-part-id" required>
                `);
                row.find('.sale-quantity').val('').removeAttr('max');
                row.find('.sale-selling-price').val('').removeAttr('readonly');
            } else if (selectedValue !== '') {
                // Existing spare part selected
                const selectedOption = $(this).find('option:selected');
                const availableQuantity = selectedOption.data('quantity');
                const sellingPrice = selectedOption.data('selling-price');
                row.find('.sale-quantity').attr('max', availableQuantity).val('');
                row.find('.sale-selling-price').val(sellingPrice).attr('readonly', true);
            } else {
                row.find('.sale-quantity').val('').removeAttr('max');
                row.find('.sale-selling-price').val('').removeAttr('readonly');
            }
            updateTotalSaleCost();
        });

        // Handle input text change in Sale Modal (if empty, revert to select)
        $(document).on('input', '.spare-part-id', function () {
            const row = $(this).closest('tr');
            const sparePartContainer = row.find('.spare-part-container');
            if ($(this).is('input') && $(this).val() === '') {
                // Replace input text with select
                sparePartContainer.find('.spare-part-id').replaceWith(`
                    <select name="spare_parts[${row.index()}][spare_part_id]" class="form-control spare-part-id" required>
                        <option value="new">{{ __('messages.add_new') }}</option>
                        <option value="" disabled>{{ __('messages.select_spare_part') }}</option>
                        @foreach($spareParts as $sparePart)
                            <option value="{{ $sparePart->id }}"
                                    data-quantity="{{ $sparePart->quantity }}"
                                    data-selling-price="{{ $sparePart->selling_price }}">
                                {{ $sparePart->name }} ({{ $sparePart->quantity }} {{ __('messages.available') }})
                            </option>
                        @endforeach
                    </select>
                `);
                row.find('.sale-quantity').val('').removeAttr('max');
                row.find('.sale-selling-price').val('').removeAttr('readonly');
                updateTotalSaleCost();
            }
        });

        // Add new spare part row in the Sale Modal
        let sparePartSaleIndex = 1;
        $('#addSparePartSaleRow').click(function () {
            const newRow = `
                <tr class="spare-part-sale-row">
                    <td>
                        <div class="spare-part-container">
                            <select name="spare_parts[${sparePartSaleIndex}][spare_part_id]" class="form-control spare-part-id" required>
                                <option value="new">{{ __('messages.add_new') }}</option>
                                <option value="" disabled>{{ __('messages.select_spare_part') }}</option>
                                @foreach($spareParts as $sparePart)
                                    <option value="{{ $sparePart->id }}"
                                            data-quantity="{{ $sparePart->quantity }}"
                                            data-selling-price="{{ $sparePart->selling_price }}">
                                        {{ $sparePart->name }} ({{ $sparePart->quantity }} {{ __('messages.available') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td><input type="number" name="spare_parts[${sparePartSaleIndex}][quantity]" class="form-control sale-quantity" min="1" required></td>
                    <td><input type="number" step="0.01" name="spare_parts[${sparePartSaleIndex}][selling_price]" class="form-control sale-selling-price" min="0" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-spare-part-sale">{{ __('messages.remove') }}</button></td>
                </tr>
            `;
            $('#sparePartsSaleTableBody').append(newRow);
            sparePartSaleIndex++;
            updateTotalSaleCost();
        });

        // Remove spare part row in Sale Modal
        $(document).on('click', '.remove-spare-part-sale', function () {
            $(this).closest('tr').remove();
            updateTotalSaleCost();
        });

        // Update total sale cost on quantity or selling price change
        $(document).on('input', '.sale-quantity, .sale-selling-price', function () {
            updateTotalSaleCost();
        });

        // Function to calculate total sale cost
        function updateTotalSaleCost() {
            let totalSaleCost = 0;
            $('.spare-part-sale-row').each(function () {
                const quantity = parseInt($(this).find('.sale-quantity').val()) || 0;
                const sellingPrice = parseFloat($(this).find('.sale-selling-price').val()) || 0;
                totalSaleCost += quantity * sellingPrice;
            });
            $('#totalSaleCost').text(totalSaleCost.toFixed(2));
        }

        // Submit Add Spare Part Sale Form via AJAX
        $('#addSparePartSaleForm').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route("spare-part-sales.store") }}',
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        $('#addSparePartSaleModal').modal('hide');
                        $('#addSparePartSaleForm')[0].reset();
                        $('#sparePartsSaleTableBody').html(`
                            <tr class="spare-part-sale-row">
                                <td>
                                    <div class="spare-part-container">
                                        <select name="spare_parts[0][spare_part_id]" class="form-control spare-part-id" required>
                                            <option value="new">{{ __('messages.add_new') }}</option>
                                            <option value="" disabled>{{ __('messages.select_spare_part') }}</option>
                                            @foreach($spareParts as $sparePart)
                                                <option value="{{ $sparePart->id }}"
                                                        data-quantity="{{ $sparePart->quantity }}"
                                                        data-selling-price="{{ $sparePart->selling_price }}">
                                                    {{ $sparePart->name }} ({{ $sparePart->quantity }} {{ __('messages.available') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td><input type="number" name="spare_parts[0][quantity]" class="form-control sale-quantity" min="1" required></td>
                                <td><input type="number" step="0.01" name="spare_parts[0][selling_price]" class="form-control sale-selling-price" min="0" readonly></td>
                                <td><button type="button" class="btn btn-danger btn-sm remove-spare-part-sale">{{ __('messages.remove') }}</button></td>
                            </tr>
                        `);
                        sparePartSaleIndex = 1;
                        $('#totalSaleCost').text('0.00');
                        // Reload the table if you have one for sales
                        // Example: saleTable.ajax.reload();
                    }
                },
                error: function (xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        });
    });
</script>
@endsection
