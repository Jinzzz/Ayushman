@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Create Staff</h3>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('status'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                </div>
                <div class="col-lg-12">
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
                    <form action="{{ route('staffs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Staff Type*</label>
                                    <select class="form-control" name="staff_type" id="staff_type">
                                        <option value="">Select Staff Type</option>
                                        @foreach($stafftype as $masterId => $masterValue)
                                            <option value="{{ $masterId }}" {{ old('staff_type') == $masterId ? 'selected' : '' }}>
                                                {{ $masterValue }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Employment Type*</label>
                                    <select class="form-control" name="employment_type" id="employment_type">
                                        <option value="">Select Employment Type</option>
                                        @foreach($employmentType as $masterId => $masterValue)
                                            <option value="{{ $masterId }}" {{ old('employment_type') == $masterId ? 'selected' : '' }}>
                                                {{ $masterValue }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Username*</label>
                                    <input type="text" class="form-control" required name="staff_username" value="{{ old('staff_username') }}" placeholder="Staff Username">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Password*</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" required name="password" id="password" value="{{old('password')}}" placeholder="Password">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye" id="togglePassword" onclick="togglePasswordVisibility()"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Confirm Password*</label>
                                    <div class="input-group">
                                       <input type="text" class="form-control" required name="confirm_password" value="{{old('confirm_password')}}" placeholder="Confirm Password" id="confirm_password" onkeyup="validatePassword()">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye" id="toggleConfirmPassword"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small id="password_error" class="text-secondary"  style="color: green; display: none;">Passwords do not match.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Staff Name*</label>
                                    <input type="text" class="form-control" required name="staff_name" value="{{ old('staff_name') }}" placeholder="Staff Name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_gender" class="form-label">Gender*</label>
                                    <select class="form-control" name="gender" id="gender">
                                        <option value="">Choose Gender</option>
                                        @foreach($gender as $id => $gender)
                                            <option value="{{ $id }}" {{ old('gender') == $id ? 'selected' : '' }}>
                                                {{ $gender }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Branch*</label>
                                    <select class="form-control" name="branch_id" id="branch_id">
                                        <option value="">Choose Branch</option>
                                        @foreach($branch as $id => $branchName)
                                            <option value="{{ $id }}"{{ old('branch_id') == $id ? 'selected' : '' }}>
                                                {{ $branchName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Date Of Birth*</label>
                                    <input type="date" class="form-control" required name="date_of_birth" value="{{ old('date_of_birth') }}" placeholder="Date Of Birth">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email*</label>
                                    <input type="email" class="form-control" required name="staff_email" value="{{ old('staff_email') }}" placeholder="Staff Email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Contact Number*</label>
                                    <input type="text" class="form-control" required name="staff_contact_number" value="{{old('staff_contact_number')}}" placeholder="Contact Number" pattern="[0-9]+" title="Please enter digits only" oninput="validateInput(this)">
                                       <p class="error-message" style="color: green; display: none;">Please enter digits only.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Address*</label>
                                    <textarea class="form-control" required name="staff_address" placeholder="Address">{{ old('staff_address') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Qualification*</label>
                                    <select class="form-control" name="staff_qualification" id="staff_qualification">
                                        <option value="">Select Qualification</option>
                                        @foreach($qualification as $masterId => $masterValue)
                                            <option value="{{ $masterId }}"{{ old('staff_qualification') == $masterId ? 'selected' : '' }}>{{ $masterValue }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Work Experience*</label>
                                    <input type="text" class="form-control" required name="staff_work_experience" value="{{ old('staff_work_experience') }}" placeholder="Work Experience">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Logon Type*</label>
                                    <select class="form-control" name="staff_logon_type" id="staff_logon_type">
                                        <option value="">Select Logon Type</option>
                                        @foreach($stafflogonType as $masterId => $masterValue)
                                            <option value="{{ $masterId }}"{{ old('staff_logon_type') == $masterId ? 'selected' : '' }}>{{ $masterValue }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Commission Type*</label>
                                    <select class="form-control" name="staff_commission_type" id="staff_commission_type">
                                        <option value="">Select Commission Type</option>
                                        @foreach($commissiontype as $masterId => $masterValue)
                                            <option value="{{ $masterId }}"{{ old('staff_commission_type') == $masterId ? 'selected' : '' }}>{{ $masterValue }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Staff Commission*</label>
                                    <input type="text" class="form-control" required name="staff_commission" value="{{ old('staff_commission') }}" placeholder="Staff Commission">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Booking Fee*</label>
                                    <input type="text" class="form-control" required name="staff_booking_fee" value="{{ old('staff_booking_fee') }}" placeholder="Booking Fee">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-label">Status</div>
                                    <label class="custom-switch">
                                        <input type="hidden" name="is_active" value="0"> <!-- Hidden field for false value -->
                                        <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" checked>
                                        <span id="statusLabel" class="custom-switch-indicator"></span>
                                        <span id="statusText" class="custom-switch-description">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-raised btn-primary">
                                <i class="fa fa-check-square-o"></i> Add
                            </button>
                            <button type="reset" class="btn btn-raised btn-success">
                                Reset
                            </button>
                            <a class="btn btn-danger" href="{{ route('staffs.index')}}">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    function togglePasswordVisibility() {
      
        if (field.type === "password") {
            field.type = "text";
            toggle.classList.remove("fa-eye");
            toggle.classList.add("fa-eye-slash");
        } else {
            field.type = "password";
            toggle.classList.remove("fa-eye-slash");
            toggle.classList.add("fa-eye");
        }
    }

    function validatePassword() {

        var password = document.getElementById("password");
        var confirm_password = document.getElementById("confirm_password");
        var error_message = document.getElementById("password_error");

        if (password.value !== confirm_password.value) {
            error_message.style.display = "block";
            confirm_password.classList.add("is-invalid");
            return false;
        } else {
            error_message.style.display = "none";
            confirm_password.classList.remove("is-invalid");
            return true;
        }
    }
</script>
<script>
   function validateInput(input) {
      var inputValue = input.value;
      var numberPattern = /^[0-9]*$/;

      if (!numberPattern.test(inputValue)) {
         input.setCustomValidity("Only numbers are allowed.");
         input.parentNode.querySelector('.error-message').style.display = 'block';
      } else {
         input.setCustomValidity("");
         input.parentNode.querySelector('.error-message').style.display = 'none';
      }
   }
</script>

