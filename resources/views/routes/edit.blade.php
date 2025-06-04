@extends('layouts.master')

@section('title', __('messages.edit_route'))

@section('css')
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
<style>
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    .form-group.required .control-label:after {
        content: " *";
        color: #dc3545;
    }
</style>
@stop

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('messages.routes') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.edit') }}</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('messages.edit_route') }}: {{ $route->name }}</h4>
                <div class="float-right">
                    <a href="{{ route('routes.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-arrow-right"></i> {{ __('messages.back_to_list') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('routes.update', $route->id) }}" id="route-form" class="needs-validation" novalidate enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="control-label required">{{ __('messages.name') }}</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $route->name) }}" required
                                       placeholder="{{ __('messages.enter_route_name') }}">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="distance_km" class="control-label required">{{ __('messages.distance') }} ({{ __('messages.km') }})</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('distance_km') is-invalid @enderror" 
                                           id="distance_km" name="distance_km" 
                                           value="{{ old('distance_km', $route->distance_km) }}" required
                                           placeholder="{{ __('messages.enter_distance') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ __('messages.km') }}</span>
                                    </div>
                                    @error('distance_km')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_point" class="control-label required">{{ __('messages.start_point') }}</label>
                                <input type="text" class="form-control @error('start_point') is-invalid @enderror" 
                                       id="start_point" name="start_point" value="{{ old('start_point', $route->start_point) }}" required
                                       placeholder="{{ __('messages.enter_start_point') }}">
                                @error('start_point')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_point" class="control-label required">{{ __('messages.end_point') }}</label>
                                <input type="text" class="form-control @error('end_point') is-invalid @enderror" 
                                       id="end_point" name="end_point" value="{{ old('end_point', $route->end_point) }}" required
                                       placeholder="{{ __('messages.enter_end_point') }}">
                                @error('end_point')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="external_cost" class="control-label required">{{ __('messages.external_cost') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('external_cost') is-invalid @enderror" 
                                           id="external_cost" name="external_cost" 
                                           value="{{ old('external_cost', $route->external_cost) }}" required
                                           placeholder="{{ __('messages.enter_external_cost') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ config('settings.currency_symbol', 'SAR') }}</span>
                                    </div>
                                    @error('external_cost')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">{{ __('messages.external_cost_help') }}</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="internal_cost">{{ __('messages.internal_cost') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('internal_cost') is-invalid @enderror" 
                                           id="internal_cost" name="internal_cost" 
                                           value="{{ old('internal_cost', $route->internal_cost) }}"
                                           placeholder="{{ __('messages.enter_internal_cost') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ config('settings.currency_symbol', 'SAR') }}</span>
                                    </div>
                                    @error('internal_cost')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">{{ __('messages.internal_cost_help') }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">{{ __('messages.description') }}</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3"
                                          placeholder="{{ __('messages.enter_description') }}">{{ old('description', $route->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.status') }}</label>
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $route->is_active) ? 'checked' : '' }}>
                                    <label for="is_active">
                                        {{ __('messages.active') }}
                                    </label>
                                </div>
                                <small class="form-text text-muted">{{ __('messages.route_status_help') }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.update') }}
                        </button>
                        <a href="{{ route('routes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
<!-- Select2 -->
<script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<!-- jquery-validation -->
<script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>

<script>
    $(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            language: '{{ app()->getLocale() }}',
            placeholder: '{{ __('messages.select_an_option') }}',
            allowClear: true
        });
        
        // Form validation
        $('#route-form').validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 255
                },
                start_point: {
                    required: true,
                    maxlength: 255
                },
                end_point: {
                    required: true,
                    maxlength: 255
                },
                distance_km: {
                    required: true,
                    number: true,
                    min: 0.01
                },
                external_cost: {
                    required: true,
                    number: true,
                    min: 0
                },
                internal_cost: {
                    number: true,
                    min: 0
                }
            },
            messages: {
                name: {
                    required: "{{ __('validation.required', ['attribute' => __('messages.name')]) }}",
                    maxlength: "{{ __('validation.max.string', ['attribute' => __('messages.name'), 'max' => 255]) }}"
                },
                start_point: {
                    required: "{{ __('validation.required', ['attribute' => __('messages.start_point')]) }}",
                    maxlength: "{{ __('validation.max.string', ['attribute' => __('messages.start_point'), 'max' => 255]) }}"
                },
                end_point: {
                    required: "{{ __('validation.required', ['attribute' => __('messages.end_point')]) }}",
                    maxlength: "{{ __('validation.max.string', ['attribute' => __('messages.end_point'), 'max' => 255]) }}"
                },
                distance_km: {
                    required: "{{ __('validation.required', ['attribute' => __('messages.distance')]) }}",
                    number: "{{ __('validation.numeric', ['attribute' => __('messages.distance')]) }}",
                    min: "{{ __('validation.min.numeric', ['attribute' => __('messages.distance'), 'min' => 0.01]) }}"
                },
                external_cost: {
                    required: "{{ __('validation.required', ['attribute' => __('messages.external_cost')]) }}",
                    number: "{{ __('validation.numeric', ['attribute' => __('messages.external_cost')]) }}",
                    min: "{{ __('validation.min.numeric', ['attribute' => __('messages.external_cost'), 'min' => 0]) }}"
                },
                internal_cost: {
                    number: "{{ __('validation.numeric', ['attribute' => __('messages.internal_cost')]) }}",
                    min: "{{ __('validation.min.numeric', ['attribute' => __('messages.internal_cost'), 'min' => 0]) }}"
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                // Show loading state
                var submitBtn = $(form).find('button[type="submit"]');
                var originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("messages.updating") }}...');
                
                // Submit the form
                form.submit();
            }
        });
    });
</script>
@endpush
