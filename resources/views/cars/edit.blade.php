@extends('layouts.master')

@section('title')
{{ __('messages.edit_car') }} - {{ __('messages.CAR_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.cars') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.edit_car') }}</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-0">{{ __('messages.edit_car') }}</h4>
                        <a href="{{ route('cars.index') }}" class="btn btn-primary">{{ __('messages.back_to_list') }}</a>
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
                                <label for="brand">{{ __('messages.brand') }}</label>
                                <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" 
                                    id="brand" value="{{ old('brand', $car->brand) }}" required>
                                @error('brand')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="model">{{ __('messages.model') }}</label>
                                    <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" 
                                    id="model" value="{{ old('model', $car->model) }}" required>
                                @error('model')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="year">{{ __('messages.year') }}</label>
                                    <input type="text" name="year" class="form-control @error('year') is-invalid @enderror" 
                                    id="year" value="{{ old('year', $car->year) }}" required>
                                @error('year')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plate_number">{{ __('messages.plate_number') }}</label>
                                    <input type="text" name="plate_number" class="form-control @error('plate_number') is-invalid @enderror" 
                                    id="plate_number" value="{{ old('plate_number', $car->plate_number) }}" required>
                                @error('plate_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="color">{{ __('messages.color') }}</label>
                                    <input type="text" name="color" class="form-control @error('color') is-invalid @enderror" 
                                    id="color" value="{{ old('color', $car->color) }}" required>
                                @error('color')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="daily_rate">{{ __('messages.daily_rate') }}</label>
                                    <input type="number" name="daily_rate" class="form-control @error('daily_rate') is-invalid @enderror" 
                                    id="daily_rate" value="{{ old('daily_rate', $car->daily_rate) }}" step="0.01" required>
                                @error('daily_rate')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="weekly_rate">{{ __('messages.weekly_rate') }}</label>
                                    <input type="number" name="weekly_rate" class="form-control @error('weekly_rate') is-invalid @enderror" 
                                    id="weekly_rate" value="{{ old('weekly_rate', $car->weekly_rate) }}" step="0.01" required>
                                @error('weekly_rate')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monthly_rate">{{ __('messages.monthly_rate') }}</label>
                                    <input type="number" name="monthly_rate" class="form-control @error('monthly_rate') is-invalid @enderror" 
                                    id="monthly_rate" value="{{ old('monthly_rate', $car->monthly_rate) }}" step="0.01" required>
                                @error('monthly_rate')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">{{ __('messages.description') }}</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                    id="description" rows="3" placeholder="Enter description">{{ old('description', $car->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="image">{{ __('messages.image') }}</label>
                                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                    id="image" accept="image/*">
                                @error('image')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                    @if($car->image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $car->image) }}" alt="Current Car Image" style="max-width: 200px;">
                                        <p class="text-muted">Current image will be replaced if you upload a new one.</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
