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
        @can('account-create')
            <button class="btn btn-success mr-2" data-toggle="modal" data-target="#addIncomeModal">
                <i class="fas fa-plus"></i> {{ __('messages.add_income') }}
            </button>
            <button class="btn btn-warning mr-2" data-toggle="modal" data-target="#addExpenseModal">
                <i class="fas fa-minus"></i> {{ __('messages.add_expense') }}
            </button>
        @endcan
        @can('account-list')
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
@can('account-list')
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

    <!-- Modal for Adding Income -->
    @can('account-create')
    <div class="modal fade" id="addIncomeModal" tabindex="-1" role="dialog" aria-labelledby="addIncomeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addIncomeModalLabel">{{ __('messages.add_income') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="addIncomeForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="type" value="income">
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
                            <input type="datetime-local" name="date" class="form-control" required value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('messages.add_income') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Expense -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExpenseModalLabel">{{ __('messages.add_expense') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="addExpenseForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="type" value="expense">
                        <div class="form-group">
                            <label>{{ __('messages.expense_type') }}</label>
                            <select name="expense_type" class="form-control" required>
                                <option value="">{{ __('messages.select_type') }}</option>
                                <option value="maintenance">{{ __('messages.maintenance') }}</option>
                                <option value="fuel">{{ __('messages.fuel') }}</option>
                                <option value="salary">{{ __('messages.salary') }}</option>
                                <option value="rent">{{ __('messages.rent') }}</option>
                                <option value="utilities">{{ __('messages.utilities') }}</option>
                                <option value="insurance">{{ __('messages.insurance') }}</option>
                                <option value="marketing">{{ __('messages.marketing') }}</option>
                                <option value="other">{{ __('messages.other') }}</option>
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
                            <input type="datetime-local" name="date" class="form-control" required value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                        <button type="submit" class="btn btn-warning">{{ __('messages.add_expense') }}</button>
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
        // Setup AJAX CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        @can('account-list')
            var month = "{{ $month }}";

            // Handle Income Form Submission
            $('#addIncomeForm').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serializeArray();
                var data = {};

                // Convert form data to object
                $(formData).each(function(index, obj){
                    data[obj.name] = obj.value;
                });

                $.ajax({
                    url: "{{ route('accounts.storeIncome') }}",
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            // Properly close the modal using Bootstrap's modal method
                            $('#addIncomeModal').modal('hide');
                            // Clear the form
                            $('#addIncomeForm')[0].reset();
                            // Remove the modal backdrop manually if it persists
                            $('.modal-backdrop').remove();
                            // Remove modal-open class from body
                            $('body').removeClass('modal-open');

                            incomeTable.ajax.reload();
                            expensesTable.ajax.reload();
                            updateTotals();
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("messages.success") }}',
                                text: response.message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("messages.error") }}',
                                text: response.message || '{{ __("messages.something_went_wrong") }}'
                            });
                        }
                    },
                    error: function(xhr) {
                        var message = '';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            message = Object.values(xhr.responseJSON.errors).join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else {
                            message = '{{ __("messages.something_went_wrong") }}';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("messages.error") }}',
                            text: message
                        });
                    }
                });
            });

            // Handle Expense Form Submission
            $('#addExpenseForm').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serializeArray();
                var data = {};

                // Convert form data to object
                $(formData).each(function(index, obj){
                    data[obj.name] = obj.value;
                });

                $.ajax({
                    url: "{{ route('accounts.storeExpense') }}",
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            // Properly close the modal using Bootstrap's modal method
                            $('#addExpenseModal').modal('hide');
                            // Clear the form
                            $('#addExpenseForm')[0].reset();
                            // Remove the modal backdrop manually if it persists
                            $('.modal-backdrop').remove();
                            // Remove modal-open class from body
                            $('body').removeClass('modal-open');

                            incomeTable.ajax.reload();
                            expensesTable.ajax.reload();
                            updateTotals();
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("messages.success") }}',
                                text: response.message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("messages.error") }}',
                                text: response.message || '{{ __("messages.something_went_wrong") }}'
                            });
                        }
                    },
                    error: function(xhr) {
                        var message = '';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            message = Object.values(xhr.responseJSON.errors).join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else {
                            message = '{{ __("messages.something_went_wrong") }}';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("messages.error") }}',
                            text: message
                        });
                    }
                });
            });

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
