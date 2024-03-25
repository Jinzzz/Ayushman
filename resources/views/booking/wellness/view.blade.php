@extends('layouts.app')
@section('content')

    
    <div class="card-body">
    <h3> Booking  Details</h3>
   

    <div class="text-right">
        <a class="btn btn-secondary ml-2" href="{{ route('bookings.wellness.index') }}">
            <i class="fa fa-times" aria-hidden="true"></i> Back
        </a>
    </div>
        <p><strong>Booking Reference Number:</strong> {{ $patient->booking_reference_number }}</p> <br>
        <p><strong>Patient Name:</strong> {{ $patient->patient_name }}</p> <br>
        <p><strong>Branch:</strong> {{ $patient->branch_name }}</p> <br>
        <p><strong>Booking Date:</strong> {{ $patient->booking_date }}</p> <br>
        <p><strong>Booking Status:</strong> {{ $patient->master_value }} </p> <br>
    </div>

    <div class="card-body">
    <h3> Wellness  Details</h3>
   

    <div class="text-right">
    </div>
    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Wellness Name</th>
                                    <th class="wd-15p">Wellness Fees</th>
                                    <th class="wd-15p">Time Slot</th>
                                    <th class="wd-15p">Booked Date</th>
                                </tr>
                            </thead>
                        <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($wellnessDetails as $key => $wellnessDetail)
                                    <tr id="">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{$wellnessDetail->wellness_name}}</td>
                                        <td>{{ number_format($wellnessDetail->wellness_fee, 2) }}</td>
                                        <td>{{$wellnessDetail->booking_timeslot}}</td>
                                        <td>{{ $wellnessDetail->created_at->format('Y-m-d') }}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
    </div>
    
        <div class="card-body">
    <h3> Wellness Payment Details</h3>
   

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
                                         <td>{{ number_format($paymentDetail->paid_amount,2)}}</td>
                                        <td>{{$paymentDetail->master_value}}</td>
                                        <td>{{$paymentDetail->ledger_name}}</td>
                                        <td>{{ $paymentDetail->reference_no}}</td>


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>



@endsection
