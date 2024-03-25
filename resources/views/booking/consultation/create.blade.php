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
   .breadcrumb{
       background:transparent!important;
   }
       .no-act {
    pointer-events: none;
}
button#sub-butn {
    padding: 0 10px;
    margin-top: 0;
    border-radius: 5px;
    font-size: 12px;
}
.hideF {
    display:none !importantimportant;
}
select#patient_ids {
    pointer-events: none;
}

</style>
<!--<div class="container">-->
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Add Consultation Booking</h3>
            </div>
            <!-- Success message -->
            <div class="col-lg-12 card-background" style="background-color:#fff" ;>
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
               <form action="{{ route('patientbooking.consultation.booking') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()" id="form-wellness">
                  @csrf
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Booking Date*</label>
                           <input type="date" class="form-control" required name="booking_date" id="booking_date" placeholder="Date" value="{{ old('booking_date') }}">
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
                              <select class="form-control"  name="patient_id" id="patient_id" required>
                                 <option value="">--Select Patient--</option>
                                 @foreach ($patients as $id => $patient)
                                 <option value="{{ $patient->id }}">
                                    {{ ucwords(strtolower($patient->patient_name)) }} -
                                    {{ $patient->patient_code }}
                                 </option>
                                 @endforeach
                              </select>
                              <span class="input-group-append">
                              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPatientModal">+</button>
                              </span>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Doctors*</label>
                           <select class="form-control" required name="staff_id" id="staff_id">
                              <option value="">--Select Doctor--</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Booking Fee*</label>
                           <input type="text" id="booking_fee" class="form-control" required name="booking_fee" placeholder="Booking Fee" value="" readonly>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Doctor Timeslot*</label>
                           <select class="form-control" required name="timeslots" id="timeslots">
                              <option value="">--Select Timeslot--</option>
                           </select>
                        </div>
                     </div>
                  </div>
                  <div class="row" id="membership_details" style="display:none;">
                     <div class="col-md-12">
                        <div class="form-group" style="border: 1px solid #000;">
                           <span style="margin-left: 10px;
                              color: #0d97c6;
                              font-weight: bold;font-size:14px;">Membership
                           Details : Package name: <span id="package_name"></span> | Start Date: <span id="start_date"></span> | Expiry: <span id="expiry_date"></span></span>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="custom-control custom-checkbox">
                           <input type="checkbox" class="custom-control-input" name="is_family" value="1" id="familyCheckbox">
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
                              <!--<button type="button" class="btn btn-primary showF" data-toggle="modal" data-target="#addFamilyModal" style="display:none;">+</button>-->
                              <button type="button" class="btn btn-primary  "  onclick="openModal(this)">+</button>
                              </span>
                           </div>
                        </div>
                     </div>
                  </div>
                            <div class="no-pay row">
                           
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="custom-control custom-checkbox">
                                         <input type="checkbox" value="1" name="noBill" class="custom-control-input">
                                        <span class="custom-control-label">No Bill</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="custom-control custom-checkbox">
                           <input type="checkbox" class="custom-control-input" name="pay_now" value="1" id="paynowCheck">
                           <span class="custom-control-label">Pay Now ?</span>
                           </label>
                        </div>
                     </div>
                  </div>
  
                        <div class="discount-div row" style=" display:none;">
                            
    
                        <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Discount</label>
                                    <input type="text" id="discount_amount" class="form-control numericInputvalue"  name="discount_amount" placeholder="Discount" value="" data-discount="{{ $discount }}">
                                    <div id="error-msg" style="display: none; color: red;">Discount cannot be greater than {{ $discount }}</div>
                                </div>
                            </div>
      
                            
                        
                            <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Discount Amount</label>
                                <input type="number" class="form-control discount numericInputvalue" name="discount_total" id="discount_Total" placeholder="Discount Amount" >
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Total Amount*</label>
                                <input type="number" class="form-control paid-amount numericInputvalue" name="paid_amount" id="paid_amount" placeholder="Total Amount" required>
                            </div>
                        </div>
                    </div>

                  <div class="row" id="paymentdiv" style="display: none;">
                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Payable Amount*</label>
                           <input type="text" id="payable_amount" class="form-control numericInputvalue"  name="payable_amount[]" placeholder="Payable amount" value="" >
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label for="payment-type" class="form-label">Payment Mode</label>
                           <select class="form-control payment_mode"  name="payment_mode[]" placeholder="Payment Mode" id="payment_mode" onchange="updateDepositTo()">
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
                           <select class="form-control" name="deposit_to[]" id="deposit_to">
                              <option value="">Deposit To</option>
                           </select>
                        </div>
                     </div>
                        <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">Reference Number</label>
                           <input type="text" id="refernce_no" class="form-control"  name="refernce_no[]" placeholder="Reference Number" value="" >
                        </div>
                     </div>
                     <div class="col-md-1">
                        <div class="form-group">
                           <label class="form-label">Action</label>
                           <button type="button" class="btn btn-raised btn-danger removebtn no-act" id="removebtn" onclick="removeFn1(this)"> Remove </button>
                        </div>
                     </div>
                  </div>
                   <button type="button" class="btn btn-raised btn-primary" id="addRow" style="margin-left: 15px; display:none;" > Add New Row </button>
                  <div class="row" style="margin-top:20px;">
                     <div class="col-md-12">
                        <div class="form-group">
                           <center>
                              <button type="submit" class="btn btn-raised btn-primary" id="sub-butn">
                              <i class="fa fa-check-square-o"></i> Add</button>
                              <!--<button type="reset" class="btn btn-raised btn-success">-->
                              <!--<i class="fa fa-refresh"></i> Reset</button>-->
                              <a class="btn btn-raised btn-success" href="{{ route('create.consultation.booking') }}"><i class="fa fa-refresh"></i> Reset</a>
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

