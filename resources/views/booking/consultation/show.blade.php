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
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title"> Booking  Details</h3>
                    </div>
                    <!-- Success message -->
                    <div class="col-lg-12 card-background" style="background-color:#fff; padding: 10px;">
                            
                                <div class="card">
									<div class="card-header">
										<h3 class="card-title">BOOKING ID: {{ $patient->booking_reference_number }}</h3>
										<div class="card-options">
											<a href="#" class="btn btn-primary btn-sm">Status:  {{ $patient->master_value }}</a>
										</div>
									</div>
									<div class="card-body">
										<div class="row">
                                            
                                            <div class="col-md-4">
                                                <span class="form-label">Doctor: {{ $patient->staff_name }}</span>
                                            </div> </div>
                                            <div class="row">
                                            <div class="col-md-4">
                                                <span class="form-label">Patient: {{ $patient->patient_name }}</span>
                                            </div>
                                            </div>
                                            <div class="row">
                                            <div class="col-md-4">
                                                <span class="form-label">Booking Date: {{ $patient->booking_date }}</span>
                                            </div>
                                            </div>
                                            <div class="row">
                                            <div class="col-md-4">
                                                <span class="form-label">Timeslot: {{ $patient->time_slot_id }}</span>
                                            </div>
                                            </div>
                                            <div class="row">
                                            <div class="col-md-4">
                                                <span class="form-label">Branch: {{ $patient->branch_name }}</span>
                                            </div>
                                             </div>
                                            <div class="row">
                                            <div class="col-md-4">
                                                <span class="form-label">Appointment Date: {{ $patient->created_at->format('Y-m-d') }}</span>
                                            </div>
                                            </div>
                                 <br> <br> 
                                        
                                        	<div class="row">
                                        	    <div class="col-md-12">
                                        	   <h6 style="font-weight: 600;
                                            color: #0d97c6;">INVOICE DETAILS</h6></div>
                                        	    @if($invoice)
                                        	    <div class="col-md-3">
                                                    <span class="form-label">Booking Invoice Number: {{ $invoice->booking_invoice_number}}</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="form-label">Invoice Date: {{ $invoice->invoice_date }}</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="form-label">Booking Amount: {{ $invoice->paid_amount }}</span>
                                                </div>
                                             <div class="col-md-3">
                                            <span class="form-label">
                                                @if($patient->is_paid == 0)
                                                    NOT PAID
                                                @else
                                                    PAID
                                                @endif
                                            </span>
                                        </div>

                                                @endif
                                        	    
                                        	</div>
									</div>
                                </div>

                    </div>
                </div>
            </div>
        </div>
            
        <div class="card-body">
    <h3> Consultation Payment Details</h3>
   

    <div class="text-right">
    </div>
    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Paid Amount</th>
                                    <th class="wd-15p">Payment Mode</th>
                                    <th class="wd-15p">Deposit To</th>
                                    <th class="wd-15p">Reference Number</th>
                                </tr>
                            </thead>
                        <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($paymentDetails as $key => $paymentDetail)
                                    <tr id="">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{$paymentDetail->paid_amount}}</td>
                                        <td>{{$paymentDetail->master_value}}</td>
                                        <td>{{$paymentDetail->ledger_name}}</td>
                                        <td>{{ $paymentDetail->reference_no}}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                                    <div class="row" style="margin-top:20px;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <center>
                                            <a class="btn btn-danger" href="{{ route('bookings.consultation.index') }}"> <i class="fa fa-times"></i>
                                                Back</a>
                                        </center>
                                    </div>
                                </div>
                            </div>
    </div>

@endsection
