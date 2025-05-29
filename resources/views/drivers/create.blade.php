@extends('layouts.master')

@section('title')
{{ __('messages.add_driver') }} - {{ __('messages.CAR_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.drivers') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.add_driver') }}</span>
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
                    <h4 class="card-title mg-b-0">{{ __('messages.add_driver') }}</h4>
                    <a href="{{ route('drivers.index') }}" class="btn btn-primary">{{ __('messages.back_to_list') }}</a>
                </div>
            </div>
            <div class="card-body">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>{{ __('messages.error') }}</strong><br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger">
                    <strong>{{ __('messages.error') }}</strong><br><br>
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('drivers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.name') }}</label>
                                <input type="text" name="name" class="form-control" placeholder="{{ __('messages.enter_driver_name') }}" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.id_number') }}</label>
                                <input type="text" name="id_number" class="form-control" placeholder="{{ __('messages.enter_id_number') }}" value="{{ old('id_number') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.id_type') }}</label>
                                <select name="id_type" class="form-control" required>
                                    <option value="national_id">{{ __('messages.national_id') }}</option>
                                    <option value="iqama">{{ __('messages.iqama') }}</option>
                                    <option value="passport">{{ __('messages.passport') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.phone') }}</label>
                                <input type="text" name="phone" class="form-control" placeholder="{{ __('messages.enter_phone_number') }}" value="{{ old('phone') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.license_number') }}</label>
                                <input type="text" name="license_number" class="form-control" placeholder="{{ __('messages.enter_license_number') }}" value="{{ old('license_number') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.license_expiry') }}</label>
                                <input type="date" name="license_expiry" class="form-control" value="{{ old('license_expiry') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="daily_rate">Daily Rate</label>
                                <input type="number" name="daily_rate" class="form-control" placeholder="Enter daily rate" value="{{ old('daily_rate') }}" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Enter address">{{ old('address') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Enter notes">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.image') }}</label>
                                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="preview" class="img-fluid rounded" style="max-width: 200px;">
                                </div>
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
        // Initialize datepicker for license expiry
        $('#license_expiry').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });
</script>
@endsection
