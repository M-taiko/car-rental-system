@extends('layouts.master')

@section('css')
<!-- Include DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endsection

@section('title')
    {{ __('messages.bikes') }} - {{ __('messages.BIKE_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.bikes') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.manage_bikes') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <!-- Add New Bike Button -->
        @can('create-bikes')
            <button class="btn btn-success" data-toggle="modal" data-target="#addBikeModal">
                <i class="fas fa-plus"></i> {{ __('messages.add_bike') }}
            </button>
        @endcan
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
                <h3 class="card-title">{{ __('messages.bikes_list') }}</h3>
            </div>
            <div class="card-body">
                <!-- DataTable -->
                <table id="bikesTable" class="table table-center table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.bike_name') }}</th>
                            <th>{{ __('messages.type') }}</th>
                            <th>{{ __('messages.color') }}</th>
                            <th>{{ __('messages.price_per_hour') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- سيتم ملء هذا الجزء ديناميكيً **باستخد DataTables -->
                    </boby>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Bike Modal -->
@can('create-bikes')
    <div class="modal fade" id="addBikeModal" tabindex="-1" role="dialog" aria-labelledby="addBikeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('bikes.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBikeModalLabel">{{ __('messages.add_bike_modal_title') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="bikeName">{{ __('messages.bike_name') }}</label>
                            <input type="text" name="name" class="form-control" id="bikeName" required>
                        </div>
                        <div class="form-group">
                            <label for="bikeType">{{ __('messages.type') }}</label>
                            <input type="text" name="type" class="form-control" id="bikeType" required>
                        </div>
                        <div class="form-group">
                            <label for="bikeColor">{{ __('messages.color') }}</label>
                            <input type="text" name="color" class="form-control" id="bikeColor" required>
                        </div>
                        <div class="form-group">
                            <label for="bikePrice">{{ __('messages.price_per_hour') }}</label>
                            <input type="number" step="0.01" name="price_per_hour" class="form-control" id="bikePrice" required>
                        </div>
                        <div class="form-group">
                            <label for="bikeDescription">{{ __('messages.description') }}</label>
                            <textarea name="description" class="form-control" id="bikeDescription"></textarea>
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
@endcan

<!-- Edit Bike Modals (سيتم تحميلها ديناميكيًا عند الحاجة) -->
@endsection

@section('js')
<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<!-- Initialize DataTables -->
<script>
    $(document).ready(function () {
        $('#bikesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('bikes.data') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // للعرض فقط
                { data: 'name', name: 'name' },
                { data: 'type', name: 'type' },
                { data: 'color', name: 'color' },
                { data: 'price_per_hour', name: 'price_per_hour' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                search: "{{ __('messages.search_bikes') }}",
                lengthMenu: "{{ __('messages.show_entries') }}",
                zeroRecords: "{{ __('messages.no_bikes_found') }}",
                info: "{{ __('messages.showing_info') }}",
                infoEmpty: "{{ __('messages.no_bikes_available') }}",
                processing: "جاري التحميل...",
                paginate: {
                    next: "{{ __('messages.next') }}",
                    previous: "{{ __('messages.previous') }}"
                }
            },
            order: [[1, 'asc']] // الترتيب بناءً على العمود الثاني (name)
        });
    });
</script>
@endsection
