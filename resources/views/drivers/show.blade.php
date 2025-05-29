@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Driver Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('driver.index') }}" class="btn btn-default">Back to List</a>
                        @can('driver-edit')
                        <a href="{{ route('driver.edit', $driver->id) }}" class="btn btn-primary">Edit</a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($driver->image)
                            <div class="text-center mb-4">
                                <img src="{{ asset('storage/' . $driver->image) }}" alt="Driver Image" class="img-fluid rounded" style="max-width: 300px;">
                            </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Name:</label>
                                        <p>{{ $driver->name }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone:</label>
                                        <p>{{ $driver->phone }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>License Number:</label>
                                        <p>{{ $driver->license_number }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>License Expiry:</label>
                                        <p>{{ $driver->license_expiry->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status:</label>
                                        <p>
                                            <span class="badge badge-{{ $driver->status == 'available' ? 'success' : ($driver->status == 'assigned' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($driver->status) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label>Daily Rate:</label>
                                        <p>{{ number_format($driver->daily_rate, 2) }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Address:</label>
                                        <p>{{ $driver->address }}</p>
                                    </div>
                                    @if($driver->notes)
                                    <div class="form-group">
                                        <label>Notes:</label>
                                        <p>{{ $driver->notes }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($driver->rentals->count() > 0)
                    <div class="mt-4">
                        <h4>Rental History</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Car</th>
                                        <th>Customer</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Driver Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($driver->rentals as $rental)
                                    <tr>
                                        <td>{{ $rental->car->brand }} {{ $rental->car->model }}</td>
                                        <td>{{ $rental->customer->name }}</td>
                                        <td>{{ $rental->start_time->format('Y-m-d H:i') }}</td>
                                        <td>{{ $rental->end_time->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $rental->status == 'active' ? 'success' : ($rental->status == 'completed' ? 'info' : 'danger') }}">
                                                {{ ucfirst($rental->status) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($rental->driver_rate, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
