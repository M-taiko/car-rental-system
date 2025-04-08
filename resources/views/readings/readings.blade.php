@extends('layouts.master')

@section('content')

@section('title')
    {{ __('messages.readings') }}
@endsection

<div class="container">
    <h1>{{ __('messages.readings') }}</h1>

    <!-- Card to wrap the DataTable -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('messages.readings') }}</h4>
        </div>
        <div class="card-body">
            <!-- DataTable for Readings -->
            <table id="readingsTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('messages.well_id') }}</th>
                        <th>{{ __('messages.vessel') }}</th>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.number_of_readings') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($readings as $groupKey => $wellReadings)
                        @php
                            // Split the groupKey to get well_id and created_at
                            list($wellId, $createdAt) = explode('_', $groupKey);
                        @endphp
                        <tr>
                            <td>{{ $wellReadings->first()->well->well_number }}</td>
                            <td>{{ $wellReadings->first()->vessel->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($createdAt)->format('Y-m-d') }}</td>
                            <td>{{ $wellReadings->count() }}</td>
                            <td>
                                <!-- Button to trigger modal -->
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#readingModal{{ $groupKey }}">
                                    {{ __('messages.view_full_test_results') }}
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="readingModal{{ $groupKey }}" tabindex="-1" role="dialog" aria-labelledby="readingModalLabel{{ $groupKey }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="readingModalLabel{{ $groupKey }}">
                                                    {{ __('messages.full_test_results', ['well_id' => $wellReadings->first()->well->well_number, 'date' => \Carbon\Carbon::parse($createdAt)->format('Y-m-d')]) }}
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Loop through each reading for this group -->
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('messages.bsw') }}</th>
                                                            <th>{{ __('messages.api') }}</th>
                                                            <th>{{ __('messages.ptb') }}</th>
                                                            <th>{{ __('messages.shrinkage') }}</th>
                                                            <th>{{ __('messages.arffer_plate_size') }}</th>
                                                            <th>{{ __('messages.water_sg') }}</th>
                                                            <th>{{ __('messages.gas_sg') }}</th>
                                                            <th>{{ __('messages.well_head_pressure') }}</th>
                                                            <th>{{ __('messages.seal_line_pressure') }}</th>
                                                            <th>{{ __('messages.oil_rate') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($wellReadings as $reading)
                                                            <tr>
                                                                <td>{{ $reading->bsw }}</td>
                                                                <td>{{ $reading->api }}</td>
                                                                <td>{{ $reading->ptb }}</td>
                                                                <td>{{ $reading->shrinkage }}</td>
                                                                <td>{{ $reading->arffer_plate_size }}</td>
                                                                <td>{{ $reading->water_sg }}</td>
                                                                <td>{{ $reading->gas_sg }}</td>
                                                                <td>{{ $reading->well_head_pressure }}</td>
                                                                <td>{{ $reading->seal_line_pressure }}</td>
                                                                <td>{{ $reading->oil_rate }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.close') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#readingsTable').DataTable({
                responsive: true, // Make table responsive
                order: [[ 2, 'desc' ]] // Optional: order by the date column (created_at)
            });
        });
    </script>
@endsection
