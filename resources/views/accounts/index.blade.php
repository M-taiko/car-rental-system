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
        @can('view-accounts')
            <form method="GET" action="{{ route('accounts.index') }}">
                <div class="input-group">
                    <input type="month" name="month" class="form-control" value="{{ $month }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">{{ __('messages.filter') }}</button>
                    </div>
                </div>
            </form>
        @endcan
    </div>
</div>
@endsection

@section('content')
@can('view-accounts')
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
                    @can('create-accounts')
                        <a href="{{ route('accounts.create') }}" class="btn btn-primary float-right">{{ __('messages.add_account') }}</a>
                    @endcan
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
                                <th>{{ __('messages.actions') }}</th>
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
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-danger">
        {{ __('messages.error') }}: You do not have permission to view accounts.
    </div>
@endcan
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        @can('view-accounts')
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
                    { data: 'date', name: 'date' },
                    {
                        data: 'id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            var actions = '';
                            @can('delete-accounts')
                                actions += '<button class="btn btn-danger btn-sm delete-account" data-id="' + data + '">{{ __("messages.delete") }}</button>';
                            @endcan
                            return actions;
                        }
                    }
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
                    { data: 'date', name: 'date' },
                    {
                        data: 'id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            var actions = '';
                            @can('delete-accounts')
                                actions += '<button class="btn btn-danger btn-sm delete-account" data-id="' + data + '">{{ __("messages.delete") }}</button>';
                            @endcan
                            return actions;
                        }
                    }
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

            // JavaScript لحذف الحساب
            $(document).on('click', '.delete-account', function () {
                var accountId = $(this).data('id');
                if (confirm("{{ __('messages.confirm_delete') }}")) {
                    $.ajax({
                        url: "{{ route('accounts.destroy', ':id') }}".replace(':id', accountId),
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            if (response.success) {
                                incomeTable.ajax.reload();
                                expensesTable.ajax.reload();
                                alert("{{ __('messages.account_deleted_successfully') }}");
                            } else {
                                alert("{{ __('messages.error') }}: " + response.message);
                            }
                        },
                        error: function () {
                            alert("{{ __('messages.error') }}");
                        }
                    });
                }
            });
        @endcan
    });
</script>
@endsection
