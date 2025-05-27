@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Drivers Management</h3>
                    @can('driver-create')
                    <div class="card-tools">
                        <a href="{{ route('drivers.create') }}" class="btn btn-success">Add New Driver</a>
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
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>License Number</th>
                                    <th>License Expiry</th>
                                    <th>Status</th>
                                    <th>Daily Rate</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($drivers as $driver)
                                <tr>
                                    <td>{{ $driver->id }}</td>
                                    <td>
                                        @if($driver->image)
                                        <img src="{{ asset('storage/' . $driver->image) }}" alt="Driver Image" style="max-width: 50px;">
                                        @else
                                        <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>{{ $driver->name }}</td>
                                    <td>{{ $driver->phone }}</td>
                                    <td>{{ $driver->license_number }}</td>
                                    <td>{{ $driver->license_expiry->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $driver->status == 'available' ? 'success' : ($driver->status == 'assigned' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($driver->status) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($driver->daily_rate, 2) }}</td>
                                    <td>
                                        <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST">
                                            <a class="btn btn-info btn-sm" href="{{ route('drivers.show', $driver->id) }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('driver-edit')
                                            <a class="btn btn-primary btn-sm" href="{{ route('drivers.edit', $driver->id) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('driver-delete')
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this driver?')">
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
                        {!! $drivers->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
