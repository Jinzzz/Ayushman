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
                        <h3 class="mb-0 card-title">Add Wellness Booking</h3>
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
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Booking Date*</label>
                                        <input type="date" class="form-control" required name="booking_date"
                                            id="booking_date" placeholder="Date" value="{{ old('booking_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Branch*</label>
                                        <select class="form-control" required name="branch_id" id="branch_id">
                                            <option value="">--Select Branch--</option>
                                            @foreach ($branches as $id => $branch)
                                                <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Patient*</label>
                                        <div class="input-group">
                                            <select class="form-control" required name="patient_id" id="patient_id">
                                                <option value="">--Select Patient--</option>
                                                @foreach ($patients as $id => $patient)
                                                    <option value="{{ $patient->id }}">
                                                        {{ ucwords(strtolower($patient->patient_name)) }} -
                                                        {{ $patient->patient_code }}</option>
                                                @endforeach
                                            </select>
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                                    data-target="#addPatientModal">+</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Wellness*</label>
                                        <select class="form-control" required name="wellness_id" id="wellness_id">
                                            <option value="">--Select Wellness--</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Booking Fee*</label>
                                        <input type="text" id="booking_fee" class="form-control" required
                                            name="booking_fee" placeholder="Booking Fee" value="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Wellness Timeslot*</label>
                                        <select class="form-control" required name="timeslots" id="timeslots">
                                            <option value="">--Select Timeslot--</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="wellness_details" style="display:none;">
                                <div class="col-md-12">
                                    <div class="form-group" style="border: 1px solid #000;     padding: 10px;">
                                        <h6 style="margin-left: 10px;
                                        font-weight: bold;">WELLNESS INFO</h6>
                                        <span  style="margin-left: 10px;
                                        color: #0d97c6;
                                        font-weight: bold;font-size:14px;">Name: <span id="wellness_name"></span>  |  Duration: <span id="wellness_duration"></span>  | Cost: <span id="wellness_cost"></span>  |  Offer: <span id="offer_price"></span>  |  Description: <span id="wellness_description"></span> </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="membership_details" style="display:none;">
                                <div class="col-md-12">
                                    <div class="form-group" style="border: 1px solid #000;">
                                        <span
                                            style="margin-left: 10px;
                                        color: #0d97c6;
                                        font-weight: bold;font-size:14px;">Membership
                                            Details : Package name: <span id="package_name"></span> | Start Date: <span
                                                id="start_date"></span> | Expiry: <span id="expiry_date"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="is_family"
                                                value="1" id="familyCheckbox">
                                            <span class="custom-control-label">Booking For a Family Member ?</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="family_member" style="display:none;">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Family member</label>
                                        <div class="input-group">
                                            <select class="form-control" name="family_id" id="family_id">
                                                <option value="">--Select member--</option>
                                            
                                            </select>
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                                    data-target="#addFamilyModal">+</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="pay_now"
                                                value="1" id="paynowCheck">
                                            <span class="custom-control-label">Pay Now ?</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="paymentdiv" style="display: none;">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Payable Amount*</label>
                                        <input type="text" id="payable_amount" class="form-control" required
                                            name="payable_amount" placeholder="Payable amount" value="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
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
                                <div class="col-md-4">
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
                                            <a class="btn btn-danger" href="{{route('bookings.consultation.index')}}"> <i class="fa fa-times"></i>
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
        $(document).ready(function() {
            var currentDate = new Date().toISOString().slice(0, 10);
            $('#booking_date').val(currentDate);

            $('#familyCheckbox').change(function(){
                if($(this).is(":checked")) {
                    $('#family_member').show();
                } else {
                    $('#family_member').hide();
                }
            });
            $('#paynowCheck').change(function(){
                if($(this).is(":checked")) {
                    $('#paymentdiv').show();
                } else {
                    $('#paymentdiv').hide();
                }
            });
            
        });

        $(document).ready(function() {
            $('#branch_id').on('change', function() {
                var branchId = $(this).val();
                if (branchId) {
                    $.ajax({
                        url: '{{ route('booking.getWellness') }}',
                        type: "GET",
                        data: {
                            branch_id: branchId,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: "json",
                        success: function(data) {
                            $('#wellness_id').empty();
                            $('#booking_fee').val('');
                            $('#wellness_id').append(
                            '<option value="">--Select Wellness--</option>');
                            $.each(data, function(key, value) {
                                $('#wellness_id').append('<option value="' + value
                                    .wellness_id + '">' + value.wellness_name +
                                    '</option>');
                            });
                        }
                    });
                } else {
                    $('#wellness_id').empty();
                    $('#booking_fee').val('');
                    $('#wellness_id').append('<option value="">--Select Wellness--</option>');
                }
            });
            //get booking fee

            $('#wellness_id').on('change', function() {
                var wellnessID = $(this).val();
                var bookingDate = $('#booking_date').val();
                if (wellnessID && bookingDate) {
                    $.ajax({
                        url: '{{ route('wellness.getBookingFee') }}',
                        type: "GET",
                        data: {
                            wellness_id: wellnessID,
                            booking_date: bookingDate,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: "json",
                        success: function(data) {
                            if (data.error) {
                                alert(data.error);
                                $('#wellness_id').val('');
                                $('#booking_fee').val('');
                                $('#timeslots').val();
                            } else {
                                $('#booking_fee').val(data.booking_fee);
                                $('#timeslots').empty();
                                $.each(data.timeslots, function(key, value) {
                                    var optionText = value.therapy_room_name + ' : ' + value.time_from + ' - ' + value.time_to;
                                    $('#timeslots').append($('<option>', {
                                        value: value.timeslot,
                                        text: optionText
                                    }));
                                });
                                if (data.wellness_name && data.wellness_duration !== null &&
                                data.wellness_cost !== null ) {
                                    $('#wellness_details').show();
                                    $('#wellness_name').text(data.wellness_name);
                                    $('#wellness_duration').text(data.wellness_duration);
                                    $('#wellness_cost').text(data.wellness_cost);
                                    $('#offer_price').text(data.offer_price);
                                    $('#wellness_description').text(data.wellness_description);
                                } else {
                                    $('#wellness_details').hide(); 
                                }
                            }
                        }
                    });
                } else {
                    $('#booking_fee').val('');
                    $('#wellness_details').hide();
                }
            });

            //get membership if any 
            $('#patient_id').on('change', function() {
                var patientId = $(this).val();
                if (patientId) {
                    $.ajax({
                        url: '{{ route('getMembershipDetails') }}',
                        type: "GET",
                        data: {
                            patient_id: patientId,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: "json",
                        success: function(data) {
                            if (data.membership && data.membership.package_title !== null &&
                                data.membership.start_date !== null && data.membership
                                .expiry_date !== null) {
                                $('#membership_details').show(); // Show membership details div
                                $('#package_name').text(data.membership.package_title);
                                $('#start_date').text(data.membership.start_date);
                                $('#expiry_date').text(data.membership.expiry_date);
                            } else {
                                $('#membership_details')
                            .hide(); // Hide membership details div if no membership found
                            }
                        }
                    });
                } else {
                    $('#membership_details').hide(); // Hide membership details div if no patient selected
                }
            });

            //membership based booking fee
            $('#patient_id, #wellness_id').on('change', function() {
                var patientId = $('#patient_id').val();
                var wellnessID = $('#wellness_id').val();
                if (patientId || wellnessID) {
                    $.ajax({
                        url: '{{ route('wellness.getMembershipAndBookingFee') }}',
                        type: "GET",
                        data: {
                            patient_id: patientId,
                            wellness_id: wellnessID,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: "json",
                        success: function(data) {
                            console.log("Data received:", data);
                            var payableAmount = data.payable_amount ? parseFloat(data
                                .payable_amount).toFixed(2) : '0.00';
                            console.log("Payable amount:", payableAmount);
                            $('#payable_amount').val(payableAmount);
                            //family members get
                            var familyMembers = data.family_members;
                            var dropdown = $('#family_id');
                            dropdown.empty();
                            dropdown.append('<option value="">--Select member--</option>');
                            if (familyMembers.length > 0) {
                                familyMembers.forEach(function(member) {
                                    dropdown.append('<option value="' + member.id + '">' + member.family_member_name + '</option>');
                                });
                            }

                        }
                    });
                }
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