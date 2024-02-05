@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Patient Search</h3>
            </div>
           <form action="{{ route('booking_search.index') }}" method="GET">

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="pat-code">Booking Reference Number:</label>
                            <input type="text" id="booking_reference_number" name="booking_reference_number" class="form-control" value="{{ request('booking_reference_number') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="pat-name">Booking Type:</label>
                            <select class="form-control" name="booking_type_id" id="booking_type_id">
                                    <option value=""selected disabled>Select Booking Type</option>
                                    @foreach($Bookingtypes as  $Bookingtype)
                                    <option value="{{ $Bookingtype->id }}">
                                        {{ $Bookingtype->master_value }}
                                    </option>
                                    @endforeach
                                </select>
                        </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <div>
                                <button type="submit" class="btn btn-secondary"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                                <a class="btn btn-secondary ml-2" href="{{ route('booking_search.index') }}"><i class="fa fa-times" aria-hidden="true"></i>Reset</a>
                            </div>
                        </div>
                    </div>
               
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">List Patients</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th class="wd-15p">SL.NO</th>
                                <th class="wd-15p">Booking Reference Number</th>
                                <th class="wd-20p">Patient Name</th>
                                <th class="wd-15p">Booking Type</th>
                                <th class="wd-15p">Booking Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i = 0;
                            @endphp
                            @foreach($patients as $patient)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $patient->booking_reference_number}}</td>
                                <td>{{ $patient->patient_name}}</td>
                                <td>{{ $patient->master_value}}</td>
                                <td>{{ $patient->master_value}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
