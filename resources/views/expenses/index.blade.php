@extends('layouts.master')

@section('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .modal-body .loading-spinner {
        display: none;
        text-align: center;
        padding: 20px;
    }
    .modal-body .loading-spinner i {
        font-size: 24px;
        color: #007bff;
    }
</style>
@endsection

@section('title')
    {{ __('messages.expenses') }} - {{ __('messages.BIKE_RENTAL_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.expenses') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.manage_expenses') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <!-- Add Expense Button -->
        @can('create-expenses')
            <button class="btn btn-success" data-toggle="modal" data-target="#addExpenseModal">
                <i class="fas fa-plus"></i> {{ __('messages.add_expense') }}
            </button>
        @endcan
    </div>
</div>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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
                            <th>{{ __('messages.expense_type') }}</th>
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
@can('create-expenses')
    <div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addExpenseModalLabel">{{ __('messages.add_expense') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="type">{{ __('messages.expense_type') }}</label>
                            <select name="type" class="form-control" id="type" required>
                                <option value="">{{ __('messages.select_type') }}</option>
                                @foreach(App\Models\Expense::getTypes() as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="amount">{{ __('messages.amount') }}</label>
                            <input type="number" step="0.01" name="amount" class="form-control" id="amount" required>
                        </div>
                        <div class="form-group">
                            <label for="description">{{ __('messages.description') }}</label>
                            <textarea name="description" class="form-control" id="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="date">{{ __('messages.date') }}</label>
                            <input type="date" name="date" class="form-control" id="date" required value="{{ date('Y-m-d') }}">
                        </div>
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

<!-- Edit Expense Modal -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" role="dialog" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editExpenseForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editExpenseModalLabel">{{ __('messages.edit_expense') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}
                    </div>
                    <input type="hidden" name="id" id="edit_expense_id">
                    <div class="form-group">
                        <label for="edit_amount">{{ __('messages.amount') }}</label>
                        <input type="number" step="0.01" name="amount" class="form-control" id="edit_amount" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">{{ __('messages.description') }}</label>
                        <textarea name="description" class="form-control" id="edit_description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_date">{{ __('messages.date') }}</label>
                        <input type="date" name="date" class="form-control" id="edit_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
   $(document).ready(function () {
    var table = $('#expensesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('expenses.data') }}",
            error: function (xhr, error, thrown) {
                if (xhr.status === 401) {
                    window.location.href = "{{ route('login') }}";
                } else {
                    let errorMessage = 'An error occurred while fetching data';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: "{{ __('messages.error') }}",
                        text: errorMessage
                    });
                }
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'type', name: 'type' },
            { data: 'amount', name: 'amount' },
            { data: 'description', name: 'description' },
            { data: 'date', name: 'date' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">{{ __("messages.processing") }}</span>',
            search: "{{ __('messages.search_expenses') }}",
            lengthMenu: "{{ __('messages.show_entries') }}",
            zeroRecords: "{{ __('messages.no_expenses_found') }}",
            info: "{{ __('messages.showing_info') }}",
            infoEmpty: "{{ __('messages.no_expenses_available') }}",
            paginate: {
                next: "{{ __('messages.next') }}",
                previous: "{{ __('messages.previous') }}"
            }
        }
    });

        // Edit Expense
        $(document).on('click', '.editExpense', function () {
            var id = $(this).data('id');
            $('#editExpenseModal .modal-body .form-group').hide();
            $('#editExpenseModal .modal-body .loading-spinner').show();
            $('#editExpenseModal').modal('show');

            $.ajax({
                url: "{{ url('expenses') }}/" + id + "/edit",
                type: "GET",
                success: function (data) {
                    $('#edit_expense_id').val(data.id);
                    $('#edit_amount').val(data.amount);
                    $('#edit_description').val(data.description);
                    $('#edit_date').val(data.date);
                    $('#editExpenseModal .modal-body .loading-spinner').hide();
                    $('#editExpenseModal .modal-body .form-group').show();
                },
                error: function (xhr) {
                    $('#editExpenseModal').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON.message || "{{ __('messages.error_loading_data') }}"
                    });
                }
            });
        });

        // Reset Edit Form on Modal Close
        $('#editExpenseModal').on('hidden.bs.modal', function () {
            $('#editExpenseForm')[0].reset();
            $('#edit_expense_id').val('');
            $('#editExpenseModal .modal-body .form-group').show();
            $('#editExpenseModal .modal-body .loading-spinner').hide();
        });

        // Update Expense
        $('#editExpenseForm').on('submit', function (e) {
            e.preventDefault();
            var id = $('#edit_expense_id').val();
            $.ajax({
                url: "{{ url('expenses') }}/" + id,
                type: "PUT",
                data: $(this).serialize(),
                success: function (response) {
                    $('#editExpenseModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || "{{ __('messages.expense_updated_successfully') }}"
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON.message || "{{ __('messages.error_updating_expense') }}"
                    });
                }
            });
        });

        // Delete Expense
        $(document).on('click', '.deleteExpense', function () {
            Swal.fire({
                title: "{{ __('messages.confirm_delete') }}",
                text: "{{ __('messages.delete_expense_confirmation') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: "{{ __('messages.delete') }}",
                cancelButtonText: "{{ __('messages.cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    var id = $(this).data('id');
                    $.ajax({
                        url: "{{ url('expenses') }}/" + id,
                        type: "DELETE",
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            table.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: response.message
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON.message || "{{ __('messages.error_deleting_expense') }}"
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
