@extends('layouts.master')

@section('css')
<!-- Owl-carousel css -->
<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
<!-- Select2 css (for dropdown) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Bootstrap css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">{{ __('messages.welcome') }}</h2>
                <p>{{ __('messages.dashboard') }}</p>
            </div>
        </div>
        <div class="main-dashboard-header-right">
            <div>
                <label class="tx-13">{{ __('messages.customer_ratings') }}</label>
                <div class="main-star">
                    <i class="typcn typcn-star active"></i>
                    <i class="typcn typcn-star active"></i>
                    <i class="typcn typcn-star active"></i>
                    <i class="typcn typcn-star active"></i>
                    <i class="typcn typcn-star"></i>
                    <span>(14,873)</span>
                </div>
            </div>
            <div>
                <label class="tx-13">{{ __('messages.online_sales') }}</label>
                <h5>563,275</h5>
            </div>
            <div>
                <label class="tx-13">{{ __('messages.offline_sales') }}</label>
                <h5>783,675</h5>
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->
@endsection

@section('content')
    <div class="row row-sm">
        <!-- Well Productivity Chart -->
        <div class="col-xl-6 col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0">{{ __('messages.well_productivity') }}</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div>





    </div>
@endsection

@section('js')
<!-- Chart.js -->
<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS (for dropdown) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


@endsection
