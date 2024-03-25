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
                        <h3 class="mb-0 card-title">Add Prescription</h3>
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
                        <form action="{{route('doctor.precription.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Booking Reference ID*</label>
                                        <input type="hidden" name="booking_id" value="{{$bookingInfo->id}}">
                                        <input type="text" class="form-control" required name="reference_no"
                                            id="reference_no" placeholder="Date" value="{{@$bookingInfo->booking_reference_number}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Consulting Date*</label>
                                        <input type="date" class="form-control" required name="consulting_date"
                                            id="consulting_date" placeholder="Date" value="{{ old('consulting_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Booking Date*</label>
                                        <input type="date" class="form-control" required name="booking_date"
                                            id="booking_date" placeholder="Date" value="{{ @$bookingInfo ? \Carbon\Carbon::parse($bookingInfo->created_at)->toDateString() : '' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Patient Name*</label>
                                        <input type="text" class="form-control" required name="patient_name"
                                            id="patient_name" placeholder="Patient Name" value="{{ @$bookingInfo->is_for_family_member !== null && @$bookingInfo->is_for_family_member > 0 ? @$bookingInfo->familyMember['family_member_name'] : @$bookingInfo->patient['patient_name'] }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Branch*</label>
                                        <input type="text" class="form-control" required name="branch_name"
                                            id="branch_name" placeholder="Date" value="{{@$bookingInfo->branch['branch_name']}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Timeslot*</label>
                                        <input type="text" class="form-control" required name="timeslot"
                                            id="timeslot" placeholder="Date" value="{{@$bookingInfo->time_slot_id}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Diagnosis*</label>
                                        <textarea  class="form-control" required name="diagnosis"
                                            id="diagnosis" placeholder="Diagnosis"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Advice*</label>
                                        <textarea  class="form-control" required name="advice"
                                            id="advice" placeholder="Advice">
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <h6 style="font-weight: 600;
                                    color: #0d97c6;"> MEDICINE PRESCRIPTION </h6>
                                    <div class="card">
                                        <div class="table-responsive" style="min-height: 200px;">
                                            <table class="table card-table table-vcenter text-nowrap" id="productTable">
                                                <thead>
                                                    <tr>
                                                        <th>Medicine Name</th>
                                                        <th>Dosage</th>
                                                        <th>Duration</th>
                                                        <th>Actions</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id="productRowTemplate">
                                                        <td>
                                                            <select class="form-control medicine-name medsearch" name="medicine_id[]"
                                                                dis required>
                                                                <option value="">Please select medicine</option>
                                                                
                                                                @foreach ($medicines as $id => $medicine)
                                                                <option value="{{ $medicine->id }}">{{ $medicine->medicine_name }}</option>
                                                                @endforeach

                                                            </select>
                                                        </td>
                                                        <td class="medicine-batch-no">
                                                            <input type="text" class="form-control" required name="dosage[]">
                                                        </td>
                                                        <td class="medicine-quantity">
                                                            <input type="text" class="form-control" required name="duration[]">
                                                        </td>
                                                        <td><button type="button" onclick="removeFn(this)"
                                                                style="background-color: #007BFF; color: #FFF; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Remove</button>
                                                        </td>
                                                        <td class="display-med-row medicine-stock-id">
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addProductBtn">Add Row</button>
                                </div>
                            </div>
                            {{-- therapy --}}
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <h6 style="font-weight: 600;
                                    color: #0d97c6;"> THERAPY PRESCRIPTION </h6>
                                    <div class="card">
                                        <div class="table-responsive" style="min-height: 200px;">
                                            <table class="table card-table table-vcenter text-nowrap" id="productTable2">
                                                <thead>
                                                    <tr>
                                                        <th>Therapy</th>
                                                        <th>Booking Fee</th>
                                                        <th>Timeslot</th>
                                                        <th>Actions</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id="productRowTemplate2" class="product-row">
                                                        <td>
                                                            <select class="form-control therapy_id"  name="therapy_id[]" id="therapy_id">
                                                                <option value="">Please select therapy</option>
                                                                @foreach ($therapies as $id => $therapy)
                                                                <option value="{{ $therapy->id }}">{{ $therapy->therapy_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" id="booking_fee" class="form-control booking_fee" 
                                                            name="booking_fee[]" placeholder="Booking Fee" value="" readonly>
                                                    </td>
                                                        <td><select class="form-control timeslots"  name="timeslots[]" id="timeslots">
                                                            <option value="">--Select Timeslot--</option>
                                                        </select></td>
                                                        <td><button type="button" onclick="removeFn2(this)"
                                                                style="background-color: #007BFF; color: #FFF; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Remove</button>
                                                        </td>
                                                        <td class="display-med-row medicine-stock-id">
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addProductBtn2">Add Row</button>
                                </div>
                            </div>
                            <div class="row" style="margin-top:20px;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <center>
                                            <button type="submit" class="btn btn-raised btn-primary">
                                                <i class="fa fa-check-square-o"></i> Update Status & Save</button>
                                            <button type="reset" class="btn btn-raised btn-success">
                                                <i class="fa fa-refresh"></i> Reset</button>
                                            <a class="btn btn-danger" href="{{route('consultation.index')}}"> <i class="fa fa-times"></i>
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
            $('#consulting_date').val(currentDate);
        });
       

      function removeFn(button) {
    var row = $(button).closest('tr');
    row.remove();
}

