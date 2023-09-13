@extends('layouts.app')

@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Create Patient</h3>
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
               <form action="{{ route('patients.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Patient Name*</label>
                           <input type="text" class="form-control" required name="patient_name" value="{{ old('patient_name') }}" placeholder="Patient Name">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Patient Email</label>
                           <input type="email" class="form-control" value="{{ old('patient_email') }}" placeholder="Patient Email">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Patient Mobile*</label>
                           <input type="text" class="form-control" required name="patient_mobile" value="{{ old('patient_mobile') }}" placeholder="Patient Mobile">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Patient Address</label>
                           <textarea class="form-control" placeholder="Patient Address">{{ old('patient_address') }}</textarea>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="patient_gender" class="form-label">Gender*</label>
                           <select class="form-control" name="patient_gender" id="patient_gender">
                              <option value="">Choose Gender</option>
                              @foreach($gender as $id => $gender)
                              <option value="{{ $id }}">{{ $gender }}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Patient Dob*</label>
                           <input type="date" class="form-control" placeholder="Patient Dob">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="patient_blood_group_id" class="form-label">Blood Group</label>
                           <select class="form-control" name="patient_blood_group_id" id="patient_blood_group_id">
                              <option value="">Choose Blood Group</option>
                              @foreach($bloodgroup as $id => $bloodgroup)
                              <option value="{{ $id }}">{{ $bloodgroup }}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Emergency Contact Person</label>
                           <input type="text" class="form-control" name="emergency_contact_person" placeholder="Emergency Contact Person">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Emergency Contact</label>
                           <input type="text" class="form-control" name="emergency_contact" placeholder="Emergency Contact">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Maritial Status</label>
                           <select class="form-control" name="maritial_status" id="maritial_status">
                              <option value="">Choose Maritial Status</option>
                              @foreach($maritialstatus as $masterId => $masterValue)
                              <option value="{{ $masterId }}">{{ $masterValue }}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                  </div>
                
                  <div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="form-label">Patient Registration Type</label>
            <select class="form-control" name="patient_registration_type" id="patient_registration_type" required>
                <option value="">Choose Registration Type</option>
                <option value="Self">Self</option>
                <option value="Online">Online</option>
            </select>
        </div>
    </div>
</div>

                  
                    <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Whatsapp Number</label>
                           <input type="text" class="form-control" value="{{ old('whatsapp_number') }}" placeholder="Whatsapp Number">
                        </div>
                     </div>
                    
                  </div>
                 
                      <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Medical History</label>
                           <textarea class="form-control" required name="patient_medical_history" placeholder="Medical History">{{ old('patient_medical_history') }}</textarea>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Patient Current Medication</label>
                           <textarea class="form-control" required name="patient_current_medications" placeholder="Patient Current Medication">{{ old('patient_current_medications') }}</textarea>
                        </div>
                     </div>
                  </div>
                    
                      
              
                           
                     <div class="col-md-6">
                        <div class="form-group">
                           <div class="form-label">Status</div>
                           <label class="custom-switch">
                              <input type="hidden" name="is_active" value="0">
                              <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" checked>
                              <span id="statusLabel" class="custom-switch-indicator"></span>
                              <span id="statusText" class="custom-switch-description">Active</span>
                           </label>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <center>
                        <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add
                        </button>
                        <button type="reset" class="btn btn-raised btn-success">
                           Reset
                        </button>
                        <a class="btn btn-danger" href="{{ route('patients.index') }}">Cancel</a>
                     </center>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

<script>
    function togglePassword() {
            const passwordInput = document.querySelector("#password");

            if (passwordInput.getAttribute("type") == "text") {
                $("#eye").removeClass("fa-eye");
                $("#eye").addClass("fa-eye-slash");

            } else {
                $("#eye").removeClass("fa-eye-slash");
                $("#eye").addClass("fa-eye");

            }

            const type = passwordInput.getAttribute("type") === "text" ? "password" : "text"
            passwordInput.setAttribute("type", type)
        }


    function toggleConfirmPassword() {
    const confirmPasswordInput = document.querySelector("#confirmPassword");

    if (confirmPasswordInput.getAttribute("type") == "text") {
        $("#confirmEye").removeClass("fa-eye");
        $("#confirmEye").addClass("fa-eye-slash");
    } else {
        $("#confirmEye").removeClass("fa-eye-slash");
        $("#confirmEye").addClass("fa-eye");
    }

    const type = confirmPasswordInput.getAttribute("type") === "text" ? "password" : "text";
    confirmPasswordInput.setAttribute("type", type);
}



    
    function validatePassword() {
        var passwordInput = document.getElementById("password");
        var confirmInput = document.getElementById("confirm_password");
        var passwordError = document.getElementById("password_error");
        
        if (passwordInput.value !== confirmInput.value) {
            passwordError.style.display = "block";
        } else {
            passwordError.style.display = "none";
        }
    }
</script>


