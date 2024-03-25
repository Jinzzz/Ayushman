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
        select#patient_ids {
    pointer-events: none;
}
.no-act {
    pointer-events: none;
}
    </style>
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">Add Therapy Booking</h3>
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
                        <form action="{{ route('patientbooking.therapy.booking') }}"  method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Doctor Type*</label>
                                        <select class="form-control" required name="doctor_type" id="doctor_type">
                                            <option value="">--Select Type--</option>
                                            <option value="1">Internal Doctor</option>
                                            <option value="2">External Doctor</option>    
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Doctor*</label>
                                        
                                            <select class="form-control" required name="doctor_id" id="doctor_id">
                                            </select>
                                            
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="card">
                                        <div class="table-responsive">
                                            <table class="table card-table table-vcenter text-nowrap" id="productTable">
                                                <thead>
                                                    <tr>
                                                        <th>Therapy</th>
                                                        <th>Booking Fee</th>
                                                        <th>Therapy Timeslot</th>
                                                        <th>Actions</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id="productRowTemplate" class="product-row">
                                                        <td>
                                                            <select class="form-control therapy_id" required name="therapy_id[]" id="therapy_id">
                                                                <option value="">--Select Therapy--</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" id="booking_fee" class="form-control booking_fee" required
                                            name="booking_fee[]" placeholder="Booking Fee" value="" readonly>
                                                    </td>
                                                        <td><select class="form-control timeslots" required name="timeslots[]" id="timeslots">
                                                            <option value="">--Select Timeslot--</option>
                                                        </select></td>
                                                        <td><button type="button" disabled onclick="removeFn(this)"
                                                                style="background-color: #007BFF; color: #FFF; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Remove</button>
                                                        </td>
                                                        <td class="display-med-row"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="padding: 10px;">
                                    <button type="button" class="btn btn-primary" id="addProductBtn">Add Row</button>
                                </div>
                            </div>
                            <div class="row" id="therapy_details" style="display:none;">
                                <div class="col-md-12">
                                    <div class="form-group" style="border: 1px solid #000;     padding: 10px;">
                                        <h6 style="margin-left: 10px;
                                        font-weight: bold;">THERAPY INFO</h6>
                                        <span  style="margin-left: 10px;
                                        color: #0d97c6;
                                        font-weight: bold;font-size:14px;">Name: <span id="therapy_name"></span>  | Cost: <span id="therapy_cost"></span> </span>
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
                             <div class="no-pay row"  style=" display:none;">
                           
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="custom-control custom-checkbox">
                                         <input type="checkbox" value="1" name="noBill" class="custom-control-input">
                                        <span class="custom-control-label">No Bill</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                            
                            <div class="discount-div row" style=" display:none;">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Discount %</label>
                                    <input type="text" id="discount_amount" class="form-control numericInputvalue"  name="discount_amount" placeholder="Discount" value="" >
                                </div>
                            </div>
                            
                                <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Discount Amount</label>
                                    <input type="text" id="amount" class="form-control numericInputvalue"  name="discount" placeholder="Discount" value="" >
                                </div>
                            </div> 
                            
                                <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Total Amount</label>
                                    <input type="text" id="total_amount" class="form-control numericInputvalue"  name="total_amount" placeholder="Total Amount" value="" >
                                </div>
                            </div>
                        </div>
                            
                  <div class="row" id="paymentdiv" style="display: none;">
                      
                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Payable Amount*</label>
                           <input type="text" id="payable_amount" class="form-control"  name="payable_amount[]" placeholder="Payable amount" value="" >
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
                                            <button type="submit" class="btn btn-raised btn-primary">
                                                <i class="fa fa-check-square-o"></i> Add</button>
                                            <button type="reset" class="btn btn-raised btn-success">
                                                <i class="fa fa-refresh"></i> Reset</button>
                                            <a class="btn btn-danger" href="{{route('bookings.therapy.index')}}"> <i class="fa fa-times"></i>
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
                        <input type="text" class="form-control" name="patient_mobile" maxlength="10" oninput="validateInput(this)" value="{{ old('patient_mobile') }}" placeholder="Patient Mobile">
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
                        <label for="patient_gender" class="form-label">Gender*</label>
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
                        <label class="form-label">Date Of Birth*</label>
                        <input type="date" class="form-control" name="patient_dob" id="patient_dob" placeholder="Patient Dob">
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
                        <input type="text" class="form-control" name="emergency_contact" maxlength="10" oninput="validateInput(this)" placeholder="Emergency Contact">
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
                        <input type="text" class="form-control" value="{{ old('whatsapp_number') }}" maxlength="10" oninput="validateInput(this)" name="whatsapp_number" placeholder="Whatsapp Number">
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
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                  </center>
               </div>
            </form>
         </div>
         <!--<div class="modal-footer">-->
         <!--   <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
         <!--   <button type="button" class="btn btn-primary" id="savePatientBtn">Save changes</button>-->
         <!--</div>-->
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
               <div class="row">

               <div class="form-group">
    <label class="form-label">Patient*</label>
    <div class="input-group">
        <select class="form-control" name="patient_id" id="patient_ids">
            <option value="">--Select Patient--</option>
            @foreach ($patients as $id => $patient)
                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
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
                        <input type="text" class="form-control" name="family_member_phone" maxlength="100" value="{{ old('family_member_phone') }}" placeholder="Enter Phone Number">
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
                        <input type="date" class="form-control" name="family_member_dob" value = "{{ old ('family_member_dob') }}" placeholder="Date Of Birth" id="family_member_dob">
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
               <div class="form-group">
                  <center>
                     <button type="submit" class="btn btn-raised btn-primary">
                     <i class="fa fa-check-square-o"></i> Add
                     </button>
                     <button type="reset" class="btn btn-raised btn-success">
                     Reset
                     </button>
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                  </center>
               </div>
            </form>
         </div>

         <!--<div class="modal-footer">-->
         <!--   <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
         <!--   <button type="button" class="btn btn-primary">Save changes</button>-->
         <!--</div>-->
      </div>
   </div>
