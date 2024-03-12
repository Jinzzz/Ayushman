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
       
        ul li {
        list-style: auto;
        }

    </style>
    <div class="container">
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">Patient History</h3>
                    </div>
                    <!-- Success message -->
                    <div class="col-lg-12 card-background" style="background-color:#fff; padding: 10px;">
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                                <div class="card">
									<div class="card-header">
										<h3 class="card-title">BOOKING ID: CBRN039</h3>
										<div class="card-options">
											<a href="#" class="btn btn-primary btn-sm">Status: Confirmed</a>
										</div>
									</div>
									<div class="card-body">
										<div class="row">
                                            
                                            <div class="col-md-3">
                                                <span class="form-label">Doctor: Pinky Doctor</span>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="form-label">Booking Date: 12/03/2024</span>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="form-label">Timeslot: Evening slot</span>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="form-label">Branch: Branch name</span>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="form-label">Diagnosis: data here</span>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="form-label">Advice: data here</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                           
                                            <div class="col-md-6">
                                            <h6 style="font-weight: 600;
                                            color: #0d97c6;">MEDICATION PRESCRIBED</h6>
                                               <ul style="list-style: auto;">
                                                <li>
                                                    Paracetamol (Dosage - Duration)
                                                </li>
                                                <li>
                                                    Medicine 2 (Dosage - Duration)
                                                </li>
                                               </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 style="font-weight: 600;
                                                color: #0d97c6;">THERAPIES</h6>
                                               <ul>
                                                <li>
                                                    Therapy 1 
                                                </li>
                                                <li>
                                                    Therapy 2
                                                </li>
                                               </ul>
                                            </div>
                                        </div>
									</div>
                                </div>
								
                          
                            <div class="row" style="margin-top:20px;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <center>
                                            <a class="btn btn-danger" href="{{route('consultation.index')}}"> <i class="fa fa-times"></i>
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
