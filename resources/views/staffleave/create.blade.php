@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Create Leave Request</h3>
                </div>
                <div class="col-lg-12" style="background-color: #fff;">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('staffleave.store') }}" id="addFm" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label branch" id="branchLabel">Staff Branch*</label>
                                <select class="form-control" name="branch_id" id="branch_id">
                                    <option value="">Choose Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->branch_id }}" {{ old('branch_id') == $branch->branch_id ? 'selected' : '' }}>
                                            {{ $branch->branch_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="staffIdRow" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Staff Name*</label>
                                <select class="form-control" name="staff_id" id="staff_id">
                                    <option value="">Select a Branch...</option>
                                    <!-- Options will be dynamically populated using AJAX -->
                                </select>
                                <p class="no-staff-message" style="color: red; display: none;">No staff members in the selected branch.</p>
                            </div>
                        </div>
                    </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Leave Type*</label>
                                    <select class="form-control" name="leave_type" id="leave_type">
                                    <option value="" disabled selected>Choose Leave Type</option>
                                    @foreach($leave_types as $leave_type)
                                        <option value="{{ $leave_type->leave_type_id }}" {{ old('leave_type') == $leave_type->leave_type_id ? 'selected' : '' }}>
                                            {{ $leave_type->name }}
                                        </option>
                                    @endforeach
                                </select>

                                </div>
                            </div>
                            </div>
                            <div class="row" style="display: none;" id="totalDaysContainer">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Total Days*</label>
                                        <input type="text" class="form-control" value="{{ old('days') }}" name="total_days" id="total_days" placeholder="No Of Days" readonly>
                                        <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"> From Date*</label>
                                    <input type="date" class="form-control" name="from_date" id="from_date" value="{{ old('from_date') }}" placeholder="Emergency Contact" >
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Start Day*</label>
                                    <select class="form-control" name="start_day" id="start_day">
                                    <option value="Full Day" {{ old('start_day') == 'Full Day' ? 'selected' : '' }}>Full Day</option>
                                    <option value="First Half" {{ old('end_day') == 'First Half' ? 'selected' : '' }}>First Half </option>
                                    <option value="Second Half" {{ old('end_day') == 'Second Half' ? 'selected' : '' }}>Second Half</option>
                                    <!-- Options will be dynamically populated using AJAX -->
                                </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">To Date*</label>
                                    <input type="date" class="form-control" name="to_date" id="to_date"  value="{{ old('to_date') }}">
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">End Day*</label>
                                    <select class="form-control" name="end_day" id="end_day">
                                    <option value="Full Day" {{ old('end_day') == 'Full Day' ? 'selected' : '' }}>Full Day</option>
                                    <option value="First Half" {{ old('end_day') == 'First Half' ? 'selected' : '' }}>First Half </option>
                                    <option value="Second Half" {{ old('end_day') == 'Second Half' ? 'selected' : '' }}>Second Half</option>
                                    <!-- Options will be dynamically populated using AJAX -->
                                </select>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">No Of Days*</label>
                                    <input type="text" class="form-control" value="{{ old('days') }}" name="days" id="no_of_days" placeholder="No Of Days" readonly>
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Reason*</label>
                                    <textarea class="form-control" name="reason" placeholder="Reason For Leave">{{ old('reason') }}</textarea>

                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <center>
                                <button type="submit" id="submitForm" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Add
                                </button>
                                <a class="btn btn-success" href="{{ route('staffleave.create') }}">Reset</a>
                                <a class="btn btn-danger" href="{{ route('staffleave.index') }}">Cancel</a>
                            </center>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
<!-- Other HTML content... -->

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<script type="text/javascript">
$(document).ready(function () {
    // Hide the staff_id div on page load
    $('#staff_id').closest('.row').hide();
    $('.no-staff-message').hide();

    // Handle change event on branch select
    $('#branch_id').change(function () {
        var branchId = $(this).val();

        // Clear the current options in the staff select
        $('#staff_id').empty().append('<option value="">Select a Branch...</option>');
        $('.no-staff-message').hide();

        // Check if a branch is selected
        if (branchId) {
            // Make an AJAX request to get the staff names for the selected branch
            $.ajax({
                type: 'GET',
                url: '{{ route("get-staff-names", ["branchId" => ":branchId"]) }}'.replace(':branchId', branchId),
                success: function (data) {
                    // Populate the staff select with received data
                    $('#staff_id').empty();
                    if (Object.keys(data).length > 0) {
                        $.each(data, function (key, value) {
                            // Create an option element
                            var option = $('<option>', {
                                value: key,
                                text: value
                            });

                            // Append the option to the staff select
                            $('#staff_id').append(option);
                        });
                        // Show the staff_id div when staff options are available
                        $('#staff_id').closest('.row').show();
                    } else {
                        $('.no-staff-message').show();
                    }
                },
                error: function (error) {
                    console.error(error);
                }
            });
        } else {
            // If no branch is selected, hide the staff_id div
            $('#staff_id').closest('.row').hide();
        }
    });
});


        // Handle change event on From Date and To Date inputs
        $('#from_date, #to_date, #start_day, #end_day').change(function () {
        var fromDate = new Date($('#from_date').val());
        var toDate = new Date($('#to_date').val());
        var startDay = $('#start_day').val();
        var endDay = $('#end_day').val();

        // Check if both dates are valid
        if (!isNaN(fromDate.getTime()) && !isNaN(toDate.getTime())) {
            // Check if the selected from_date and to_date are the same
            var sameDate = fromDate.toISOString().split('T')[0] === toDate.toISOString().split('T')[0];

            // Show or hide the End Day dropdown based on the sameDate condition
            $('#end_day').closest('.form-group').toggle(!sameDate);

            // Calculate the difference in days based on start and end days
            var differenceInDays = calculateDifferenceInDays(fromDate, toDate, startDay, endDay);

            // Update the No Of Days input
            $('#no_of_days').val(differenceInDays);
        }
    

        // Handle change event on staff select
        $('#staff_id').change(function () {
            var staffId = $(this).val();

            // Make an AJAX request to get the total leaves for the selected staff
            $.ajax({
                type: 'GET',
                url: '{{ route("get-total-leaves", ["staffId" => ":staffId"]) }}'.replace(':staffId', staffId),
                success: function (data) {
                    // Update the total days input with received data
                    $('#total_days').val(data.total_leaves);
                },
                error: function (error) {
                    console.error(error);
                }
            });
        });

        // Function to calculate the difference in days
        function calculateDifferenceInDays(startDate, endDate, startDay, endDay) {
            // Calculate the difference in days and include the last day
            var differenceInTime = endDate.getTime() - startDate.getTime();
            var differenceInDays = Math.floor(differenceInTime / (1000 * 3600 * 24)) + 1;

            // Adjust the difference based on start and end days
            if(startDay === 'First Half' && endDay === 'First Half' || endDay === 'Second Half'){
                differenceInDays -= 0.5;
            }if(startDay === 'Second Half' && endDay === 'First Half' || endDay === 'Second Half'){
                differenceInDays -= 0.5;
            }
            else if(startDay === 'First Half' || startDay === 'Second Half') {
                differenceInDays -= 0.5; // If both halves are selected, subtract half a day
            } else if (endDay === 'First Half' || endDay === 'Second Half') {
                differenceInDays -= 0.5; // If only one half is selected, subtract half a day
            }

            return differenceInDays;
        }
    });
</script>

<script>
        var validator; // Declare validator outside $(document).ready()

        $(document).ready(function () {
            validator = $("#addFm").validate({
                rules: {
                    branch_id: "required",
                    staff_id: "required",
                    leave_type: "required",
                    total_days: "required",
                    from_date: "required",
                    start_day: "required",
                    end_day: "required",
                    to_date: "required",
                    days: "required",    
                    reason: "required",           
                 },
                messages: {
                    branch_id: "Please select branch.",
                    staff_id: "Please select staff.",
                    leave_type: "Please enter leave_type.",
                    total_days: "Please enter total days.",
                    from_date: "Please enter from date.",
                    start_day: "Please enter start day.",
                    end_day: "Please enter end day.",
                    to_date: "Please enter to date.",
                    days: "Please enter days.",
                    reason: "Please enter reason.",
                },
                submitHandler: function (form) {
                    // Your form submission logic here
                    form.submit();
                },
            });

            $(document).on('click', '#submitForm', function () {
                if (validator.form()) {
                    $('#addFm').submit();
                } else {
                    flashMessage('w', 'Please fill all mandatory fields');
                }
            });

            function flashMessage(type, message) {
                // Implement or replace this function based on your needs
                console.log(type, message);
            }
        });
    </script>
@endsection



