@extends('layouts.master')

@section('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                        <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addAccountModal">{{ __('messages.add_account') }}</button>
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

    <!-- Modal for Adding Account -->
    @can('create-accounts')
    <div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-labelledby="addAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAccountModalLabel">{{ __('messages.add_account') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="addAccountForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('messages.account_type') }}</label>
                            <select name="type" class="form-control" required>
                                <option value="">{{ __('messages.select_type') }}</option>
                                <option value="income">{{ __('messages.income') }}</option>
                                <option value="expense">{{ __('messages.expense') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('messages.amount') }}</label>
                            <input type="number" name="amount" class="form-control" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('messages.description') }}</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>{{ __('messages.date') }}</label>
                            <input type="datetime-local" name="date" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('messages.add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
@else
    <div class="alert alert-danger">
        {{ __('messages.error') }}: You do not have permission to view accounts.
    </div>
@endcan
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
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
                            @can('view-accounts')
                                // تعديل طريقة إنشاء الرابط
                                var showUrl = "{{ route('accounts.show', '__ID__') }}".replace('__ID__', data);
                                actions += '<a href="' + showUrl + '" class="btn btn-info btn-sm">{{ __("messages.view") }}</a> ';
                            @endcan
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
                            @can('view-accounts')
                                // تعديل طريقة إنشاء الرابط
                                var showUrl = "{{ route('accounts.show', '__ID__') }}".replace('__ID__', data);
                                actions += '<a href="' + showUrl + '" class="btn btn-info btn-sm">{{ __("messages.view") }}</a> ';
                            @endcan
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

            // Add Account via Modal
            $('#addAccountForm').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('accounts.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("messages.success") }}',
                                text: response.message || '{{ __("messages.account_added_successfully") }}',
                            });
                            $('#addAccountModal').modal('hide');
                            $('#addAccountForm')[0].reset();
                            incomeTable.ajax.reload();
                            expensesTable.ajax.reload();
                            updateTotals();
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("messages.error") }}',
                            text: xhr.responseJSON?.message || '{{ __("messages.error_adding_account") }}',
                        });
                    }
                });
            });

            // Delete Account
            $(document).on('click', '.delete-account', function () {
                var accountId = $(this).data('id');
                Swal.fire({
                    title: '{{ __("messages.confirm_delete") }}',
                    text: '{{ __("messages.delete_account_confirmation") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{ __("messages.delete") }}',
                    cancelButtonText: '{{ __("messages.cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('accounts.destroy', ':id') }}".replace(':id', accountId),
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __("messages.success") }}',
                                        text: response.message || '{{ __("messages.account_deleted_successfully") }}',
                                    });
                                    incomeTable.ajax.reload();
                                    expensesTable.ajax.reload();
                                    updateTotals();
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("messages.error") }}',
                                    text: xhr.responseJSON?.message || '{{ __("messages.error_deleting_account") }}',
                                });
                            }
                        });
                    }
                });
            });

            // دالة لتحديث الإجماليات
            function updateTotals() {
                $.ajax({
                    url: "{{ route('accounts.totals') }}",
                    type: 'GET',
                    data: { month: month },
                    success: function (response) {
                        $('.card-text').eq(0).text(response.totalIncome);
                        $('.card-text').eq(1).text(response.totalExpenses);
                        $('.card-text').eq(2).text(response.balance);
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("messages.error") }}',
                            text: '{{ __("messages.error_fetching_totals") }}',
                        });
                    }
                });
            }
        @endcan
    });
</script>
@endsection
