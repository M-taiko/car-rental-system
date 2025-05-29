@extends('layouts.master')

@section('title')
{{ __('messages.drivers') }} - {{ __('messages.CAR_RENTAL_SYSTEM') }}
@endsection

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">{{ __('messages.drivers') }}</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.driver_list') }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        @can('driver-create')
            <a href="{{ route('driver.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.add_driver') }}
            </a>
        @endcan
    </div>
</div>
@endsection

@section('content')
<div class="row">
                <div class="card col-12">
                    <div class="card-body">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="driversTable">
                                <thead>
                                    <tr class="table-primary">
                                        <th>#</th>
                                        <th>{{ __('messages.image') }}</th>
                                        <th>{{ __('messages.name') }}</th>
                                        <th>{{ __('messages.phone') }}</th>
                                        <th>{{ __('messages.license_number') }}</th>
                                        <th>{{ __('messages.license_expiry') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th>{{ __('messages.daily_rate') }}</th>
                                        <th>{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($drivers as $driver)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($driver->image)
                                            <img src="{{ asset('storage/' . $driver->image) }}" alt="{{ $driver->name }}" class="wd-40 ht-40 rounded-circle">
                                            @else
                                            <div class="wd-40 ht-40 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
                                                <span class="tx-16">{{ strtoupper(substr($driver->name, 0, 1)) }}</span>
                                            </div>
                                            @endif
                                        </td>
                                        <td>{{ $driver->name }}</td>
                                        <td>{{ $driver->phone }}</td>
                                        <td>{{ $driver->license_number }}</td>
                                        <td>{{ $driver->license_expiry }}</td>
                                        <td>
                                            <span class="badge badge-{{ $driver->status == 'active' ? 'success' : 'danger' }}">
                                                {{ __('messages.' . $driver->status) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($driver->daily_rate, 2) }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('driver-edit')
                                                <a href="{{ route('driver.edit', $driver->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}</a>
                                                @endcan
                                                @can('driver-delete')
                                                <form action="{{ route('driver.destroy', $driver->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                                        <i class="fas fa-trash"></i> {{ __('messages.delete') }}</button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#driversTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
            },
            "order": [[0, "desc"]],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
    });

    // Add tooltips for icons
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection
