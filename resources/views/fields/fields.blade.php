@extends('layouts.master')

@section('css')
<!-- Include DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endsection

@section('title')
    {{ __('messages.fields') }} - {{ __('messages.TAQA_SYSTEM') }}
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.fields') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.manage_fields') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <!-- Add New Field Button -->
        <button class="btn btn-success" data-toggle="modal" data-target="#addFieldModal">
            <i class="fas fa-plus"></i> {{ __('messages.add_field') }}
        </button>
    </div>
</div>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
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
                <h3 class="card-title">{{ __('messages.fields_list') }}</h3>
            </div>
            <div class="card-body">
                <!-- DataTable -->
                <table id="fieldsTable" class="table table-center table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.field_name') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fields as $field)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $field->name }}</td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editFieldModal{{ $field->id }}">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </button>
                                <!-- Delete Form -->
                                <form action="{{ route('fields.destroy', $field->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Field Modal -->
<div class="modal fade" id="addFieldModal" tabindex="-1" role="dialog" aria-labelledby="addFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('fields.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addFieldModalLabel">{{ __('messages.add_field_modal_title') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="fieldName">{{ __('messages.field_name') }}</label>
                        <input type="text" name="name" class="form-control" id="fieldName" required>
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

<!-- Edit Field Modals -->
@foreach($fields as $field)
<div class="modal fade" id="editFieldModal{{ $field->id }}" tabindex="-1" role="dialog" aria-labelledby="editFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('fields.update', $field->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editFieldModalLabel">{{ __('messages.edit_field_modal_title') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="fieldName{{ $field->id }}">{{ __('messages.field_name') }}</label>
                        <input type="text" name="name" class="form-control" id="fieldName{{ $field->id }}" value="{{ $field->name }}" required>
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
@endforeach
@endsection

@section('js')
<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<!-- Initialize DataTables -->
<script>
    $(document).ready(function () {
        $('#fieldsTable').DataTable({
            language: {
                search: "{{ __('messages.search_fields') }}",
                lengthMenu: "{{ __('messages.show_entries') }}",
                zeroRecords: "{{ __('messages.no_fields_found') }}",
                info: "{{ __('messages.showing_info') }}",
                infoEmpty: "{{ __('messages.no_fields_available') }}",
                paginate: {
                    next: "{{ __('messages.next') }}",
                    previous: "{{ __('messages.previous') }}"
                }
            },
            order: [[0, 'asc']]
        });
    });
</script>
@endsection
