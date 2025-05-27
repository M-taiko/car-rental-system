@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Car Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('cars.index') }}" class="btn btn-default">Back to List</a>
                        @can('car-edit')
                        <a href="{{ route('cars.edit', $car->id) }}" class="btn btn-primary">Edit</a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Brand:</label>
                                <p>{{ $car->brand }}</p>
                            </div>
                            <div class="form-group">
                                <label>Model:</label>
                                <p>{{ $car->model }}</p>
                            </div>
                            <div class="form-group">
                                <label>Year:</label>
                                <p>{{ $car->year }}</p>
                            </div>
                            <div class="form-group">
                                <label>Plate Number:</label>
                                <p>{{ $car->plate_number }}</p>
                            </div>
                            <div class="form-group">
                                <label>Color:</label>
                                <p>{{ $car->color }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Daily Rate:</label>
                                <p>{{ number_format($car->daily_rate, 2) }}</p>
                            </div>
                            <div class="form-group">
                                <label>Weekly Rate:</label>
                                <p>{{ number_format($car->weekly_rate, 2) }}</p>
                            </div>
                            <div class="form-group">
                                <label>Monthly Rate:</label>
                                <p>{{ number_format($car->monthly_rate, 2) }}</p>
                            </div>
                            <div class="form-group">
                                <label>Status:</label>
                                <p>
                                    <span class="badge badge-{{ $car->status == 'available' ? 'success' : ($car->status == 'rented' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($car->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description:</label>
                                <p>{{ $car->description ?? 'No description available' }}</p>
                            </div>
                        </div>
                        @if($car->image)
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Image:</label>
                                <div>
                                    <img src="{{ asset('storage/' . $car->image) }}" alt="Car Image" class="img-fluid" style="max-width: 300px;">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($car->rentals->count() > 0)
                    <div class="mt-4">
                        <h4>Rental History</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($car->rentals as $rental)
                                    <tr>
                                        <td>{{ $rental->customer->name }}</td>
                                        <td>{{ $rental->start_time->format('Y-m-d H:i') }}</td>
                                        <td>{{ $rental->end_time->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $rental->status == 'active' ? 'success' : ($rental->status == 'completed' ? 'info' : 'danger') }}">
                                                {{ ucfirst($rental->status) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($rental->total_amount, 2) }}</td>
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
