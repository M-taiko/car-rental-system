@extends('layouts.master')

@section('title')
{{ __('messages.car_details') }} - {{ __('messages.CAR_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.cars') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.car_details') }}</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-0">{{ $car->brand }} {{ $car->model }}</h4>
                        <div class="btn-group">
                            <a href="{{ route('cars.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}</a>
                            @can('car-edit')
                            <a href="{{ route('cars.edit', $car->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> {{ __('messages.edit') }}</a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.brand') }}:</label>
                                <p class="tx-semibold">{{ $car->brand }}</p>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.model') }}:</label>
                                <p class="tx-semibold">{{ $car->model }}</p>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.year') }}:</label>
                                <p class="tx-semibold">{{ $car->year }}</p>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.plate_number') }}:</label>
                                <p class="tx-semibold">{{ $car->plate_number }}</p>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.color') }}:</label>
                                <p class="tx-semibold">{{ $car->color }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.daily_rate') }}:</label>
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
                                        <td>{{ number_format($rental->actual_amount, 2) }}</td>
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
