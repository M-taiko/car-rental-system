@extends('layouts.master')

@php
    use App\Models\Setting;
@endphp

@section('css')
<link href="{{URL::asset('assets/plugins/fileuploads/css/fileupload.css')}}" rel="stylesheet" type="text/css"/>
<style>
    .logo-preview {
        max-width: 200px;
        max-height: 100px;
        margin-top: 10px;
    }
    .logo-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>
@endsection

@section('title')
    {{ __('messages.settings') }} - {{ __('messages.CAR_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.system_settings') }}</h4>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title mg-b-0">{{ __('messages.system_settings') }}</h4>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('setting.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Company Information -->
                        <div class="col-12 col-md-6">
                            <h5>{{ __('messages.company_information') }}</h5>

                            <div class="form-group">
                                <label for="company_name">{{ __('messages.company_name') }}</label>
                                <input type="text" name="company_name" id="company_name" class="form-control @error('company_name') is-invalid @enderror"
                                    value="{{ old('company_name', Setting::get('company_name')) }}" required>
                                @error('company_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="company_logo">{{ __('messages.company_logo') }}</label>
                                <input type="file" name="company_logo" id="company_logo" class="form-control @error('company_logo') is-invalid @enderror"
                                    accept="image/*" onchange="previewLogo(this)">
                                @error('company_logo')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <div class="logo-preview mt-2">
                                    @php
                                        $logo = Setting::get('company_logo');
                                    @endphp
                                    <img src="{{ $logo ? Storage::url($logo) : asset('assets/img/brand/logo.png') }}"
                                         alt="{{ __('messages.company_logo') }}"
                                         id="logoPreview">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="company_address">{{ __('messages.company_address') }}</label>
                                <textarea name="company_address" id="company_address" class="form-control @error('company_address') is-invalid @enderror"
                                    required>{{ old('company_address', Setting::get('company_address')) }}</textarea>
                                @error('company_address')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="company_phone">{{ __('messages.company_phone') }}</label>
                                <input type="text" name="company_phone" id="company_phone" class="form-control @error('company_phone') is-invalid @enderror"
                                    value="{{ old('company_phone', Setting::get('company_phone')) }}" required>
                                @error('company_phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="company_email">{{ __('messages.company_email') }}</label>
                                <input type="email" name="company_email" id="company_email" class="form-control @error('company_email') is-invalid @enderror"
                                    value="{{ old('company_email', Setting::get('company_email')) }}" required>
                                @error('company_email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="company_website">{{ __('messages.company_website') }}</label>
                                <input type="url" name="company_website" id="company_website" class="form-control @error('company_website') is-invalid @enderror"
                                    value="{{ old('company_website', Setting::get('company_website')) }}">
                                @error('company_website')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="tax_number">{{ __('messages.tax_number') }}</label>
                                <input type="text" name="tax_number" id="tax_number" class="form-control @error('tax_number') is-invalid @enderror"
                                    value="{{ old('tax_number', Setting::get('tax_number')) }}">
                                @error('tax_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="commercial_number">{{ __('messages.commercial_number') }}</label>
                                <input type="text" name="commercial_number" id="commercial_number" class="form-control @error('commercial_number') is-invalid @enderror"
                                    value="{{ old('commercial_number', Setting::get('commercial_number')) }}">
                                @error('commercial_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Invoice Settings -->
                        <div class="col-12 col-md-6">
                            <h5>{{ __('messages.invoice_settings') }}</h5>

                            <div class="form-group">
                                <label for="invoice_prefix">{{ __('messages.invoice_prefix') }}</label>
                                <input type="text" name="invoice_prefix" id="invoice_prefix" class="form-control @error('invoice_prefix') is-invalid @enderror"
                                    value="{{ old('invoice_prefix', Setting::get('invoice_prefix')) }}">
                                @error('invoice_prefix')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="tax_rate">{{ __('messages.tax_rate') }}</label>
                                <div class="input-group">
                                    <input type="number" name="tax_rate" id="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror"
                                        value="{{ old('tax_rate', Setting::get('tax_rate')) }}" min="0" max="100" step="0.01" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                @error('tax_rate')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="invoice_footer">{{ __('messages.invoice_footer') }}</label>
                                <textarea name="invoice_footer" id="invoice_footer" class="form-control @error('invoice_footer') is-invalid @enderror">{{ old('invoice_footer', Setting::get('invoice_footer')) }}</textarea>
                                @error('invoice_footer')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <h5 class="mt-4">{{ __('messages.currency_settings') }}</h5>

                            <div class="form-group">
                                <label for="currency_symbol">{{ __('messages.currency_symbol') }}</label>
                                <input type="text" name="currency_symbol" id="currency_symbol" class="form-control @error('currency_symbol') is-invalid @enderror"
                                    value="{{ old('currency_symbol', Setting::get('currency_symbol')) }}" required>
                                @error('currency_symbol')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="currency_code">{{ __('messages.currency_code') }}</label>
                                <input type="text" name="currency_code" id="currency_code" class="form-control @error('currency_code') is-invalid @enderror"
                                    value="{{ old('currency_code', Setting::get('currency_code')) }}" required>
                                @error('currency_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="currency_position">{{ __('messages.currency_position') }}</label>
                                <select name="currency_position" id="currency_position" class="form-control @error('currency_position') is-invalid @enderror" required>
                                    <option value="before" {{ old('currency_position', Setting::get('currency_position')) == 'before' ? 'selected' : '' }}>
                                        {{ __('messages.before_amount') }}
                                    </option>
                                    <option value="after" {{ old('currency_position', Setting::get('currency_position')) == 'after' ? 'selected' : '' }}>
                                        {{ __('messages.after_amount') }}
                                    </option>
                                </select>
                                @error('currency_position')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('messages.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{URL::asset('assets/plugins/fileuploads/js/fileupload.js')}}"></script>
<script src="{{URL::asset('assets/plugins/fileuploads/js/file-upload.js')}}"></script>
<script>
    // Update file input label with selected filename
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);

        // Preview selected image
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('.preview-image').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    function previewLogo(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logoPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
