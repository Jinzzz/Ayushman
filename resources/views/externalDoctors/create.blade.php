@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Create External Doctor</h3>
                </div>

                <div class="col-lg-12" style="background-color: #fff;">
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
                    <form action="{{ route('externaldoctors.store') }}" id="addFm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Doctor Name*</label>
                                    <input type="text" class="form-control" required name="doctor_name" value="{{ old('doctor_name') }}" placeholder="Doctor Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Contact No*</label>
                                    <input type="text" class="form-control" required name="contact_no" value="{{ old('contact_no') }}" placeholder="Contact No" pattern="[0-9]{10}" title="Please enter digits only" oninput="validateInput(this)">
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="contact_email" maxlength="100" value="{{ old('contact_email') }}" placeholder="Email">
                                    <div class="text-danger" id="email-error"></div>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" placeholder="Address">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Remarks</label>
                                    <textarea class="form-control" name="remarks" placeholder="Remarks">{{ old('remarks') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Commission(%)</label>
                                    <input type="number" class="form-control" required name="commission" value="{{ old('commision') }}" placeholder="Commission" maxlength="3" oninput="validateCommission(this);">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-label">Status</div>
                                    <label class="custom-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <!-- Hidden field for false value -->
                                        <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" checked>
                                        <span id="statusLabel" class="custom-switch-indicator"></span>
                                        <span id="statusText" class="custom-switch-description">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <center>
                                    <button type="submit" id="submitForm" class="btn btn-primary">
                                        <i class="fa fa-check-square-o"></i> Add
                                    </button>
                                    <a class="btn btn-danger" href="{{ route('externaldoctors.index') }}">Cancel</a>
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/latest/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        $.validator.addMethod("checkPercentage", function(value, element) {
            console.log("Inside checkPercentage", value);
            return !isNaN(value) && parseFloat(value) <= 100;
        }, "Commission must be a valid number and should not exceed 100%");

        var validator = $("#addFm").validate({
            ignore: "",
            rules: {
                doctor_name: {
                    required: true,
                    maxlength: 100
                },
                contact_no: {
                    required: true,
                    maxlength: 10
                },
                commission: {
                    required: true,
                    checkPercentage: true
                },
                contact_email: {
                    email: true,
                    maxlength: 100
                },
            },
            messages: {
                doctor_name: {
                    required: 'Please enter doctor name.',
                    maxlength: 'Doctor name must not exceed 100 characters.'
                },
                contact_no: {
                    required: 'Please enter contact number.',
                    digits: 'Please enter a valid 10-digit phone number.',
                },
                commission: {
                    required: 'Please enter commission.',
                    checkPercentage: 'Commission must be a valid number and should not exceed 100%',
                },
                contact_email: {
                    email: 'Please enter a valid email address.',
                    maxlength: 'Email address must not exceed 100 characters.'
                },
            },
            errorPlacement: function(label, element) {
                label.addClass('text-danger');
                label.insertAfter(element.parent().children().last());
            },
            highlight: function(element, errorClass) {
                $(element).parent().addClass('has-error');
                $(element).addClass('form-control-danger');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parent().removeClass('has-error');
                $(element).removeClass('form-control-danger');
            }
        });

        $(document).on('click', '#submitForm', function() {
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
    // impliment jQuery Validation 
    $(document).ready(function() {
        $('#contact_email').on('input', function() {
            var emailInput = $(this).val();
            var emailErrorDiv = $('#email-error');

            if (emailInput.trim() === '' || isValidEmail(emailInput)) {
                emailErrorDiv.text('');
            } else {
                emailErrorDiv.text('Please enter a valid email address.');
            }
        });

        function isValidEmail(email) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    });
</script>
<script>
    function validateInput(input) {
        var inputValue = input.value;

        // Remove any non-numeric characters from the input
        var numericValue = inputValue.replace(/[^0-9]/g, '');

        // Ensure the input does not exceed 10 characters
        if (numericValue.length > 10) {
            // Truncate the input to the first 10 digits
            numericValue = numericValue.slice(0, 10);
        }

        // Update the input value with the numeric value
        input.value = numericValue;

        // Check if the resulting value has exactly 10 digits
        if (numericValue.length !== 10) {
            input.setCustomValidity("Please enter exactly 10-digit numbers.");
            input.parentNode.querySelector('.error-message').style.display = 'block';
        } else {
            input.setCustomValidity("");
            input.parentNode.querySelector('.error-message').style.display = 'none';
        }
    }


    function validateCommission(input) {
        // Remove any non-numeric characters from the input
        input.value = input.value.replace(/[^0-9]/g, '');

        // Ensure the value is within the min and max limits
        var numericValue = parseInt(input.value, 10);
        if (isNaN(numericValue)) {
            input.value = ''; // Clear the input if it's not a valid number
        } else if (numericValue < 0) {
            input.value = '0'; // Set to the minimum value (0) if it's below 0
        } else if (numericValue > 100) {
            input.value = '100'; // Set to the maximum value (100) if it's above 100
        }
    }
</script>



@endsection