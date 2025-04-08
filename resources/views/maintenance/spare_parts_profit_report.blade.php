@extends('layouts.master')

@section('title')
    {{ __('messages.spare_parts_profit_report') }} - {{ __('messages.BIKE_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.spare_parts_profit_report') }}</h4>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.spare_parts_profit_report') }}</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.part_name') }}</th>
                            <th>{{ __('messages.quantity_used') }}</th>
                            <th>{{ __('messages.purchase_price') }}</th>
                            <th>{{ __('messages.selling_price') }}</th>
                            <th>{{ __('messages.profit_per_unit') }}</th>
                            <th>{{ __('messages.total_profit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report as $item)
                            <tr>
                                <td>{{ $item['spare_part_name'] }}</td>
                                <td>{{ $item['quantity_used'] }}</td>
                                <td>{{ $item['purchase_price'] }}</td>
                                <td>{{ $item['selling_price'] }}</td>
                                <td>{{ $item['profit_per_unit'] }}</td>
                                <td>{{ $item['total_profit'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
