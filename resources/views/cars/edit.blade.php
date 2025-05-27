@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Car</h3>
                    <div class="card-tools">
                        <a href="{{ route('cars.index') }}" class="btn btn-default">Back to List</a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('cars.update', $car->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="brand">Brand</label>
                                    <input type="text" name="brand" class="form-control" placeholder="Enter brand name" value="{{ old('brand', $car->brand) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="model">Model</label>
                                    <input type="text" name="model" class="form-control" placeholder="Enter model name" value="{{ old('model', $car->model) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="year">Year</label>
                                    <input type="text" name="year" class="form-control" placeholder="Enter year" value="{{ old('year', $car->year) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plate_number">Plate Number</label>
                                    <input type="text" name="plate_number" class="form-control" placeholder="Enter plate number" value="{{ old('plate_number', $car->plate_number) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="color">Color</label>
                                    <input type="text" name="color" class="form-control" placeholder="Enter color" value="{{ old('color', $car->color) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="daily_rate">Daily Rate</label>
                                    <input type="number" name="daily_rate" class="form-control" placeholder="Enter daily rate" value="{{ old('daily_rate', $car->daily_rate) }}" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="weekly_rate">Weekly Rate</label>
                                    <input type="number" name="weekly_rate" class="form-control" placeholder="Enter weekly rate" value="{{ old('weekly_rate', $car->weekly_rate) }}" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monthly_rate">Monthly Rate</label>
                                    <input type="number" name="monthly_rate" class="form-control" placeholder="Enter monthly rate" value="{{ old('monthly_rate', $car->monthly_rate) }}" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Enter description">{{ old('description', $car->description) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="image">Car Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    @if($car->image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $car->image) }}" alt="Current Car Image" style="max-width: 200px;">
                                        <p class="text-muted">Current image will be replaced if you upload a new one.</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