function removeFn2(button) {
    var row = $(button).closest('tr');
    row.remove();
}

        $(document).ready(function() {
            
            $("#addProductBtn").click(function(event) {
                event.preventDefault();
                //alert(1);
                var newRow = $("#productRowTemplate").clone().removeAttr("style");
                newRow.find('select').addClass('medicine-select');
                newRow.find('input').val('').prop('readonly', false);
                newRow.find('input span').remove();
                
                $("#productTable tbody").append(newRow);
               
               // $('#productTable tbody tr:first-child button').show();
            });
            $("#addProductBtn2").click(function(event) {
                event.preventDefault();
                //alert(2)
                var newRow = $("#productRowTemplate2").clone().removeAttr("style");
                newRow.find('select').addClass('medicine-select');
                //newRow.find('input').val('').prop('readonly', false);
                newRow.find('input').val('');
                newRow.find('input span').remove();
                $("#productTable2 tbody").append(newRow);
              
                //$('#productTable2 tbody tr:first-child button').show();
            });
                //get booking fee

                $(document).on('change', '.therapy_id', function() {
                var row = $(this).closest('.product-row');
                var therapyID = $(this).val();
                var bookingDate = $('#booking_date').val();
        
                if (therapyID && bookingDate) {
                    $.ajax({
                        url: '{{ route('therapy.getTherapyBookingFee') }}',
                        type: "GET",
                        data: {
                            therapy_id: therapyID,
                            booking_date: bookingDate,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: "json",
                        success: function(data) {
                            var bookingFeeInput = row.find('.booking_fee');
                            var timeslotsSelect = row.find('.timeslots');

                            if (data.error) {
                                $(this).val('');
                                bookingFeeInput.val('');
                                timeslotsSelect.empty();
                            } else {
                                bookingFeeInput.val(data.booking_fee);
                                timeslotsSelect.empty().append('<option value="">--Select Timeslot--</option>');
                                $.each(data.timeslots, function(key, value) {
                                    var optionText = value.therapy_room_name + ' : ' + value.time_from + ' - ' + value.time_to;
                                    timeslotsSelect.append($('<option>', {
                                        value: value.timeslot,
                                        text: optionText
                                    }));
                                });
                            }
                        }
                    });
                } else {
                    var bookingFeeInput = row.find('.booking_fee');
                    var timeslotsSelect = row.find('.timeslots');

                    bookingFeeInput.val('');
                    timeslotsSelect.empty();
                }
            });
        });

        </script>
        
@endsection
