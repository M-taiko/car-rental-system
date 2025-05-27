@extends('layouts.master')

@section('title')
    {{ __('messages.spare_parts_profit_report') }} - {{ __('messages.CAR_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.maintenance') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.spare_parts_profit_report') }}</span>
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
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('messages.spare_part_name') }}</th>
                                <th>{{ __('messages.quantity_used') }}</th>
                                <th>{{ __('messages.purchase_price') }}</th>
                                <th>{{ __('messages.selling_price') }}</th>
                                <th>{{ __('messages.profit_per_unit') }}</th>
                                <th>{{ __('messages.total_profit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalQuantity = 0;
                                $totalPurchase = 0;
                                $totalSelling = 0;
                                $totalProfit = 0;
                            @endphp
                            @foreach($report as $item)
                                <tr>
                                    <td>{{ $item['spare_part_name'] }}</td>
                                    <td>{{ $item['quantity_used'] }}</td>
                                    <td>{{ number_format($item['purchase_price'], 2) }}</td>
                                    <td>{{ number_format($item['selling_price'], 2) }}</td>
                                    <td>{{ number_format($item['profit_per_unit'], 2) }}</td>
                                    <td>{{ number_format($item['total_profit'], 2) }}</td>
                                </tr>
                                @php
                                    $totalQuantity += $item['quantity_used'];
                                    $totalPurchase += $item['purchase_price'] * $item['quantity_used'];
                                    $totalSelling += $item['selling_price'] * $item['quantity_used'];
                                    $totalProfit += $item['total_profit'];
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>{{ __('messages.total') }}</th>
                                <th>{{ $totalQuantity }}</th>
                                <th>{{ number_format($totalPurchase, 2) }}</th>
                                <th>{{ number_format($totalSelling, 2) }}</th>
                                <th>-</th>
                                <th>{{ number_format($totalProfit, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // يمكنك إضافة أي سلوك JavaScript إضافي هنا
});
</script>
@endsection
