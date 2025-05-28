@extends('layouts.master')

@section('title')
{{ __('messages.add_car') }} - {{ __('messages.CAR_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.cars') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.add_car') }}</span>
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
                    <h4 class="card-title mg-b-0">{{ __('messages.add_car') }}</h4>
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

                <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.brand') }}</label>
                                <input type="text" name="brand" class="form-control" placeholder="{{ __('messages.enter_brand_name') }}" value="{{ old('brand') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.model') }}</label>
                                <input type="text" name="model" class="form-control" placeholder="{{ __('messages.enter_model_name') }}" value="{{ old('model') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.year') }}</label>
                                <input type="text" name="year" class="form-control" placeholder="{{ __('messages.enter_year') }}" value="{{ old('year') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.plate_number') }}</label>
                                <input type="text" name="plate_number" class="form-control" placeholder="{{ __('messages.enter_plate_number') }}" value="{{ old('plate_number') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.color') }}</label>
                                <input type="text" name="color" class="form-control" placeholder="{{ __('messages.enter_color') }}" value="{{ old('color') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.daily_rate') }}</label>
                                <input type="number" name="daily_rate" class="form-control" placeholder="{{ __('messages.enter_daily_rate') }}" value="{{ old('daily_rate') }}" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.weekly_rate') }}</label>
                                <input type="number" name="weekly_rate" class="form-control" placeholder="{{ __('messages.enter_weekly_rate') }}" value="{{ old('weekly_rate') }}" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.monthly_rate') }}</label>
                                <input type="number" name="monthly_rate" class="form-control" placeholder="{{ __('messages.enter_monthly_rate') }}" value="{{ old('monthly_rate') }}" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.description') }}</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="{{ __('messages.enter_description') }}">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.image') }}</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Initialize datepicker for year field
        $('#year').datepicker({
            format: 'yyyy',
            viewMode: "years", 
            minViewMode: "years",
            autoclose: true
        });
    });
</script>
@endsection
