@extends('layouts.master')

@section('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endsection

@section('title')
    {{ __('messages.spare_part_sales') }} - {{ __('messages.BIKE_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.spare_part_sales') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.manage_spare_part_sales') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <button class="btn btn-success" data-toggle="modal" data-target="#addSparePartSaleModal">
            <i class="fas fa-plus"></i> {{ __('messages.add_spare_part_sale') }}
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
                <h3 class="card-title">{{ __('messages.spare_part_sales_list') }}</h3>
            </div>
            <div class="card-body">
                <table id="sparePartSalesTable" class="table table-center table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.spare_part_name') }}</th>
                            <th>{{ __('messages.quantity') }}</th>
                            <th>{{ __('messages.total_price') }}</th>
                            <th>{{ __('messages.sale_date') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Spare Part Sale Modal -->
<div class="modal fade" id="addSparePartSaleModal" tabindex="-1" role="dialog" aria-labelledby="addSparePartSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('spare-part-sales.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addSparePartSaleModalLabel">{{ __('messages.add_spare_part_sale_modal_title') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="sparePartId">{{ __('messages.spare_part') }}</label>
                        <select name="spare_part_id" class="form-control" id="sparePartId" required>
                            <option value="">{{ __('messages.select_spare_part') }}</option>
                            @foreach($spareParts as $sparePart)
                                <option value="{{ $sparePart->id }}">{{ $sparePart->name }} ({{ $sparePart->quantity }} {{ __('messages.available') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="saleQuantity">{{ __('messages.quantity') }}</label>
                        <input type="number" name="quantity" class="form-control" id="saleQuantity" required>
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
        $('#sparePartSalesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('spare-part-sales.data') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'spare_part_name', name: 'spare_part_name' },
                { data: 'quantity', name: 'quantity' },
                { data: 'total_price', name: 'total_price' },
                { data: 'sale_date', name: 'sale_date' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                search: "{{ __('messages.search_spare_part_sales') }}",
                lengthMenu: "{{ __('messages.show_entries') }}",
                zeroRecords: "{{ __('messages.no_spare_part_sales_found') }}",
                info: "{{ __('messages.showing_info') }}",
                infoEmpty: "{{ __('messages.no_spare_part_sales_available') }}",
                processing: "{{ __('messages.processing') }}",
                paginate: {
                    next: "{{ __('messages.next') }}",
                    previous: "{{ __('messages.previous') }}"
                }
            },
            order: [[4, 'desc']]
        });
    });
</script>
@endsection