</div>
@endsection
@section('js')
    <script>
     function toggleStatus(checkbox) {
    var statusText = document.getElementById('statusText');
    if (checkbox.checked) {
        statusText.textContent = 'Active';
    } else {
        statusText.textContent = 'Inactive';
    }
}    
    $(document).ready(function() {
        var today = new Date();
        var maxDate = new Date(today);
        maxDate.setDate(maxDate.getDate() - 1);
        
        var formattedDate = maxDate.toISOString().split('T')[0];
        $('#patient_dob').attr('max', formattedDate);
        $('#family_member_dob').attr('max', formattedDate);
        
    });

    $('#doctor_type').change(function() {
    var doctorType = $(this).val();
    if (doctorType == 1) {
        $.ajax({
            url: '{{ route("getInternalDoctors") }}',
            type: 'GET',
            success: function(response) {
                populateInternalDoctors(response);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    } else if (doctorType == 2) {
        $.ajax({
            url: '{{ route("getExternalDoctors") }}',
            type: 'GET',
            success: function(response) {
                populateExternalDoctors(response);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    } else {
        $('#doctor_id').empty();
    }
});
function populateInternalDoctors(doctors) {
    $('#doctor_id').empty();
     $('#doctor_id').append('<option value="">Select Internal Doctors</option>');
    $.each(doctors, function(id, doctor) {
        $('#doctor_id').append('<option value="' + doctor.staff_id + '">' + doctor.staff_name + '</option>');
    });
}
function populateExternalDoctors(doctors) {
    $('#doctor_id').empty();
     $('#doctor_id').append('<option value="">Select External  Doctors</option>');
    $.each(doctors, function(id, doctor) {
        $('#doctor_id').append('<option value="' + doctor.id + '">' + doctor.doctor_name + '</option>');
    });
}

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
                    $('.discount-div').show();
                    $(".no-pay").show();
                     $('#addRow').show();
                } else {
                    $('#paymentdiv').hide();
                    $('.discount-div').hide();
                    $(".no-pay").hide();
                     $('#addRow').hide();
                }
            });
            
        });
        </script>
        <script>
            $(document).ready(function() {
                $("#addProductBtn").click(function(event) {
                    event.preventDefault();
                    var newRow = $("#productRowTemplate").clone().removeAttr("style");
                    newRow.find('select').addClass('medicine-select');
                    newRow.find('input').val('').prop('readonly', true);
                    newRow.find('button').prop('disabled', false);
                    newRow.find('input span').remove();
                    $("#productTable tbody").append(newRow);
                    attachHandlersToRow(newRow);
                });
            });

            function attachHandlersToRow(row) {
                row.find('.therapy_id').on('change', function() {
                    selectedTherapy = $(this).val();
                    $('.therapy_id').not(this).find('option[value="' + selectedTherapy + '"]').hide();
                });
            }
            function removeFn(parm) {
                    var currentRow = $(parm).closest('tr');
                    currentRow.remove();
                    var totalBookingFee = 0;
                
                $('.product-row').each(function() {
                    var bookingFee = parseFloat($(this).find('.booking_fee').val()) 
                    totalBookingFee += bookingFee;
                });
                $('#payable_amount').val(totalBookingFee.toFixed(2));
                }
        </script>

<script>
$(document).ready(function() {
    var selectedTherapy = null;
    $('#branch_id').on('change', function() {
        var branchId = $(this).val();
        if (branchId) {
            $.ajax({
                url: '{{ route('booking.getTherapy') }}',
                type: "GET",
                data: {
                    branch_id: branchId,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(data) {
                    $('.therapy_id').empty();
                    $('.booking_fee').val('');
                    $('.therapy_id').append(
                    '<option value="">--Select Therapy--</option>');
                    $.each(data, function(key, value) {
                        $('.therapy_id').append('<option value="' + value
                            .id + '">' + value.therapy_name +
                            '</option>');
                    });
                }
            });
        } else {
            $('.therapy_id').empty();
            $('.booking_fee').val('');
            $('.therapy_id').append('<option value="">--Select Therapy--</option>');
        }
    });
    $(document).on('change', '.therapy_id', function() {
        selectedTherapy = $(this).val();
        $('.therapy_id').not(this).find('option[value="' + selectedTherapy + '"]').hide();
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
                    alert(data.error);
                    $(this).val('');
                    bookingFeeInput.val('');
                    timeslotsSelect.empty();
                } else {
                    bookingFeeInput.val(data.booking_fee);
                    var payableAmount = data.booking_fee ? parseFloat(data.booking_fee).toFixed(2) : '0.00';
                       $('#payable_amount').val(payableAmount);
                       $('#total_amount').val(payableAmount);
                     if (typeof payableAmount !== 'undefined' || payableAmount > 0) {
                            //alert(payableAmount)
                            
                            //   if ($('input[name="noBill"]').prop('checked')) {
                            //   $('#payable_amount').val('0.00');
                            // } else {
                            //   calculatePayableAmount(payableAmount);
                            // }
                            calculatePayableAmount(payableAmount);
                        }
                        
                        
                    timeslotsSelect.empty().append('<option value="">--Select Timeslot--</option>');
                    $.each(data.timeslots, function(key, value) {
                        var optionText = value.therapy_room_name + ' : ' + value.time_from + ' - ' + value.time_to;
                        timeslotsSelect.append($('<option>', {
                            value: value.timeslot,
                            text: optionText
                        }));
                    });

                    if (data.therapy_name && data.therapy_cost !== null) {
                        $('#therapy_details').show();
                        $('#therapy_name').text(data.therapy_name);
                        $('#therapy_cost').text(data.therapy_cost);
                       
                    } else {
                        $('#therapy_details').hide(); 
                    }
                }
            }
        });
        calculatePayableAmount();
    } else {
        var bookingFeeInput = row.find('.booking_fee');
        var timeslotsSelect = row.find('.timeslots');

        bookingFeeInput.val('');
        timeslotsSelect.empty();
        $('#therapy_details').hide();
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
                    .hide();
                    calculatePayableAmount(0)
                    }
                }
            });
            $('#payable_amount').val('0.00');
        } else {
            $('#membership_details').hide();
           calculatePayableAmount(0)
        }

        
    });

    function calculatePayableAmount(e) {
       // alert(e)
    var totalBookingFee = 0;
    if (typeof e === 'undefined' || e === null) {
        e = 0;
    }
    $('.product-row').each(function() {
        var bookingFee = parseFloat($(this).find('.booking_fee').val()) || parseFloat(e);
        //alert($(this).find('.booking_fee').val())
        totalBookingFee += bookingFee;
    });
    if ($('#membership_details').is(':visible')) {
        $('#payable_amount').val('0.00');
    } else {
        $('#payable_amount').val(totalBookingFee.toFixed(2));
    }
}


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


           $(document).on('change', 'input[name="noBill"]', function() {
    if ($('input[name="noBill"]').prop('checked')) {
        // $("#paymentdiv").css({"display":"none"});
        $("#discount_amount").val('');
        $("#discount_amount").prop('readonly', true);
        $('input[name="payable_amount[]"]').val(0).prop('readonly', true).removeAttr('required');
         $('input[name="refernce_no[]"]').val(0).prop('readonly', true).removeAttr('required');
        $('select[name="payment_mode[]"]').val('').prop('disabled', true).removeAttr('required');
        $('select[name="deposit_to[]"]').val('').prop('disabled', true).removeAttr('required');
        
        $('input[name="discount"]').val('').prop('readonly', true).removeAttr('required');
        $('input[name="total_amount"]').val('').prop('readonly', true).removeAttr('required');
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
        $('select[name="deposit_to[]"]').prop('disabled', false).attr('required', true);
          $('input[name="discount"]').val('').prop('readonly', false)
          $('input[name="total_amount"]').val('').prop('readonly', false)
         $("button#removebtn").show();
          $("button#addRow").show();
    }
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
        
 function removeFn1(parm) {
             // alert("test")
            var currentRow = $(parm).closest('.row');
            // alert(currentRow)
            currentRow.remove();
             $('#sub-butn').removeClass("disabled");
        }
function validateForm() {
    var isValid = true;
    var totalBookingFee = 0;
    var noBill = false;
    var payNow = false;
    
    if ($('input[name="noBill"]').prop('checked')) {
        noBill = true;
        $("#discount_amount").prop('readonly', true);
    } else {
        noBill = false;
    }
     if ($('input[name="pay_now"]').prop('checked')) {
        payNow = true;
        //$("#discount_amount").prop('readonly', true);
    } else {
        payNow = false;
    }
    
    $('.product-row').each(function() {
        var bookingFee = parseFloat($(this).find('.booking_fee').val());
        totalBookingFee += bookingFee;
    });
    
    var total = 0;
    var discount = parseFloat($("#discount_amount").val()) || 0;
    
    $('input[name="payable_amount[]"]').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
     var x = totalBookingFee;
    if (discount !== 0) {
        totalBookingFee = x * (1 - discount / 100); // Adjust total based on the discount
    }
    
    if (total !== totalBookingFee && !noBill && payNow) {
        alert("Total payable amount should be " + totalBookingFee);
        isValid = false;
    }
    // else {
    //     alert("test")
    // }
    
    return isValid;
}

   $(document).ready(function() {
    // Function to update total amount
    function updateTotalAmount() {
       // var paidAmount = parseFloat($("#booking_fee").val());
        //alert("test")
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
        
        
        var totalBookingFee = 0;
        
        $('.product-row').each(function() {
           var bookingFee = parseFloat($(this).find('input[name="booking_fee[]"]').val());
            totalBookingFee += bookingFee;
        });
    
   var totalAmount = totalBookingFee;
        if (!isNaN(discount)) {
            totalAmount -= (totalBookingFee * (discount / 100));
            var x = totalBookingFee * (discount / 100);
            $("#amount").val(x)
        }

        // Update total amount field
        $("#total_amount").val(totalAmount.toFixed(2));
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