<!--</div>-->
<div class="modal fade" id="addPatientModal" tabindex="-1" role="dialog" aria-labelledby="addPatientModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="addPatientModalLabel">Add Patient</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <!-- Add your form or content for adding a new patient here -->
            <!-- Example: -->
            <form action="{{ route('savepatient.consultation.booking') }}" method="POST" enctype="multipart/form-data">
               @csrf
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Patient Name*</label>
                        <input type="text" class="form-control" name="patient_name" maxlength="100" value="{{ old('patient_name') }}" placeholder="Patient Name">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Patient Email</label>
                        <input type="email" class="form-control" value="{{ old('patient_email') }}" maxlength="200" name="patient_email" placeholder="Patient Email">
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Patient Mobile*</label>
                      <input type="text" class="form-control" name="patient_mobile" maxlength="10" id="numericInput" pattern="\d+(\.\d{0,2})?"  value="{{ old('patient_mobile') }}" placeholder="Patient Mobile">

                        <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Patient Address</label>
                        <textarea class="form-control" name="patient_address" placeholder="Patient Address">{{ old('patient_address') }}</textarea>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="patient_gender" class="form-label">Gender</label>
                        <select class="form-control" name="patient_gender" id="patient_gender">
                           <option value="">Choose Gender</option>
                           @foreach($genders as $id => $gender)
                           <option value="{{ $id }}">{{ $gender }}</option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Date Of Birth</label>
                        <input type="date" class="form-control" name="patient_dob" placeholder="Patient Dob" id="patient_dob">
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="patient_blood_group_id" class="form-label">Blood Group</label>
                        <select class="form-control" name="patient_blood_group_id" id="patient_blood_group_id">
                           <option value="">Choose Blood Group</option>
                           @foreach($bloodgroups as $id => $bloodgroup)
                           <option value="{{ $id }}">{{ $bloodgroup }}</option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Emergency Contact Person</label>
                        <input type="text" class="form-control" name="emergency_contact_person"  placeholder="Emergency Contact Person">
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Emergency Contact</label>
                        <input type="number" class="form-control" name="emergency_contact" maxlength="10"  id="numericInput1" pattern="\d+(\.\d{0,2})?" placeholder="Emergency Contact">
                        <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Marital Status</label>
                        <select class="form-control" name="marital_status" id="marital_status">
                           <option value="">Choose Marital Status</option>
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
                        <select class="form-control" name="patient_registration_type" id="patient_registration_type">
                           <option value="self" selected>Self</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Whatsapp Number</label>
                        <input type="number" class="form-control" value="{{ old('whatsapp_number') }}" maxlength="10" id="numericInput2" pattern="\d+(\.\d{0,2})?" name="whatsapp_number" placeholder="Whatsapp Number">
                        <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Medical History</label>
                        <textarea class="form-control" name="patient_medical_history" id="medicalHistory" placeholder="Medical History">{{ old('patient_medical_history') }}</textarea>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Patient Current Medication</label>
                        <textarea class="form-control" name="patient_current_medications" id="currentMedication" placeholder="Patient Current Medication">{{ old('patient_current_medications') }}</textarea>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <label class="custom-switch">
                            <input type="hidden" name="is_active" value="0">
                            <!-- Hidden field for false value -->
                            <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" checked>
                            <span id="statusLabel" class="custom-switch-indicator"></span>
                            <span id="statusText" class="custom-switch-description">Active</span>
                        </label>
                    </div>


                  </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="form-label">Has Credit</div>
                        <label class="custom-switch">
                            <input type="hidden" name="has_credit" value="0">
                            <input type="checkbox" id="has_credit" name="has_credit" onchange="toggleStatus1(this)" class="custom-switch-input">
                            <span class="custom-switch-indicator"></span>
                            <span id="statusText1" class="custom-switch-description">Inactive</span>
                        </label>
                    </div>
                </div>

               </div>
               <div class="form-group" style="display:flex;align-items:center;justify-content:center">
                  <center>
                     <button type="submit" class="btn btn-raised btn-primary">
                     <i class="fa fa-check-square-o"></i> Add
                     </button>
                     <button type="reset" class="btn btn-raised btn-success">
                     Reset
                     </button>
                     
            <button type="button" class="btn btn-raised btn-danger" data-dismiss="modal">Close
            <span aria-hidden="true">&times;</span>
            </button>
                  </center>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="addFamilyModal" tabindex="-1" aria-labelledby="addFamilyModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="addFamilyModalLabel">Add Family Member</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <!-- Your form or content for adding family members goes here -->
            <form action="{{ route('savemember.consultation.booking') }}" method="POST" enctype="multipart/form-data">
               @csrf
               <div class="row" style="justify-content:center">

                       <div class="form-group">
            <label class="form-label">Patient*</label>
            <div class="input-group">
                <select class="form-control" name="patient_id" id="patient_ids" >
                    <option value="">--Select Patient--</option>
                    @foreach ($patients as $id => $patient)
                        <option value="{{ $patient->id }}" {{ old('`````') == $patient->id ? 'selected' : '' }}>
                            {{ ucwords(strtolower($patient->patient_name)) }} -
                            {{ $patient->patient_code }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
          
                  <div class="col-md-6">
                  <div class="form-group">
                        <label class="form-label">Member Name*</label>
                        <input type="text" class="form-control" name="family_member_name" maxlength="100" value="{{ old('family_member_name') }}" placeholder="Enter Name">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Member Email</label>
                        <input type="email" class="form-control" value="{{ old('family_member_email') }}" maxlength="200" name="family_member_email" placeholder="Enter Email">
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Member Mobile*</label>
                        <input type="number" class="form-control" name="family_member_phone" maxlength="100" id="numericInput3" pattern="\d+(\.\d{0,2})?" value="{{ old('family_member_phone') }}" placeholder="Enter Phone Number">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                     <label for="patient_gender" class="form-label">Gender*</label>
                     <select class="form-control" name="family_member_gender" id="family_member_gender">
                        <option value="">Choose Gender</option>
                        @foreach($genders as $id => $gender)
                            <option value="{{ $id }}" @if(old('family_member_gender') == $id) selected @endif>{{ $gender }}</option>
                        @endforeach
                    </select>

                     </div>
                  </div>
               </div>
               <div class="row">
               <div class="col-md-6">
                     <div class="form-group">
                        <label for="patient_blood_group_id" class="form-label">Blood Group</label>
                        <select class="form-control" name="family_member_blood_group_id" id="family_member_blood_group_id">
    <option value="">Choose Blood Group</option>
    @foreach($bloodgroups as $id => $bloodgroup)
        <option value="{{ $id }}" @if(old('family_member_blood_group_id') == $id) selected @endif>{{ $bloodgroup }}</option>
    @endforeach
</select>

                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Date Of Birth*</label>
                        <input type="date" class="form-control" name="family_member_dob" id="family_member_dob" value = "{{ old ('family_member_dob') }}" placeholder="Date Of Birth">
                     </div>
                  </div>
               </div>
               <div class="row">
               <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="family_member_address" placeholder="Enter Address">{{ old('family_member_address') }}</textarea>
                     </div>
                  </div>

                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Relationship</label>
                        <select class="form-control" name="family_member_relationship_id" id="family_member_relationship_id">
                        <option value="">Choose Relationship</option>
                        @foreach($relationships as $id => $relationship)
                            <option value="{{ $id }}" @if(old('family_member_relationship_id') == $id) selected @endif>{{ $relationship }}</option>
                        @endforeach
                    </select>

                     </div>
                  </div>
               </div>
               <div class="form-group" style="display:flex;align-items:center;justify-content:center">
                  <center>
                     <button type="submit" class="btn btn-raised btn-primary">
                     <i class="fa fa-check-square-o"></i> Add
                     </button>
                     <button type="reset" class="btn btn-raised btn-success">
                     Reset
                     </button>
                     <button type="button" class="btn btn-raised btn-danger" data-dismiss="modal">Close
            <span aria-hidden="true">&times;</span>
            </button>
                  </center>
               </div>
            </form>
         </div>

      </div>
   </div>
</div>
@endsection
@section('js')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // Get all elements with the class name "numericInput"
const numericInputs = document.getElementsByClassName('numericInputvalue');

// Add event listener to each numeric input element
for (let i = 0; i < numericInputs.length; i++) {
    numericInputs[i].addEventListener('input', function(event) {
        let inputValue = event.target.value;
        inputValue = inputValue.replace(/[^0-9.]/g, '');
        inputValue = inputValue.replace(/(\..*)\./g, '$1');
        event.target.value = inputValue;
    });
}

 $(document).ready(function() {
        var currentDate = new Date().toISOString().split('T')[0];
        $('#patient_dob').attr('max', currentDate);
         $('#family_member_dob').attr('max', currentDate);
    });
    
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
       $('#paynowCheck').change(function() {
           var Nrow = $("#payable_amount").closest(".row");
            if ($(this).is(":checked")) {
                $('#paymentdiv').show();
                $('#addRow').show();
                $('.discount-div').show();
                $('input[name="payable_amount"]').attr('required', true);
                $('select[name="payment_mode"]').attr('required', true);
                $('select[name="deposit_to"]').attr('required', true);
                
                Nrow.find("select").prop('required', true);
                 
            } else {
                $('#paymentdiv').hide();
                $('#addRow').hide();
                $('.discount-div').hide();
                $('input[name="payable_amount"]').attr('required', false);
                $('select[name="payment_mode"]').attr('required', false);
                $('select[name="deposit_to"]').attr('required', false);
                Nrow.find("select").prop('required', false);
            }
        });
        
         
       
   });
   
   $(document).ready(function() {
   $('#branch_id').on('change', function() {
       var branchId = $(this).val();
       if (branchId) {
           $.ajax({
               url: '{{ route('consultation.getStaffs') }}',
               type: "GET",
               data: {
                   branch_id: branchId,
                   _token: '{{ csrf_token() }}'
               },
               dataType: "json",
               success: function(data) {
                   $('#staff_id').empty();
                   $('#booking_fee').val('');
                   $('#staff_id').append(
                       '<option value="">--Select Doctor--</option>');
                   $.each(data, function(key, value) {
                       $('#staff_id').append('<option value="' + value
                           .staff_id + '">' + value.staff_name +
                           '</option>');
                   });
               }
           });
       } else {
           $('#staff_id').empty();
           $('#booking_fee').val('');
           $('#staff_id').append('<option value="">--Select Doctor--</option>');
       }
   });
   //get booking fee
   
   $('#staff_id').on('change', function() {
       var staffId = $(this).val();
       var bookingDate = $('#booking_date').val();
       if (staffId && bookingDate) {
           $.ajax({
               url: '{{ route('getBookingFee') }}',
               type: "GET",
               data: {
                   staff_id: staffId,
                   booking_date: bookingDate,
                   _token: '{{ csrf_token() }}'
               },
               dataType: "json",
               success: function(data) {
                   if (data.error) {
          
                       $('#staff_id').val('');
                       $('#booking_fee').val('');
                       $('#timeslots').val();
                   } else {
                       $('#booking_fee').val(data.booking_fee);
                       $('#timeslots').empty();
                       $.each(data.timeslots, function(key, value) {
                           var optionText = value.slot_name + ' : ' + value
                               .time_from + ' - ' + value.time_to;
                           $('#timeslots').append($('<option>', {
                               value: value.timeslot,
                               text: optionText
                           }));
                       });
                   }
               }
           });
       } else {
           $('#booking_fee').val('');
       }
   });
   
   //get membership if any 
   $('#patient_id').on('change', function() {
       var patientId = $(this).val();
       if (patientId) {
           $("#patient_ids").val(patientId)
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
           
        //   $("button.showF").show();
        //   $("button.hideF").hide();
           
       } else {
           $('#membership_details').hide(); // Hide membership details div if no patient selected
        //   $("button.showF").hide();
        //   $("button.hideF").show();
       }
   });
   
   //membership based booking fee
   $('#patient_id, #staff_id').on('change', function() {
       var patientId = $('#patient_id').val();
       var staffId = $('#staff_id').val();
       if (patientId || staffId) {
           $.ajax({
               url: '{{ route('getMembershipAndBookingFee') }}',
               type: "GET",
               data: {
                   patient_id: patientId,
                   staff_id: staffId,
                   _token: '{{ csrf_token() }}'
               },
               dataType: "json",
               success: function(data) {
                   console.log("Data received:", data);
                   var payableAmount = data.payable_amount ? parseFloat(data
                       .payable_amount).toFixed(2) : '0.00';
                   console.log("Payable amount:", payableAmount);
                   $('#payable_amount').val(payableAmount);
                    $("#paid_amount").val(payableAmount);
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
   $(document).on('change', '.payment_mode', function() {
            var selectedPaymentMode = $(this).val();
            //alert(selectedPaymentMode)
            var rowNew = $(this).closest(".row");
            var dep = rowNew.find('select[name="deposit_to[]"]')
            $.ajax({
                url: '{{ route('getLedgerNames1') }}',
                type: 'GET',
                data: {
                    payment_mode: selectedPaymentMode
                },
                success: function(data) {
                    dep.empty();
                   dep.append('<option value="">Deposit To</option>');
                    $.each(data, function(key, value) {
                       dep.append('<option value="' + key + '">' +
                            value + '</option>');
                    });
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });
   });
   
   
   $(document).ready(function() {
   $('#savePatientBtn').click(function() {
       // Assuming you're using jQuery AJAX to submit the form
       $.ajax({
           url: 'your-api-endpoint-url',
           method: 'POST',
           data: $('#addPatientForm').serialize(),
           success: function(response) {
               // Handle success response
               console.log('Patient added successfully');
               // Close the modal
               $('#addPatientModal').modal('hide');
           },
           error: function(xhr, status, error) {
               // Handle error response
               console.error('Error adding patient:', error);
           }
       });
   });
   });
   
   function toggleStatus(checkbox) {
    var statusText = document.getElementById('statusText');
    if (checkbox.checked) {
        statusText.textContent = 'Active';
    } else {
        statusText.textContent = 'Inactive';
    }
}    
function toggleStatus1(checkbox) {
    var statusText = document.getElementById('statusText1');
    if (checkbox.checked) {
        statusText.textContent = 'Active';
    } else {
        statusText.textContent = 'Inactive';
    }
}
   
document.getElementById('numericInput').addEventListener('input', function(event) {
        let inputValue = event.target.value;
        inputValue = inputValue.replace(/[^0-9.]/g, '');
        inputValue = inputValue.replace(/(\..*)\./g, '$1');
        event.target.value = inputValue;
    });
    document.getElementById('numericInput1').addEventListener('input', function(event) {
        let inputValue = event.target.value;
        inputValue = inputValue.replace(/[^0-9.]/g, '');
        inputValue = inputValue.replace(/(\..*)\./g, '$1');
        event.target.value = inputValue;
    });
    
    document.getElementById('numericInput2').addEventListener('input', function(event) {
        let inputValue = event.target.value;
        inputValue = inputValue.replace(/[^0-9.]/g, '');
        inputValue = inputValue.replace(/(\..*)\./g, '$1');
        event.target.value = inputValue;
    });
    
    document.getElementById('numericInput3').addEventListener('input', function(event) {
        let inputValue = event.target.value;
        inputValue = inputValue.replace(/[^0-9.]/g, '');
        inputValue = inputValue.replace(/(\..*)\./g, '$1');
        event.target.value = inputValue;
    });
    
    function removeFn1(parm) {
             // alert("test")
            var currentRow = $(parm).closest('.row');
            // alert(currentRow)
            currentRow.remove();
             $('#sub-butn').removeClass("disabled");
        }
      $(document).ready(function() { 
        $('#addRow').click(function(){
            event.preventDefault();
            var newRow = $("#paymentdiv").clone().removeAttr("style");
            newRow.find('select').addClass('medicine-select');
            newRow.find('input').val('').prop('readonly', false);
            newRow.find('button').prop('disabled', false);
            newRow.find('input span').remove();
            newRow.find('#removebtn').removeClass("no-act");
            
            // $("#productTable tbody").append(newRow);
            newRow.insertBefore("#addRow");
        });
        
         $(document).on('change', 'input[name="payable_amount"]', function() {
            var totalBookingFee = 0;
            var noBill = false;
            
            if ($('input[name="noBill"]').prop('checked')) {
                noBill = true;
                $("#discount_amount").prop('readonly', true);
            } else {
                noBill = false;
                $("#discount_amount").prop('readonly', false);
            }
            
            // $('.product-row').each(function() {
                var bookingFee = parseFloat($("#booking_fee").val());
                totalBookingFee += bookingFee;
            // });
            
            var total = 0;
            var discount = parseFloat($("#discount_amount").val()) || 0;
            
            $('input[name="payable_amount"]').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
             var x = totalBookingFee;
            if (discount !== 0) {
                totalBookingFee = x * (1 - discount / 100);
            }
            
            // if (total !== totalBookingFee && !noBill) {
            //   // alert("Total payable amount should be " + totalBookingFee);
            //   // $(this).val('');
            //     $('#sub-butn').addClass("disabled");
            // }
            // else {
            //      $('#sub-butn').removeClass("disabled");
            // }
        });

        
      $(document).on('change', 'input[name="noBill"]', function() {
    if ($('input[name="noBill"]').prop('checked')) {
        // $("#paymentdiv").css({"display":"none"});
        $("#discount_amount").val('');
        $("#discount_amount").prop('readonly', true);
        $('input[name="payable_amount[]"]').val(0).prop('readonly', true).removeAttr('required');
         $('input[name="refernce_no[]"]').val(0).prop('readonly', true).removeAttr('required');
        $('select[name="payment_mode[]"]').val('').prop('disabled', true).removeAttr('required');
        $('select[name="deposit_to[]"]').val('').prop('disabled', true).removeAttr('required');
        $('input[name="discount_Total"]').val('').prop('readonly', true).removeAttr('required');
        $('input[name="paid_amount"]').val('').prop('readonly', true).removeAttr('required');
        $("button#removebtn").hide();
         $("button#addRow").hide();
         $('div#paymentdiv:gt(0)').remove();
    } else {
        $("#paymentdiv").css({"display":"flex"});
        //$("#discount_amount").val('');
        $("#discount_amount").prop('readonly', false);
        $('input[name="payable_amount[]"]').attr('readonly', false).attr('required', true);
         $('input[name="refernce_no[]"]').val(0).prop('readonly', false);
        $('select[name="payment_mode[]"]').prop('disabled', false).attr('required', true);
        $('input[name="deposit_to[]"]').prop('disabled', false).attr('required', true);
          $('select[name="discount_Total"]').val('').prop('readonly', false)
          $('input[name="paid_amount"]').val('').prop('readonly', false)
         $("button#removebtn").show();
          $("button#addRow").show();
    }
});

    
       
        
        
         
    });
    $(document).on('change', 'input[name="payable_amount[]"]', function() {
   var Nrow = $(this).closest(".row");
   if($(this).val()) {
       Nrow.find("select").prop('required', true);
   }
   else {
        Nrow.find("select").prop('required', false);
   }
   
});

function validateForm() {
    var isValid = true;
    var totalBookingFee = 0;
    var noBill = false;
    var pay = false
    
    if ($('input[name="noBill"]').prop('checked')) {
        noBill = true;
        $("#discount_amount").prop('readonly', true);
    } else {
        noBill = false;
    }
    
     if ($('input[name="pay_now"]').prop('checked')) {
        pay = true;
        //$("#discount_amount").prop('readonly', true);
    } else {
        pay = false;
    }
    
    // $('.product-row').each(function() {
        var bookingFee = parseFloat($("#booking_fee").val());
        totalBookingFee += bookingFee;
    // });
    
    var total = 0;
    var discount = parseFloat($("#discount_amount").val()) || 0;
    
    $('input[name="payable_amount[]"]').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
     var x = totalBookingFee;
    if (discount !== 0) {
        totalBookingFee = x * (1 - discount / 100); // Adjust total based on the discount
    }
    

    if (total !== totalBookingFee && !noBill && pay) {
     Swal.fire("Total payable amount should be " + totalBookingFee);
     return false;
    }
    //isValid = true;
    return true;
}

function openModal(button) {
   // alert(button)
    var selectedPatient = document.getElementById('patient_id').value;
    if (!selectedPatient) {
       Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Please select a patient.',
        });
         $(button).removeAttr('data-toggle').removeAttr('data-target');
    } else {
        $(button).attr('data-toggle', 'modal').attr('data-target', '#addFamilyModal');
    }
}

   $(document).ready(function() {
    // Function to update total amount
    function updateTotalAmount() {
        var paidAmount = parseFloat($("#booking_fee").val());
        var discount = parseFloat($("#discount_amount").val());
        var discountLimit = parseFloat($("#discount_amount").data("discount"));
        //var discountAmount = parseFloat($("#discount_amount").val());
        
        // Check if discount exceeds the limit
        if (!isNaN(discount) && discount > discountLimit) {
            $("#error-msg").show();
            // $("#discount_amount").val(discountLimit);
            $("#discount_amount").val(0);
            return;
        } else {
            $("#error-msg").hide();
        }

        // Calculate total amount after discount if discount is present
        var totalAmount = paidAmount;
        if (!isNaN(discount)) {
            totalAmount -= (paidAmount * (discount / 100));
            var x = paidAmount * (discount / 100);
            $("#discount_Total").val(x.toFixed(2))
        }
        else {
            $("#discount_Total").val('0');
        }

        // Update total amount field
        $("#paid_amount").val(totalAmount.toFixed(2));
        $('#payable_amount').val(totalAmount.toFixed(2));
    }

    // Set initial value of total amount to paid amount
    $("#paid_amount").val($("#consultation_fee").val());

    // Listen for changes in paid amount and discount fields
    $("#discount_amount").on("input", function() {
        updateTotalAmount();
    });
});
</script>
@endsection