@extends('layouts.app')
@section('content')
<div class="container">
    
    <div class="card-body">
    <h1>Patient Details</h1>
    <div class="show-container">

    <div class="text-right">
        <a class="btn btn-secondary ml-2" href="{{ route('patient_search.index') }}">
            <i class="fa fa-times" aria-hidden="true"></i> Back
        </a>
    </div>
        <p><strong>Patient Code:</strong> {{ $patient->patient_code }}</p> <br>
        <p><strong>Patient Name:</strong> {{ $patient->patient_name }}</p> <br>
        <p><strong>Patient Mobile:</strong> {{ $patient->patient_mobile }}</p> <br>
        </div>
     
        <h1> Booking Details</h1>

            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th class="wd-20p">SL.NO</th>
                            <th>Reference Number</th>
                            <th class="wd-20p">Doctor Name</th>
                            <th class="wd-20p">Branch</th>
                            <th class="wd-20p">Booking Date</th>
                            <th class="wd-20p">Slot Time</th>
                            <th class="wd-20p">Booking Status</th>
                            <th class="wd-20p">Booking Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach($patient_bookings as $patient_booking)
                        <tr id="dataRow_{{$patient_booking->id }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ $patient_booking->booking_reference_number}}</td>
                            <td>{{ $patient_booking->staff_username }}</td>
                            <td>{{ $patient_booking->branch_name}}</td>
                            <td>{{ $patient_booking->booking_date }}</td>
                             <td>{{ $patient_booking->time_slot_id }}</td>
                            <!--<td>{{ $patient_booking->time_from }} - {{ $patient->time_to }}</td>-->
                            <td>{{ $patient_booking->master_value }}</td>
                            <td>{{ $patient_booking->booking_fee}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- TABLE WRAPPER -->
    </div>
    </div>
@endsection
