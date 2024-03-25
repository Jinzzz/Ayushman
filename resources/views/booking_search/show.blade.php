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
       

    </style>
    <div class="container">
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title"> Booking  Details</h3>
                    </div>
                    <!-- Success message -->
                    <div class="col-lg-12 card-background" style="background-color:#fff; padding: 10px;">
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                                <div class="card">
									<div class="card-header">
										<h3 class="card-title">BOOKING ID: {{ $patient->booking_reference_number }}</h3>
										<div class="card-options">
											<a href="#" class="btn btn-primary btn-sm">Status:  {{ $patient->master_value }}</a>
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
                                                <span class="form-label">Patient: {{ $patient->patient_name }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label">Patient Code: {{ $patient->patient_code }}</span>
                                            </div>
                                              <div class="col-md-4">
                                                <span class="form-label">Patient Mobile: {{ $patient->patient_mobile }}</span>
                                            </div>
                                             <div class="col-md-4">
                                                <span class="form-label">Booking Reference Number: {{ $patient->booking_reference_number }}</span>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <span class="form-label">Booking Date: {{ $patient->created_at->format('Y-m-d') }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label">Appointment Date: {{ $patient->booking_date }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label">Timeslot:  {{ $patient->time_slot_id }} </span>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label">Branch: {{ $patient->branch_name }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label">Appointment Date & Time : {{ $patient->created_at->format('Y-m-d') }} | {{ $patient->time_slot_id }} </span>
                                            </div>
                                            
                                        </div>
                                        
                                        	<div class="row">
                                        	    <div class="col-md-12">
                                        	  
                                        	    
                                        	</div>
									</div>
                                </div>
                            <div class="row" style="margin-top:20px;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <center>
                                            <a class="btn btn-danger" href="{{ route('booking_search.index') }}"> <i class="fa fa-times"></i>
                                                Back</a>
                                        </center>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>


        </div>

@endsection
