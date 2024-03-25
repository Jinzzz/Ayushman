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
       
        /*ul li {*/
        /*list-style: auto;*/
        /*}*/

    </style>
    <div class="container">
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">View Consultation</h3>
                    </div>
                    <!-- Success message -->
                    <div class="col-lg-12 card-background" style="background-color:#fff; padding: 10px;">
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                          
                                <div class="card">
									<div class="card-header">
										<h3 class="card-title">BOOKING ID:{{ @$history->bookingDetails['booking_reference_number']}}</h3>
										<div class="card-options">
											<a href="#" class="btn btn-primary btn-sm">Status: {{@$history->bookingDetails->bookingStatus['master_value']}}</a>
										</div>
									</div>
									<div class="card-body">
										<div class="row">
                                            
                                            <div class="col-md-3">
                                                <span class="form-label">Doctor:  {{@$history->Staff['staff_name']}}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="form-label">Patient: {{ @$history->bookingDetails['is_for_family_member'] !== null && @$history->bookingDetails['is_for_family_member'] > 0 ? @$history->bookingDetails->familyMember['family_member_name'] : @$history->bookingDetails->patient['patient_name'] }} </span>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="form-label">Booking Date: {{ \Carbon\Carbon::parse(@$history->bookingDetails['created_at'])->toDateString() }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="form-label">Timeslot: {{@$history->bookingDetails['time_slot_id']}}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="form-label">Branch:  {{@$history->bookingDetails->branch['branch_name']}}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="form-label">Diagnosis:{{@$history->diagnosis}}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="form-label">Advice::{{@$history->advice}}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                           
                                            <div class="col-md-6">
                                            <h6 style="font-weight: 600;
                                            color: #0d97c6;">MEDICATION PRESCRIBED</h6>
                                               <ul style="list-style: auto;">
                                             @foreach($history->PrescriptionDetails as $details)
                                                <li>
                                                    {{ $details->medicine['medicine_name'] }} ({{ $details->medicine_dosage }} - {{ $details->duration }})
                                                </li>
                                            @endforeach
                                                
                                               </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 style="font-weight: 600;
                                                color: #0d97c6;">THERAPIES</h6>
                                               <ul>
                                       
                                        @if(@$history->bookingDetails['therapyBookings']->isNotEmpty())
                                            @foreach(@$history->bookingDetails['therapyBookings'] as $therapy)
                                                <li>
                                                    {{@$therapy->therapy['therapy_name']}}
                                                </li>
                                            @endforeach
                                        @else
                                                
                                                    No therapy Added!
                                                
                                        @endif
                                               </ul>
                                            </div>
                                        </div>
									</div>
                                </div>
                           
								
                          
                            <div class="row" style="margin-top:20px;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <center>
                                            <a class="btn btn-danger" href="{{route('consultation.history')}}"> <i class="fa fa-times"></i>
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
@endsection
@section('js')
        
@endsection
