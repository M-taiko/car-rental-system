@extends('layouts.master')

@section('title')
    {{ __('messages.account_details') }} - {{ __('messages.BIKE_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.accounts') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.account_details') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <a href="{{ route('accounts.index') }}" class="btn btn-primary">{{ __('messages.back') }}</a>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.account_details') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __('messages.type') }}:</strong> {{ $account->type == 'income' ? __('messages.income') : __('messages.expense') }}</p>
                        <p><strong>{{ __('messages.amount') }}:</strong> {{ $account->amount }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>{{ __('messages.description') }}:</strong> {{ $account->description ?? '-' }}</p>
                        <p><strong>{{ __('messages.date') }}:</strong> {{ date('Y-m-d H:i', strtotime($account->date)) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
