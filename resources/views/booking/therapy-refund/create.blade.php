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
                        <h3 class="mb-0 card-title">Add Therapy Refund</h3>
                    </div>
                    <!-- Success message -->
                    <div class="col-lg-12 card-background" style="background-color:#fff";>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <!-- <strong>Whoops!</strong> There were some problems with your input.<br><br> -->
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{route('store.therapy-refund')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Patient*</label>
                                            <select class="form-control" required name="patient_id" id="patient_id">
                                                <option value="">--Select Patient--</option>
                                                @foreach ($patients as $id => $patient)
                                                    <option value="{{ $patient->id }}">
                                                        {{ ucwords(strtolower($patient->patient_name)) }} -
                                                        {{ $patient->patient_code }}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Booking ID*</label>
                                            <select class="form-control" required name="booking_id" id="booking_id">    
                                            </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Paid Amount*</label>
                                        <input type="text" id="paid_amount" class="form-control" required
                                            name="paid_amount" placeholder="Paid amount" value="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="therapy_details" style="display:none;">
                                <div class="col-md-12">
                                    <div class="form-group" style="border: 1px solid #000;     padding: 10px;">
                                        <h6 style="margin-left: 10px;
                                        font-weight: bold;">THERAPY BOOKING INFO</h6>
                                    <span  style="margin-left: 10px;
                                        color: #0d97c6;
                                        font-weight: bold;font-size:14px;">
                                        Booking Date: <span id="booking_date"></span>  |  Booking Reference Number: <span id="reference_number"></span>  | Family Member/Self: <span id="family_member"></span>  |  Branch: <span id="branch_name"></span> |  Booking Status: <span id="booking_status"></span> <br>
                                        <span>Included Therapies List</span>
                                        <ul id="therapy_list">
                                            <li>    
                                            </li>
                                        </ul>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="paymentdiv">
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="payment-type" class="form-label">Refund Mode</label>
                                        <select class="form-control" required name="refund_mode"
                                            placeholder="Refund Mode" id="refund_mode" >
                                            <option value="">--Select Mode--</option>
                                                <option value="1">Full Amount Refund</option>
                                                <option value="2">Partial Refund</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Refund Amount</label>
                                        <input type="text" id="refund_amount" class="form-control" required
                                            name="refund_amount" placeholder="Refund amount" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="payment-type" class="form-label">Payment Mode</label>
                                        <select class="form-control" required name="payment_mode"
                                            placeholder="Payment Mode" id="payment_mode" onchange="updateDepositTo()">
                                            <option value="">--Select--</option>
                                            @foreach ($paymentType as $id => $value)
                                                <option value="{{ $id }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Deposit To</label>
                                        <select class="form-control" name="deposit_to" id="deposit_to">
                                            <option value="">Deposit To</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top:20px;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <center>
                                            <button type="submit" class="btn btn-raised btn-primary">
                                                <i class="fa fa-check-square-o"></i> Add</button>
                                            <button type="reset" class="btn btn-raised btn-success">
                                                <i class="fa fa-refresh"></i> Reset</button>
                                            <a class="btn btn-danger" href="{{route('bookings.therapy-refund.index')}}"> <i class="fa fa-times"></i>
                                                Cancel</a>
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
<script>
    $(document).ready(function () {
         $('#refund_mode').change(function () {
            var refundMode = $(this).val();
            var paidAmount = parseFloat($('#paid_amount').val());
            if (refundMode === '1') { // Full payment refund
                $('#refund_amount').val(paidAmount);
                $('#refund_amount').prop('readonly', true);
                
            } else if (refundMode === '2') { // Partial refund
                $('#refund_amount').val('');
                $('#refund_amount').prop('readonly', false);
                $('#refund_amount').attr('max', paidAmount); 
            }
        });
        $('#patient_id').change(function () {
            var patientId = $(this).val();

            $.ajax({
                url: '{{ route("fetch.refund.bookings") }}',
                type: 'GET',
                data: {
                    patient_id: patientId
                },
                success: function (response) {
                    $('#booking_id').empty();
                    $('#booking_id').append('<option value="">--Select Booking ID--</option>');
                    $.each(response, function (id, bookingReference) {
                        $('#booking_id').append('<option value="' + id + '">' + bookingReference + '</option>');
                    });
                }
            });
        });

        $('#booking_id').change(function () {
            var bookingId = $(this).val();

            $.ajax({
                url: '{{ route("fetch.refund.fetchtherapyInfo") }}',
                type: 'GET',
                data: {booking_id: bookingId},
                success: function (response) {
                    $('#therapy_details').show();
                    var booking = response.booking;
                    var therapies = response.therapies;

                    if (booking && therapies) {
                        $('#therapy_details').show();
                        // Display main booking information
                        $('#booking_date').text(booking.booking_date);
                        $('#reference_number').text(booking.booking_reference_number);
                        $('#family_member').text(booking.is_for_family_member ? 'Family Member' : 'Self');
                        $('#branch_name').text(booking.branch ? booking.branch.branch_name : 'N/A');
                        $('#booking_status').text(booking.bookingStatus ? booking.bookingStatus.master_value : 'N/A');

                        // Display list of therapies
                        var therapyList = '';
                        $.each(therapies, function (index, therapy) {
                            therapyList += '<li>' + (index + 1) + '. Therapy name: ' + therapy.therapy.therapy_name + '</li>';
                        });
                        $('#therapy_list').html(therapyList);
                        $('#paid_amount').val(booking.booking_fee);
                    }else{
                        $('#therapy_details').hide(); 
                    }
                }
            });
        });
        
        //payment mode
            $('#payment_mode').change(function() {
                var selectedPaymentMode = $(this).val();
                $.ajax({
                    url: '{{ route('getLedgerNames1') }}',
                    type: 'GET',
                    data: {
                        payment_mode: selectedPaymentMode
                    },
                    success: function(data) {
                        $('#deposit_to').empty();
                        $('#deposit_to').append('<option value="">Deposit To</option>');
                        $.each(data, function(key, value) {
                            $('#deposit_to').append('<option value="' + key + '">' +
                                value + '</option>');
                        });
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
    });
</script>
@endsection
