@extends('layouts.master')

@section('title')
{{ __('messages.edit_driver') }} - {{ __('messages.CAR_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.drivers') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.edit_driver') }}</span>
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
                    <h4 class="card-title mg-b-0">{{ __('messages.edit_driver') }}</h4>
                    <a href="{{ route('driver.index') }}" class="btn btn-primary">{{ __('messages.back_to_list') }}</a>
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

                <form action="{{ route('driver.update', $driver->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ __('messages.name') }}</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" value="{{ old('name', $driver->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="Enter phone number" value="{{ old('phone', $driver->phone) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="license_number">License Number</label>
                                <input type="text" name="license_number" class="form-control" placeholder="Enter license number" value="{{ old('license_number', $driver->license_number) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="license_expiry">License Expiry Date</label>
                                <input type="date" name="license_expiry" class="form-control" value="{{ old('license_expiry', $driver->license_expiry->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="daily_rate">Daily Rate</label>
                                <input type="number" name="daily_rate" class="form-control" placeholder="Enter daily rate" value="{{ old('daily_rate', $driver->daily_rate) }}" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="available" {{ $driver->status == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="assigned" {{ $driver->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                    <option value="off_duty" {{ $driver->status == 'off_duty' ? 'selected' : '' }}>Off Duty</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Enter address">{{ old('address', $driver->address) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Enter notes">{{ old('notes', $driver->notes) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.image') }}</label>
                                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                                @if($driver->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/settings/' . $driver->image) }}" alt="Current Driver Image" class="img-fluid rounded" style="max-width: 200px;">
                                    <p class="text-muted">{{ __('messages.current_image_will_be_replaced') }}</p>
                                </div>
                                @endif
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="preview" class="img-fluid rounded" style="max-width: 200px;">
                                </div>
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
@endsection
