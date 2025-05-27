@extends('layouts.master')

@section('title')
    {{ __('messages.expense_details') }} - {{ __('messages.BIKE_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.expenses') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.expense_details') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <a href="{{ route('expenses.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.expense_details') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __('messages.expense_type') }}:</strong> {{ App\Models\Expense::getTypes()[$expense->type] ?? $expense->type }}</p>
                        <p><strong>{{ __('messages.amount') }}:</strong> {{ $expense->amount }}</p>
                        <p><strong>{{ __('messages.description') }}:</strong> {{ $expense->description ?? 'N/A' }}</p>
                        <p><strong>{{ __('messages.date') }}:</strong> {{ $expense->date ? date('Y-m-d H:i:s', strtotime($expense->date)) : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        @if($account)
                            <p><strong>{{ __('messages.account_type') }}:</strong> {{ $account->type }}</p>
                            <p><strong>{{ __('messages.account_description') }}:</strong> {{ $account->description }}</p>
                            <p><strong>{{ __('messages.account_date') }}:</strong> {{ $account->date ? date('Y-m-d H:i:s', strtotime($account->date)) : 'N/A' }}</p>
                        @else
                            <p>{{ __('messages.no_account_entry') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
