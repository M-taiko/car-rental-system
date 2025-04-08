@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Add Readings</h1>

    <!-- Form for multiple readings -->
    <form action="{{ route('readings.store') }}" method="POST">
        @csrf

        <!-- Well and Vessel Selection -->
        <div class="form-group">
            <label for="well_id">Select Well:</label>
            <select name="well_id" id="well_id" class="form-control" required>
                <option value="">Select a Well</option>
                @foreach($wells as $well)
                    <option value="{{ $well->id }}">{{ $well->well_number }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="vessel_id">Select Vessel:</label>
            <select name="vessel_id" id="vessel_id" class="form-control" required>
                <option value="">Select a Vessel</option>
                @foreach($vessels as $vessel)
                    <option value="{{ $vessel->id }}">{{ $vessel->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Multiple Readings (5 sets) -->
        <div id="readingsContainer">
            @for($i = 1; $i < 6; $i++)
                <div class="form-row mb-4">
                    <p> Reading Number {{$i}}  </p><br><hr width="100%">
                    <div class="form-group col-md-1">
                        <label for="bsw_{{ $i }}" style="font-size: smaller;">BSW:</label>
                        <input type="number" step="any" class="form-control" name="readings[{{ $i }}][bsw]" id="bsw_{{ $i }}" required>
                    </div>

                    <div class="form-group col-md-1">
                        <label for="api_{{ $i }}" style="font-size: smaller;">API:</label>
                        <input type="number" step="any" class="form-control" name="readings[{{ $i }}][api]" id="api_{{ $i }}" required>
                    </div>

                    <div class="form-group col-md-1">
                        <label for="ptb_{{ $i }}" style="font-size: smaller;">PTB:</label>
                        <input type="number" step="any" class="form-control" name="readings[{{ $i }}][ptb]" id="ptb_{{ $i }}" required>
                    </div>

                    <div class="form-group col-md-1">
                        <label for="shrinkage_{{ $i }}" style="font-size: smaller;">Shrinkage:</label>
                        <input type="number" step="any" class="form-control" name="readings[{{ $i }}][shrinkage]" id="shrinkage_{{ $i }}" required>
                    </div>

                    <div class="form-group col-md-1">
                        <label for="arffer_plate_size_{{ $i }}" style="font-size: smaller;">Arffer Plate Size:</label>
                        <input type="number" step="any" class="form-control" name="readings[{{ $i }}][arffer_plate_size]" id="arffer_plate_size_{{ $i }}" required>
                    </div>

                    <div class="form-group col-md-1">
                        <label for="water_sg_{{ $i }}" style="font-size: smaller;">Water SG:</label>
                        <input type="number" step="any" class="form-control" name="readings[{{ $i }}][water_sg]" id="water_sg_{{ $i }}" required>
                    </div>

                    <div class="form-group col-md-1">
                        <label for="gas_sg_{{ $i }}" style="font-size: smaller;">Gas SG:</label>
                        <input type="number" step="any" class="form-control" name="readings[{{ $i }}][gas_sg]" id="gas_sg_{{ $i }}" required>
                    </div>

                    <div class="form-group col-md-1">
                        <label for="well_head_pressure_{{ $i }}" style="font-size: x-small;;">Well Head Pressure:</label>
                        <input type="number" step="any" class="form-control" name="readings[{{ $i }}][well_head_pressure]" id="well_head_pressure_{{ $i }}" required>
                    </div>

                    <div class="form-group col-md-1">
                        <label for="seal_line_pressure_{{ $i }}" style="font-size: x-small;;">Seal Line Pressure:</label>
                        <input type="number" step="any" class="form-control" name="readings[{{ $i }}][seal_line_pressure]" id="seal_line_pressure_{{ $i }}" required>
                    </div>

                    <div class="form-group col-md-1">
                        <label for="oil_rate_{{ $i }}" style="font-size: smaller;">Oil Rate:</label>
                        <input type="number" step="any" class="form-control" name="readings[{{ $i }}][oil_rate]" id="oil_rate_{{ $i }}" required>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Submit Readings</button>
    </form>
</div>
@endsection
