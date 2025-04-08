@extends('layouts.master')

@section('title')
    {{ __('messages.rental_invoice') }} - {{ __('messages.BIKE_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.rental_invoice') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.view_invoice') }}</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.rental_invoice') }} #{{ $invoice['rental_id'] }}</h3>
            </div>
            <div class="card-body">
                <div id="invoiceContent">
                    <p><strong>{{ __('messages.bike_name') }}:</strong> {{ $invoice['bike_name'] }}</p>
                    <p><strong>{{ __('messages.user_name') }}:</strong> {{ $invoice['user_name'] }}</p>
                    <p><strong>{{ __('messages.start_date') }}:</strong> {{ explode(' ', $invoice['start_time'])[0] }}</p>
                    <p><strong>{{ __('messages.start_time') }}:</strong> {{ implode(' ', array_slice(explode(' ', $invoice['start_time']), 1)) }}</p>
                    <p><strong>{{ __('messages.end_date') }}:</strong> {{ explode(' ', $invoice['end_time'])[0] }}</p>
                    <p><strong>{{ __('messages.end_time') }}:</strong> {{ implode(' ', array_slice(explode(' ', $invoice['end_time']), 1)) }}</p>
                    <p><strong>{{ __('messages.hours') }}:</strong> {{ $invoice['hours'] }}</p>
                    <p><strong>{{ __('messages.price_per_hour') }}:</strong> {{ $invoice['price_per_hour'] }}</p>
                    <p><strong>{{ __('messages.total_cost') }}:</strong> {{ $invoice['total_cost'] }}</p>
                </div>
                <div class="text-right">
                    <button class="btn btn-primary" onclick="printInvoice()">{{ __('messages.print') }}</button>
                    <a href="{{ route('rentals.index') }}" class="btn btn-secondary">{{ __('messages.close') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function printInvoice() {
        var printContents = document.getElementById('invoiceContent').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
</script>
@endsection
