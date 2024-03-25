@extends('layouts.app')
@section('content')
    <style>
        .fa-eye:before {
            color: #fff !important;
        }
    </style>
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                <div class="card-header">
                    <h3 class="card-title">Consultation Bookings</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Reference No.</th>
                                    <th class="wd-15p">Patient</th> {{--  if is_for_family_member = 0 show "self" / else show the name of family member and display text "family member" --}}
                                    <th class="wd-15p">Branch</th>
                                    <th class="wd-15p">Appointment<br> Date</th>
                                    <th class="wd-15p">Booked<br> Date</th>
                                    <th class="wd-20p">Status</th>
                                    <th class="wd-15p">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($bookings as $key => $booking)
                                    <tr id="">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{@$booking->booking_reference_number}}</td>
                                        <td>{!! wordwrap(@$booking->patient['patient_name'], 10, "<br>", true) !!} <br>
                                        @if($booking->is_for_family_member !== null && @$booking->is_for_family_member > 0)
                                        Booked For : {!! wordwrap(@$booking->familyMember['family_member_name'], 10, "<br>", true) !!}
                                        @endif
                                           
                                        </td>
                                        <td>{{@$booking->branch['branch_name']}}</td>
                                        <td>{{ @$booking ? \Carbon\Carbon::parse($booking->booking_date)->toDateString() : '' }}
                                       
                                        </td>
                                        <td>{{ @$booking ? \Carbon\Carbon::parse($booking->created_at)->toDateString() : '' }}</td>
                                        <td>{{ @$booking->bookingStatus['master_value']}}</td>
                                        <td>
                                            <a class="btn btn-primary" href="{{ route('doctor.patient.history', $booking->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Patient History </a>
                                            <a class="btn btn-primary" href="{{ route('doctor.precription.add', $booking->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Prescription </a>
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
