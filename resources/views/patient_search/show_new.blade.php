@extends('layouts.app')
@section('content')

<style>
    .form-control[readonly] {
        background-color: #c7c7c7 !important;
    }

    .page input[type=text][readonly] {
        background-color: #c7c7c7 !important;
    }

    .form-group .last-row {
        border-top: 1px solid #0d97c6;
        padding-top: 15px;
    }

    .bookingtype {
        border: 1px solid #333 !important;
        margin-right: 5px;
        border-top-right-radius: 20px !important;
        border-top-left-radius: 20px !important;
    }

    .nav-link.active {
        background-color: rgb(48 209 88) !important;
    }

    .nav-link:hover {
        background-color: rgba(1, 1, 1, 0.2) !important;
    }

    #myTab {
        margin-left: 3px;
        margin-top: 10px;

    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="col-lg-12 card-background" style="background-color:#fff; padding: 10px;">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Patient Details</h3>
                </div>
                <div class="col-md-4">
                    <span class="form-label">Patient: {{ $patient->patient_name }}</span>
                </div> </br>
                <div class="col-md-4">
                    <span class="form-label">Branch: {{ $patient->branch_name }}</span>
                </div> </br>
                <div class="col-md-4">
                    <span class="form-label">Branch: {{ $patient->patient_email }}</span>
                </div> </br>

                <div class="col-md-4">
                    <span class="form-label">Patient Code: {{ $patient->patient_code }}</span>
                </div> </br>

                <div class="col-md-4">
                    <span class="form-label">Patient Code: {{ $patient->patient_mobile }}</span>
                </div> </br>
                <div class="col-md-4">
                    <span class="form-label">Patient Code: {{ $patient->patient_dob }}</span>
                </div> </br>

            </div>

        </div>


    </div>
</div>
<div class="row" style="min-height: 70vh;">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0 card-title">Booking Details</h3>
            </div>
            <!-- Success message -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link bookingtype active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Consultation Booking</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link bookingtype" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Wellness Booking</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link bookingtype" id="contact-tab" data-toggle="tab" data-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Therapy Booking</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="col-lg-12 card-background" style="background-color:#fff; padding: 10px;">




                        @foreach($patient_bookings1 as $patient)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">BOOKING ID: {{ $patient->booking_reference_number }}</h3>
                                <div class="card-options">
                                    <a href="#" class="btn btn-primary btn-sm">Status: {{ $patient->master_value }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if($patient->booking_type_id==84)
                                    <div class="col-md-4">
                                        <span class="form-label">Doctor: {{ $patient->staff_username }}</span>
                                    </div>
                                    @endif
                                    <div class="col-md-4">
                                        <span class="form-label">Booking Reference Number: {{ $patient->booking_reference_number }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="form-label">Booking Date: {{ $patient->created_at->format('Y-m-d') }}</span>

                                    </div>
                                    <div class="col-md-4">
                                        <span class="form-label">Booking Type Id: {{ $patient->booking_type_id }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="form-label">Appointment Date: {{ @$patient->booking_date }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="form-label">Timeslot: {{ $patient->time_slot_id }}</span>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="form-label">Appointment Date & Time: | {{ $patient->time_slot_id }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Add any additional content here if needed -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>

                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="col-lg-12 card-background" style="background-color:#fff; padding: 10px;">

                        @foreach($patient_bookings2 as $patient)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">BOOKING ID: {{ $patient->booking_reference_number }}</h3>
                                <div class="card-options">
                                    <a href="#" class="btn btn-primary btn-sm">Status: {{ $patient->master_value }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if($patient->booking_type_id==84)
                                    <div class="col-md-4">
                                        <span class="form-label">Doctor: {{ $patient->staff_username }}</span>
                                    </div>
                                    @endif
                                    <div class="col-md-4">
                                        <span class="form-label">Booking Reference Number: {{ $patient->booking_reference_number }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="form-label">Booking Date: {{ $patient->created_at->format('Y-m-d') }}</span>

                                    </div>
                                    <div class="col-md-4">
                                        <span class="form-label">Booking Type Id: {{ $patient->booking_type_id }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="form-label">Appointment Date: {{ @$patient->booking_date }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="form-label">Timeslot: {{ $patient->time_slot_id }}</span>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="form-label">Appointment Date & Time: | {{ $patient->time_slot_id }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Add any additional content here if needed -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    <div class="col-lg-12 card-background" style="background-color:#fff; padding: 10px;">

                        @foreach($patient_bookings3 as $patient)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">BOOKING ID: {{ $patient->booking_reference_number }}</h3>
                                <div class="card-options">
                                    <a href="#" class="btn btn-primary btn-sm">Status: {{ $patient->master_value }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if($patient->booking_type_id==84)
                                    <div class="col-md-4">
                                        <span class="form-label">Doctor: {{ $patient->staff_username }}</span>
                                    </div>
                                    @endif
                                    <div class="col-md-4">
                                        <span class="form-label">Booking Reference Number: {{ $patient->booking_reference_number }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="form-label">Booking Date: {{ $patient->created_at->format('Y-m-d') }}</span>

                                    </div>
                                    <div class="col-md-4">
                                        <span class="form-label">Booking Type Id: {{ $patient->booking_type_id }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="form-label">Appointment Date: {{ @$patient->booking_date }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="form-label">Timeslot: {{ $patient->time_slot_id }}</span>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="form-label">Appointment Date & Time: | {{ $patient->time_slot_id }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Add any additional content here if needed -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>


            <div class="row" style="margin-top:20px;">
                <div class="col-md-12">
                    <div class="form-group">
                        <center>
                            <a class="btn btn-danger" href="{{ route('patient_search.index') }}">
                                <i class="fa fa-times"></i> Back</a>
                        </center>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

@endsection