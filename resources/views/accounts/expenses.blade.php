@extends('layouts.master')

@section('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('title')
    {{ __('messages.expenses') }} - {{ __('messages.BIKE_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.accounts') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.expenses') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        @can('create-accounts')
            <button class="btn btn-primary" data-toggle="modal" data-target="#addExpenseModal">
                <i class="fas fa-plus"></i> {{ __('messages.add_expense') }}
            </button>
        @endcan
        <form method="GET" class="ml-2">
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

<!-- Add Expense Modal -->
@can('create-accounts')
<div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addExpenseForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addExpenseModalLabel">{{ __('messages.add_expense') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('messages.expense_type') }}</label>
                        <select name="expense_type" class="form-control" required>
                            <option value="">{{ __('messages.select_type') }}</option>
                            @foreach(App\Models\Expense::getTypes() as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
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
                        <input type="datetime-local" name="date" class="form-control" required value="{{ date('Y-m-d\TH:i') }}">
                    </div>
                    <input type="hidden" name="type" value="expense">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
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
    var month = "{{ $month }}";

    var table = $('#expensesTable').DataTable({
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
                        var showUrl = "{{ route('accounts.show', '__ID__') }}".replace('__ID__', data);
                        actions += '<a href="' + showUrl + '" class="btn btn-info btn-sm mx-1"><i class="fas fa-eye"></i> {{ __("messages.view") }}</a>';
                    @endcan
                    @can('delete-accounts')
                        actions += '<button class="btn btn-danger btn-sm mx-1 delete-account" data-id="' + data + '"><i class="fas fa-trash"></i> {{ __("messages.delete") }}</button>';
                    @endcan
                    return actions;
                }
            }
        ],
        language: {
            search: "{{ __('messages.search') }}",
            lengthMenu: "{{ __('messages.show_entries') }}",
            zeroRecords: "{{ __('messages.no_records') }}",
            info: "{{ __('messages.showing_page') }}",
            infoEmpty: "{{ __('messages.no_records_available') }}",
            infoFiltered: "{{ __('messages.filtered_from') }}",
            processing: "{{ __('messages.processing') }}",
            paginate: {
                next: "{{ __('messages.next') }}",
                previous: "{{ __('messages.previous') }}"
            }
        }
    });

    // Add Expense
    $('#addExpenseForm').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serializeArray();
        var expenseType = formData.find(item => item.name === 'expense_type')?.value;
        var description = formData.find(item => item.name === 'description')?.value || '';

        // Combine expense type with description
        var fullDescription = expenseType + ' - ' + description;

        // Update the description in the form data
        formData = formData.filter(item => item.name !== 'description' && item.name !== 'expense_type');
        formData.push({ name: 'description', value: fullDescription });

        $.ajax({
            url: "{{ route('accounts.store') }}",
            method: 'POST',
            data: $.param(formData),
            success: function(response) {
                if (response.success) {
                    $('#addExpenseModal').modal('hide');
                    $('#addExpenseForm')[0].reset();
                    table.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("messages.success") }}',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("messages.error") }}',
                    text: xhr.responseJSON?.message || '{{ __("messages.something_went_wrong") }}'
                });
            }
        });
    });

    // Delete Expense
    $(document).on('click', '.delete-account', function() {
        var accountId = $(this).data('id');
        Swal.fire({
            title: '{{ __("messages.are_you_sure") }}',
            text: '{{ __("messages.delete_confirmation") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("messages.yes_delete") }}',
            cancelButtonText: '{{ __("messages.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('accounts') }}/" + accountId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("messages.deleted") }}',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("messages.error") }}',
                            text: xhr.responseJSON?.message || '{{ __("messages.something_went_wrong") }}'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection
