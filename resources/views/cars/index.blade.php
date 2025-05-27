@extends('layouts.master')

@section('content')
@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
<!-- Page Header -->
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <div>
            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">{{ __('messages.cars') }}</h2>
            <p>{{ __('messages.cars_management') }}</p>
        </div>
    </div>
    <div class="main-dashboard-header-right">
        <a href="{{ route('cars.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            {{ __('messages.add_car') }}
        </a>
    </div>
</div>
<!-- /Page Header -->

<!-- Cars Table -->
<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.cars_list') }}</h3>
            </div>
            <div class="card-body">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="carsTable">
                        <thead>
                            <tr>
                                <th>{{ __('messages.id') }}</th>
                                <th>{{ __('messages.image') }}</th>
                                <th>{{ __('messages.brand') }}</th>
                                <th>{{ __('messages.model') }}</th>
                                <th>{{ __('messages.plate_number') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.daily_rate') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cars as $car)
                            <tr>
                                <td>{{ $car->id }}</td>
                                <td>
                                    @if($car->image)
                                    <img src="{{ asset('storage/' . $car->image) }}" alt="{{ $car->brand }} {{ $car->model }}" class="wd-50 ht-50 rounded" style="object-fit: cover;">
                                    @else
                                    <div class="wd-50 ht-50 rounded bg-gray-200 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-car text-muted"></i>
                                    </div>
                                    @endif
                                </td>
                                <td>{{ $car->brand }}</td>
                                <td>{{ $car->model }}</td>
                                <td>{{ $car->plate_number }}</td>
                                <td>
                                    <span class="badge badge-{{ $car->status == 'available' ? 'success' : ($car->status == 'rented' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($car->status) }}
                                    </span>
                                </td>
                                <td>{{ number_format($car->daily_rate, 2) }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('cars.show', $car->id) }}" class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('car-edit')
                                        <a href="{{ route('cars.edit', $car->id) }}" class="btn btn-sm btn-primary" title="{{ __('messages.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('car-delete')
                                        <form action="{{ route('cars.destroy', $car->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.confirm_delete') }}')" title="{{ __('messages.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
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

<!-- Add Modal -->
<div class="modal fade" id="addCarModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.add_car') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Form fields will be added here -->
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editCarModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.edit_car') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('cars.update', 0) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- Form fields will be added here -->
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewCarModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.car_details') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Car details will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#carsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
            },
            responsive: true,
            order: [[0, 'desc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "{{ __('messages.all') }}"]]
        });
    });
</script>
@endsection
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cars Management</h3>
                    @can('car-create')
                    <div class="card-tools">
                        <a href="{{ route('cars.create') }}" class="btn btn-success">Add New Car</a>
                    </div>
                    @endcan
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Plate Number</th>
                                    <th>Status</th>
                                    <th>Daily Rate</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cars as $car)
                                <tr>
                                    <td>{{ $car->id }}</td>
                                    <td>
                                        @if($car->image)
                                        <img src="{{ asset('storage/' . $car->image) }}" alt="Car Image" style="max-width: 50px;">
                                        @else
                                        <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>{{ $car->brand }}</td>
                                    <td>{{ $car->model }}</td>
                                    <td>{{ $car->plate_number }}</td>
                                    <td>
                                        <span class="badge badge-{{ $car->status == 'available' ? 'success' : ($car->status == 'rented' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($car->status) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($car->daily_rate, 2) }}</td>
                                    <td>
                                        <form action="{{ route('cars.destroy', $car->id) }}" method="POST">
                                            <a class="btn btn-info btn-sm" href="{{ route('cars.show', $car->id) }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('car-edit')
                                            <a class="btn btn-primary btn-sm" href="{{ route('cars.edit', $car->id) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('car-delete')
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this car?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {!! $cars->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
