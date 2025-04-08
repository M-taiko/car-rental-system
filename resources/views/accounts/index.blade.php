@extends('layouts.master')

@section('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endsection

@section('title')
    {{ __('messages.accounts') }} - {{ __('messages.BIKE_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.accounts') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.manage_accounts') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <form method="GET" action="{{ route('accounts.index') }}">
            <div class="input-group">
                <input type="month" name="month" class="form-control" value="{{ $month }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">{{ __('messages.filter') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __('messages.total_income') }}</h5>
                <p class="card-text">{{ $totalIncome }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __('messages.total_expenses') }}</h5>
                <p class="card-text">{{ $totalExpenses }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __('messages.balance') }}</h5>
                <p class="card-text">{{ $balance }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.income_list') }}</h3>
            </div>
            <div class="card-body">
                <table id="incomeTable" class="table table-center table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.type') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.description') }}</th>
                            <th>{{ __('messages.date') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('messages.expenses_list') }}</h3>
            </div>
            <div class="card-body">
                <table id="expensesTable" class="table table-center table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.type') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.description') }}</th>
                            <th>{{ __('messages.date') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        var month = "{{ $month }}";

        var incomeTable = $('#incomeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('accounts.income') }}",
                data: { month: month }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'type', name: 'type' },
                { data: 'amount', name: 'amount' },
                { data: 'description', name: 'description' },
                { data: 'date', name: 'date' }
            ],
            language: {
                search: "{{ __('messages.search_income') }}",
                lengthMenu: "{{ __('messages.show_entries') }}",
                zeroRecords: "{{ __('messages.no_income_found') }}",
                info: "{{ __('messages.showing_info') }}",
                infoEmpty: "{{ __('messages.no_income_available') }}",
                processing: "{{ __('messages.processing') }}",
                paginate: {
                    next: "{{ __('messages.next') }}",
                    previous: "{{ __('messages.previous') }}"
                }
            },
            order: [[4, 'desc']]
        });

        var expensesTable = $('#expensesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('accounts.expenses') }}",
                data: { month: month }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'type', name: 'type' },
                { data: 'amount', name: 'amount' },
                { data: 'description', name: 'description' },
                { data: 'date', name: 'date' }
            ],
            language: {
                search: "{{ __('messages.search_expenses') }}",
                lengthMenu: "{{ __('messages.show_entries') }}",
                zeroRecords: "{{ __('messages.no_expenses_found') }}",
                info: "{{ __('messages.showing_info') }}",
                infoEmpty: "{{ __('messages.no_expenses_available') }}",
                processing: "{{ __('messages.processing') }}",
                paginate: {
                    next: "{{ __('messages.next') }}",
                    previous: "{{ __('messages.previous') }}"
                }
            },
            order: [[4, 'desc']]
        });
    });
</script>
@endsection
