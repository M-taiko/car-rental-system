@extends('layouts.master')

@section('title', __('messages.route_management'))

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<style>
    .action-buttons .btn {
        margin: 0 2px;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
    }
</style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('messages.routes') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('messages.list') }}</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('messages.route_management') }}</h4>
                    
                    <a href="{{ route('routes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.add_route') }}
                    </a>
              
                     
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="routes-table" class="table table-bordered table-striped table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.start_point') }}</th>
                                <th>{{ __('messages.end_point') }}</th>
                                <th>{{ __('messages.distance') }} ({{ __('messages.km') }})</th>
                                <th>{{ __('messages.external_cost') }}</th>
                                <th>{{ __('messages.internal_cost') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($routes as $route)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $route->name }}</td>
                                    <td>{{ $route->start_point }}</td>
                                    <td>{{ $route->end_point }}</td>
                                    <td>{{ number_format($route->distance_km, 2) }}</td>
                                    <td class="text-nowrap">{{ number_format($route->external_cost, 2) }} {{ config('settings.currency_symbol', 'SAR') }}</td>
                                    <td class="text-nowrap">{{ number_format($route->internal_cost ?? 0, 2) }} {{ config('settings.currency_symbol', 'SAR') }}</td>
                                    <td>
                                        <span class="badge {{ $route->is_active ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $route->is_active ? __('messages.active') : __('messages.inactive') }}
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                       
                                        <a href="{{ route('routes.edit', $route->id) }}" class="btn btn-info btn-sm" title="{{ __('messages.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        
                                       
                                        <form action="{{ route('routes.destroy', $route->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm delete-btn" title="{{ __('messages.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        
                                        
                                       
                                        <form action="{{ route('routes.toggle-status', $route->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn {{ $route->is_active ? 'btn-warning' : 'btn-success' }} btn-sm" 
                                                title="{{ $route->is_active ? __('messages.deactivate') : __('messages.activate') }}">
                                                <i class="fas {{ $route->is_active ? 'fa-times' : 'fa-check' }}"></i>
                                            </button>
                                        </form>
                                        
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">{{ __('messages.no_routes_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
