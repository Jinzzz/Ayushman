@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Search Consultation Booking</h3>
            </div>
            <form action="{{ route('consultation.booking') }}" method="GET">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="patient-code" class="form-label">Patient Code:</label>
                            <input type="text" id="patient-code" name="patient_code" class="form-control" value="{{ request('patient_code') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="patient-name" class="form-label">Patient Name:</label>
                            <input type="text" id="patient-name" name="patient_name" class="form-control" value="{{ request('patient_name') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="patient-mobile" class="form-label">Doctor Name</label>
                            <input type="text" id="doctor_name" name="doctor_name" class="form-control" value="{{ request('doctor_name') }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="patient-code" class="form-label">Booking date</label>
                            <input type="date" id="patient-code" name="booking_date" class="form-control" value="{{ request('booking_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>&nbsp;
                            <a class="btn btn-primary" href="{{ route('consultation.booking') }}"><i class="fa fa-times" aria-hidden="true"></i> Reset</a>
                        </div>
                    </div>
                </div>
        </div>
        </form>
    </div>


    <div class="card">
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{$message}}</p>
        </div>
        @endif
        @if ($message = Session::get('error'))
        <div class="alert alert-danger">
            <p>{{$message}}</p>
        </div>
        @endif
        <div class="card-header">
            <h3 class="card-title">List Consultation Booking</h3>
        </div>
        <div class="card-body">

                
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th class="wd-50p">SL.NO</th>
                            <th class="wd-25p">Booking Code</th>
                            <th class="wd-25p">Patient Name</th>
                            <th class="wd-25p">Doctor Name</th>
                            <th class="wd-25p">Time Slot</th>
                            <th class="wd-25p">Booking Date</th>
                            <th class="wd-25p">Status</th>
                            <th class="wd-25p">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach($consultations as $consultation)
                        <tr id="dataRow_{{$consultation->id }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ $consultation->booking_reference_number }}</td>
                            <td> {{ $consultation->patient_code }} - {{ $consultation->patient_name }}</td>
                            <td>{{ $consultation->staff_name }}</td>
                            <td>{{ $consultation->time_from }} - {{ $consultation->time_to }}</td>
                            <td>{{ $consultation->booking_date }}</td>
                            <td>{{ $consultation->master_value }}</td>
                            <td><a class="btn btn-secondary btn-sm" href="{{ route('viewconsultation.booking', $consultation->consultation_id) }}">
                                <i class="fa fa-eye" aria-hidden="true"></i> View </a>
                             </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- TABLE WRAPPER -->
        </div>
        <!-- SECTION WRAPPER -->
    </div>
</div>
</div>
<!-- ROW-1 CLOSED -->
@endsection
