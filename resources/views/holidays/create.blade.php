@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Create Holiday</h3>
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
                    <form action="{{ route('holidays.store') }}" id="addFm" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label branch" id="branchLabel">Holiday Name*</label>
                                    <input type="text" class="form-control" name="holiday_name" id="holiday_name" value="{{ old('holiday_name') }}" placeholder="Holiday Name" >
                                </div>
                            </div>
                            <div class="col-md-6">
                            <div class="form-group">
                                    <label class="form-label">Holiday Type*</label>
                                    <select class="form-control" name="leave_type" id="leave_type">
                                    <option value="" disabled selected>Choose Holiday Type</option>
                                    <option value="Weekend">Weekend</option>
                                    <option value="General">General</option>
                                </select>

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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">To Date*</label>
                                    <input type="date" class="form-control" name="to_date" id="to_date"  value="{{ old('to_date') }}">
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Year*</label>
                                    <input type="number" class="form-control" name="year" id="year" placeholder="YYYY" maxlength="4"value="{{ old('year') }}">
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div> 
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Company*</label>
                                    <input type="text" class="form-control" name="company" id="company" value="Ayushman" placeholder="Company" readonly>
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div> 
                        </div>
                       
</br>
                        <div class="form-group">
                            <center>
                                <button type="submit" id="submitForm" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Add
                                </button>
                                <a class="btn btn-success" href="{{ route('holidays.index') }}">Reset</a>
                             
                                <a class="btn btn-danger" href="{{ route('holidays.index') }}">Cancel</a>
                            </center>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script type="text/javascript">
  $(document).ready(function () {
        // Handle change event on branch select
        $('#branch_id').change(function () {
            var branchId = $(this).val();

            // Clear the current options in the staff select
            $('#staff_id').empty().append('<option value="">Select a Branch...</option>');
            $('.no-staff-message').hide();

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
                    } else {
                        $('.no-staff-message').show();
                    }
                },
                error: function (error) {
                    console.error(error);
                }
            });
        });
    });

    $(document).ready(function () {
        // Handle change event on From Date and To Date inputs
        $('#from_date, #to_date').change(function () {
            var fromDate = new Date($('#from_date').val());
            var toDate = new Date($('#to_date').val());

            // Check if both dates are valid
            if (!isNaN(fromDate.getTime()) && !isNaN(toDate.getTime())) {
                // Calculate the difference in days and include the last day
                var differenceInTime = toDate.getTime() - fromDate.getTime();
                var differenceInDays = Math.floor(differenceInTime / (1000 * 3600 * 24)) + 1;

                // Update the No Of Days input
                $('#no_of_days').val(differenceInDays);
            }
        });
    });
</script>
<script>
        var validator; // Declare validator outside $(document).ready()

        $(document).ready(function () {
            validator = $("#addFm").validate({
                rules: {
                    holiday_name: "required",
                    leave_type: "required",
                    from_date: "required",
                    to_date: "required",
                    year: {
    required: true,
    digits: true,
    min: new Date().getFullYear()
},
                    },
                messages: {
                    holiday_name: "Please enter holiday name.",
                    leave_type: "Please select leave type.",
                    from_date: "Please enter from date.",
                    to_date: "Please enter to date.",
                    year: "Please enter a valid year.",
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



