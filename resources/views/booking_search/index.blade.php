@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Booking Search</h3>
            </div>
           <form action="{{ route('booking_search.index') }}" method="GET">

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="pat-code">Booking ID</label>
                            <input type="text" id="booking_reference_number" name="booking_reference_number" class="form-control" value="{{ request('booking_reference_number') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="pat-name">Booking Type:</label>
                            <select class="form-control" name="booking_type_id" id="booking_type_id">
                                    <option value=""selected disabled>Select Booking Type</option>
                                    @foreach($Bookingtypes as  $Bookingtype)
                                    <option value="{{ $Bookingtype->id }}" {{ request('booking_type_id') == $Bookingtype->id ? 'selected' : '' }}>
                                        {{ $Bookingtype->master_value }}
                                    </option>
                                    @endforeach
                                </select>
                        </div>
                        <div class="col-md-4">
                        <label for="status">Booking Status:</label>
                        <select id="status" name="booking_status" class="form-control">
                            <option value="">Select Booking Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ request('booking_status') == $status->id ? 'selected' : '' }}>{{ $status->master_value }}</option>
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
                <h3 class="card-title">List Bookings</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th class="wd-15p">SL.NO</th>
                                <th class="wd-15p">Booking ID</th>
                                <th class="wd-20p">Patient</th>
                                <th class="wd-20p">Branch</th>
                                <th class="wd-15p">Booking Type</th>
                                <th class="wd-15p">Status</th>
                                <th class="wd-15p">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i = 0;
                            @endphp
                            @foreach($patients as $patient)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $patient->booking_reference_number}}
                                <br>
                                <a class="btn btn-primary btn-sm edit-custom" href="{{ route('customer.feedback.create', $patient->booking_reference_number) }}">
                               Feedback</a>
                                </td>
                                <td>{{ $patient->patient_name}}</td>
                                <td>{{ $patient->branch_name}}</td>
                                <td>{{ $patient->booking_type_value}}</td>
                                <td>{{ $patient->booking_status_value}}</td>
                                <td>
                                
                                <a class="btn btn-secondary" href="{{ route('booking_search.show', $patient->id) }}">
                                    <i class="fa fa-eye" aria-hidden="true"></i> View
                                </a>
            
                                                
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
@endsection
