@extends('layouts.master')

@section('content')
<!-- Page Header -->
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <div>
            <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">{{ __('messages.profile') }}</h2>
            <p>{{ __('messages.profile_management') }}</p>
        </div>
    </div>
</div>
<!-- /Page Header -->

<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="row">
                    <!-- Profile Image Section -->
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="profile-photo">
                               
                                     <img alt="{{ Auth::user()->name }}" id="profileImage"  class=" rounded-wd-150 ht-150 rounded-circle" src="{{ Auth::user()->profile_photo_path ? asset('settings/' . Auth::user()->profile_photo_path) : asset('assets/img/brand/user.png') }}" style="width: 40px; height: 40px; object-fit: cover;">

                            </div>
                            <div class="mt-4">
                                <h4>{{ Auth::user()->name }}</h4>
                                <p class="text-muted mb-2">{{ auth()->user()->getRoleNames()->first() }}</p>
                            </div>
                            <div class="mt-4">
                                <form action="{{ route('profile.update.photo') }}" method="POST" enctype="multipart/form-data" class="d-inline">
                                    @csrf
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="profilePhoto" name="profile_photo" accept="image/*" onchange="previewImage(this)">
                                        <label class="custom-file-label" for="profilePhoto">{{ __('messages.choose_photo') }}</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-2">{{ __('messages.upload') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password Section -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('messages.change_password') }}</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('profile.update.password') }}" method="POST" class="needs-validation" novalidate>
                                    @csrf
                                    <div class="form-group">
                                        <label for="current_password">{{ __('messages.current_password') }}</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        <div class="invalid-feedback">{{ __('messages.password_required') }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="new_password">{{ __('messages.new_password') }}</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <div class="invalid-feedback">{{ __('messages.password_required') }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_password">{{ __('messages.confirm_password') }}</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <div class="invalid-feedback">{{ __('messages.password_required') }}</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{ __('messages.update_password') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.success') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="successMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.error') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="errorMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#profileImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
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
@endsection
