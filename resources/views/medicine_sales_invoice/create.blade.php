@extends('layouts.app')
@section('content')
@php
use App\Helpers\AdminHelper;
use App\Models\Mst_Staff;
// dd(AdminHelper::getProductId($value->medicine_code));
@endphp

<style>
   .table th {   font-size: 12px;} select.medsearch { display: none !important; } span.current {
    font-size: 10px!important; } .table td {padding: 5px 3px;} .pricecard .form-group label {font-size: 12px;margin:0;} .pricecard .form-group span, .pricecard .form-group input {width: auto;padding: 0;border: unset;line-height: 1;height:auto;} .pricecard .form-group input:focus {outline: unset !important;border: none !important;} .pricecard .form-group {display: flex;align-items: center;gap: 16px;margin: 5px 0;}.pricecard .col-md-4 {margin-left: auto;}.dropdown-select {background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 100%);background-repeat: repeat-x;filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#40FFFFFF', endColorstr='#00FFFFFF', GradientType=0);background-color: #fff;border-radius: 6px;box-sizing: border-box;cursor: pointer;display: block;float: left;font-size: 14px;font-weight: normal;outline: none;padding-left: 18px;padding-right: 30px;position: relative;text-align: left !important;transition: all 0.2s ease-in-out;-webkit-user-select: none;-moz-user-select: none; -ms-user-select: none;user-select: none;white-space: nowrap; width: auto;}.dropdown-select:focus {background-color: #fff;}.dropdown-select:hover {background-color: #fff;}.dropdown-select:active,.dropdown-select.open {background-color: #fff !important;border-color: #bbb;box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) inset;}.dropdown-select:after {height: 0; width: 0; border-left: 4px solid transparent; border-right: 4px solid transparent; border-top: 4px solid #777;-webkit-transform: origin(50% 20%); transform: origin(50% 20%); transition: all 0.125s ease-in-out; content: ''; display: block; margin-top: -2px; pointer-events: none; position: absolute; right: 10px; top: 50%;}.dropdown-select.open:after { -webkit-transform: rotate(-180deg); transform: rotate(-180deg);}.dropdown-select.open .list { -webkit-transform: scale(1); transform: scale(1); opacity: 1; pointer-events: auto;}.dropdown-select.open .option {cursor: pointer;}.dropdown-select.wide {width: 100%;}.dropdown-select.wide .list {left: 0 !important;right: 0 !important;}.dropdown-select .list {box-sizing: border-box; transition: all 0.15s cubic-bezier(0.25, 0, 0.25, 1.75), opacity 0.1s linear; -webkit-transform: scale(0.75); transform: scale(0.75); -webkit-transform-origin: 50% 0; transform-origin: 50% 0; box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.09); background-color: #fff; border-radius: 6px; margin-top: 4px; padding: 3px 0; opacity: 0; overflow: hidden; pointer-events: none; position: absolute; top: 100%; left: 0; z-index: 999; max-height: 250px; overflow: auto; border: 1px solid #ddd;}.dropdown-select .list:hover .option:not(:hover) { background-color: transparent !important;}.dropdown-select .dd-search{overflow:hidden;display:flex;align-items:center;justify-content:center;margin:5px 0;}.dropdown-select .dd-searchbox{width:90%;padding:0.5rem;border:1px solid #999;border-color:#999;border-radius:4px;outline:none;line-height: 1;}.dropdown-select .dd-searchbox:focus{border-color:#12CBC4;}.dropdown-select .list ul { padding: 0;}.dropdown-select .option {cursor: default; font-weight: 400; line-height: 2; outline: none; padding-left: 10px; padding-right: 25px; text-align: left; transition: all 0.2s; list-style: none; font-size: 10px;}.dropdown-select .option:hover,.dropdown-select .option:focus {background-color: #f6f6f6 !important;}.dropdown-select .option.selected { font-weight: 600; color: #12cbc4;}.dropdown-select .option.selected:focus {background: #f6f6f6;}.dropdown-select a {color: #aaa; text-decoration: none; transition: all 0.2s ease-in-out;}.dropdown-select a:hover {
    color: #666;}

   /* Custom CSS for ash color border */
   .card-ash-border {
      border: 1px solid #b0b0b0;
      /* Adjust the color as needed */
   }

   .breadcrumb {
      margin-top: 1rem;
      !important;
      background-color: #fff !important;
   }

   td.medicine-quantity span {
      color: red;
      font-size: 10px;
      position: absolute;
      bottom: -1px;
      left: 00;
      text-align: center;
      width: 100%;
   }

   td.medicine-quantity {
      position: relative;
   }

button.disable {
    pointer-events: none;
}
   .display-med-row {
      display: none;
   }

   .alert-div {
      position: absolute;
      top: 50%;
      left: 50%;
      margin-right: -50%;
      transform: translate(-50%, -50%);
      background: #c40e0e;
      padding: 25px;
      color: white;
      width: 370px;
      height: 200px;
      z-index: 999;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 5px;
   }
   
    .form-control:disabled, .form-control[readonly] {
    background-color: #c7c7c7;
      }
      .list li.option[disabled] {
    display: none;
}
select.form-control.medsearch.errorSelect + .dropdown-select {
    border-color: red !important;
}
.dropdown-select .list ul {
    padding: 0;
    height: 50px;
    overflow: auto;
}
</style>
<div class="row" style="min-height: 70vh;">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header">
            <h3 class="mb-0 card-title">{{ $pageTitle }}</h3>
         </div>
         <div class="card-body">
            @if ($message = Session::get('status'))
            <div class="alert alert-success">
               <p>{{ $message }}</p>
            </div>
            @endif
            @if ($message = Session::get('error'))
            <div class="alert alert-danger">
               <p>{{ $message }}</p>
            </div>
            @endif
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
            <!-- Modal -->
            <div class="modal fade" id="addPatientModal" tabindex="-1" role="dialog" aria-labelledby="addPatientModalLabel" aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="addPatientModalLabel">Add New Patient</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                     <div class="modal-body">
                        <!-- Add your form or content for adding a new patient here -->
                        <!-- Example: -->
                        <form action="{{ route('medicine.sales.invoices.patient-store') }}" method="POST" enctype="multipart/form-data">
                           @csrf
                           <div class="row">
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <label class="form-label">Patient Name*</label>
                                    <input type="text" class="form-control" required name="patient_name" maxlength="100" value="{{ old('patient_name') }}" placeholder="Patient Name">
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
                                    <input type="text" class="form-control numericInput" required name="patient_mobile" maxlength="10" value="{{ old('patient_mobile') }}" placeholder="Patient Mobile" pattern="\d+(\.\d{0,2})?">
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
                                       @foreach($gender as $id => $gender)
                                       <option value="{{ $id }}">{{ $gender }}</option>
                                       @endforeach
                                    </select>
                                 </div>
                              </div>
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <label class="form-label">Date Of Birth</label>
                                    <input type="date" class="form-control" name="patient_dob" placeholder="Patient Dob">
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
                                    <input type="text" class="form-control" name="emergency_contact_person" maxlength="100" placeholder="Emergency Contact Person">
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <label class="form-label">Emergency Contact</label>
                                    <input type="text" class="form-control numericInput" name="emergency_contact" maxlength="10" pattern="\d+(\.\d{0,2})?" placeholder="Emergency Contact">
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
                                    <select class="form-control" name="patient_registration_type" id="patient_registration_type" required>
                                       <option value="self" selected>Self</option>
                                    </select>
                                 </div>
                              </div>

                              <div class="col-md-6">
                                 <div class="form-group">
                                    <label class="form-label">Whatsapp Number</label>
                                    <input type="text" class="form-control numericInput" value="{{ old('whatsapp_number') }}" maxlength="10" pattern="\d+(\.\d{0,2})?" name="whatsapp_number" placeholder="Whatsapp Number">
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
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <div class="form-label">Has Credit</div>
                                    <label class="custom-switch">
                                       <input type="hidden" name="has_credit" value="0">
                                       <input type="checkbox" id="has_credit" name="has_credit" onchange="toggleStatus1(this)" class="custom-switch-input" checked>
                                       <span id="statusLabel1" class="custom-switch-indicator"></span>
                                       <span id="statusText1" class="custom-switch-description">Active</span>
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
                                 <a class="btn btn-danger" class="close" data-dismiss="modal" aria-label="Close">Cancel</a>
                              </center>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
            <!-- Modal -->

            <form action="{{ route('medicine.sales.invoices.store') }}" method="POST" id="salesInvForm" enctype="multipart/form-data" onsubmit="return validateForm()">
               @csrf
               <!-- <input type="hidden" name="discount_percentage" value="3" id="discount_percentage"> -->

               <div class="row">
                  <div class="col-md-3">

                     <div class="form-group">
                        <label class="form-label">Select Patient*</label>
                        <div class="input-group">
                           <select class="form-control" name="patient_id" id="patient_id" required>
                              <option value="">Select Patient</option>
                              <option value="0">Guest Patient</option>
                              @foreach ($patients as $patient)
                              <option value="{{ $patient->id }}">
                                 {{ $patient->patient_name }} ({{ $patient->patient_code }})
                              </option>
                              @endforeach
                           </select>
                           <span class="input-group-append">
                              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPatientModal">+</button>
                           </span>
                        </div>
                     </div>

                  </div>

                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="form-label">Select Booking ID</label>
                        <select class="form-control" name="patient_booking_id" id="patient_booking_id">
                           <option value="">Choose Booking ID</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" readonly name="due_date" id="date" placeholder="Date">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="form-label">Pharmacy*</label>
                         @if(Auth::check() && Auth::user()->user_type_id == 96)
                           @php
                            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
                            $mappedpharma = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
                           @endphp
                           
                               <select class="form-control" name="pharmacy_id" id="pharmacy_id" required>
                                   <option value="">Select Pharmacy</option>
                                   @foreach ($pharmacies as $pharmacy)
                                       @if(in_array($pharmacy->id, $mappedpharma))
                                           <option value="{{ $pharmacy->id }}" @if(old('pharmacy_id') == $pharmacy->id) selected @endif>{{ $pharmacy->pharmacy_name }}</option>
                                       @endif
                                   @endforeach
                               </select>
                          
                       @else
                       @if(session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all")
                               <select class="form-control activeelement" name="pharmacy_id" id="pharmacy_id" readonly>
                                   <option value="{{ session('pharmacy_id') }}" selected>{{ session('pharmacy_name') }}</option>
                               </select>
                           @else
                           <select class="form-control" name="pharmacy_id" id="pharmacy_id" required>
                               <option value="">Select Pharmacy</option>
                               @foreach ($pharmacies as $pharmacy)
                                   <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
                               @endforeach
                           </select>
                           @endif
                       @endif
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12 col-lg-12">
                     <div class="card">
                        <div class="table-responsive">
                           <table class="table card-table table-vcenter text-nowrap" id="productTable" style="min-height: 200px;">
                              <thead>
                                 <tr>
                                    <th>Medicine Name</th>
                                    <th>Batch No</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Rate</th>
                                    <th>Tax</th>
                                    <th>Tax Amount</th>
                                    <th>Amount</th>
                                    <th>Actions</th>

                                 </tr>
                              </thead>
                              <tbody>
                                 <tr id="productRowTemplate" style="display: none">
                                    <td>
                                       <select class="form-control medicine-name medsearch" name="medicine_id[]">
                                          <option value="" selected disabled>Please select medicine</option>
                                          @foreach($medicines as $medicine)
                                          <option value="{{ $medicine->id }}">{{ $medicine->medicine_name}}</option> @endforeach
                                       </select>
                                    </td>
                                    <td class="medicine-batch-no"><input type="text" class="form-control" value="" name="batch_no[]" readonly></td>
                                    <td class="medicine-quantity"><input type="number" min="1" class="form-control" value="" name="quantity[]" oninput="calculateAmount(this)"></td>
                                    <td class="medicine-unit-id"><input type="text" class="form-control" value="" name="unit_id[]" readonly></td>
                                    <td class="medicine-rate"><input type="text" class="form-control" value="" name="rate[]" readonly></td>
                                    
                                     <td class="medicine-tax"><input type="text" class="form-control" value="" name="tax[]" readonly></td>
                                      <td class="medicine-tax-amount"><input type="text" class="form-control" value="" name="tax_amount[]" readonly></td>
                                      
                                    <td class="medicine-amount"><input type="text" class="form-control" value="" name="amount[]" readonly></td>
                                    <td><button type="button" onclick="removeFn(this)" style="background-color: #007BFF; color: #FFF; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Remove</button></td>
                                    <td class="display-med-row medicine-stock-id"><input type="hidden" class="form-control" name="med_stock_id[]" readonly></td>
                                    <td class="display-med-row medicine-current-stock"><input type="hidden" class="form-control" name="current-stock[]" readonly></td>
                                    <td class="display-med-row medicine-reorder-limit"><input type="hidden" class="form-control" name="limit[]" readonly></td>
                                    <td class="display-med-row medicine-tax-rate"><input type="hidden" class="form-control" name="tax_rate[]"></td>
                                    <td class="display-med-row medicine-tax-amount"><input type="hidden" class="form-control" name="single_tax_amount[]" readonly></td>
                                    <td class="display-med-row medicine-mfd"><input type="hidden" class="form-control" name="mfd[]" readonly></td>
                                    <td class="display-med-row medicine-expd"><input type="hidden" class="form-control" name="expd[]" readonly></td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- popup starts  -->
               <!-- Modal for displaying medicine batch details -->
               <div class="modal" id="medicineBatchModal" tabindex="-1" role="dialog" aria-labelledby="medicineBatchModalLabel" aria-hidden="true" data-backdrop="static">
                  <div class="modal-dialog" role="document" style="max-width: 90%; ">
                     <div class="modal-content">
                        <div class="modal-header">
                           <h5 class="modal-title" id="medicineBatchModalLabel">Medicine Batch Details</h5>
                           <button type="button" class="close modal-close no-selected-item" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                           </button>
                        </div>
                        <div class="modal-body">
                           <!-- Display medicine batch details here -->
                           <table class="table">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Batch Number</th>
                                    <th>Type</th>
                                    <th>MFD</th>
                                    <th>EXPD</th>
                                    <th>Current Stock</th>
                                    <th>Unit</th>
                                    <th>Sales Price</th>
                                    <th>Tax Rate</th>
                                    <th>Select</th>
                                 </tr>
                              </thead>
                              <tbody id="medicineBatchDetails">
                                 <!-- Data will be displayed here -->
                              </tbody>
                           </table>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" id="close-modal" data-dismiss="modal">Select</button>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- popup ends  -->
               <div class="row">
                  <div class="col-md-12">
                     <button type="button" class="btn btn-primary" id="addProductBtn">Add Medicine</button>
                  </div>
               </div>
               <!-- ROW-1 CLOSED -->
               <div class="row">
                  <div class="col-md-6">
                     <!-- Left Div - terms_condition -->
                     <div id="terms_condition" class="custom-margin">
                        <div class="form-group">
                           <label class="form-label">Notes:</label>
                           <textarea class="form-control" name="notes" placeholder="Notes"></textarea>
                        </div>
                        <div class="form-group">
                           <label class="form-label">Terms and Conditions:</label>
                           <textarea class="form-control" name="terms_condition" placeholder="Terms and Conditions"></textarea>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <!-- Right Div - discount_amount -->
                     <div id="discount_amount" class="custom-margin">
                        <div class="card card-ash-border">
                           <div class="card-body">
                              <table style="width: 100%;">
                                 <tr>
                                    <td><strong>Sub Total</strong></td>
                                    <td style="text-align: right;"><strong class="tot">0</strong><input type="hidden" id="sub-total-input" name="sub_total_amount" value="0"></td>
                                 </tr>
                                 <tr>
                                    <td><strong>Tax Amount</strong></td>
                                    <td style="text-align: right;"><strong class="tax-amount">0</strong><input type="hidden" id="tax-amount-input" name="total_tax_amount" value="0"></td>
                                 </tr>
                                 <tr>
                                    <td><strong>Total Amount</strong></td>
                                    <td style="text-align: right;"><strong class="total-amount">0</strong><input type="hidden" id="total-amount-input" name="total_amount" value="0"></td>
                                 </tr>
                                 <tr>
                                    <td><strong>Discount Percentage</strong></td>
                                    <label class="form-label">Maximum Discount: {{ $discount_percentage}}%</label>
                                    <td style="text-align: right;"><strong class="discount-amount"></strong><input type="text" class="form-control" id="discount-amount-input" name="discount_amount" placeholder="0"></td>
                                 </tr>
                              </table>
                              <hr>
                              <div class="form-group mb-2">
                                 <label class="form-label payable-amount">Payable Amount : <b>0</b></label>
                                 <input type="hidden" value="" name="payable_hidden" id="pay1">
                              </div>

                              <div class="form-group mb-2">
                                 <label class="form-label payable-amount balance-payable">Balance Credit Amount : <b>0</b></label>
                              </div>

                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- payment   -->
               <div id="payment_fields">
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">Paid Amount</label>
                           <input type="text" class="form-control paid-amount numericInput" name="paid_amount[]" maxlength="16" value="" required placeholder="Paid Amount" pattern="\d+(\.\d{0,2})?" onkeyup="validateAmount(this)">

                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label for="payment-type" class="form-label">Payment Mode</label>
                           <select class="form-control payment_mode" required name="payment_mode[]" placeholder="Payment Mode">
                              <option value="">--Select--</option>
                              @foreach($paymentType as $id => $value)
                              <option value="{{ $id }}">{{ $value }}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">Deposit To</label>
                           <select class="form-control deposit_to" name="deposit_to[]">
                              <option value="">Deposit To</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">Action</label>
                           <button type="button" class="btn btn-raised btn-danger removebtn" id="removebtn"> Remove </button>
                        </div>
                     </div>

                  </div>
               </div>
               <button type="button" class="btn btn-raised btn-primary" id="addRow"> Add New Row </button>
               <div class="form-group">
                  <center>
                     <button type="submit" id="saveFormBtn" class="btn btn-raised btn-primary">
                        <i class="fa fa-check-square-o"></i> Save</button>
                     <a class="btn btn-danger" href="{{ url('/medicine-sales-invoices') }}">Cancel</a>
                  </center>
               </div>
         </div>
      </div>
   </div>
   </form>
</div>
</div>
</div>
</div>
</div>
</div>

@endsection
@section('js')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<!--<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>
   // total amount 
   // Get the current date
   var currentDate = new Date();
   // Format the date as "YYYY-MM-DD" (required by input type="date)
   var formattedDate = currentDate.toISOString().split('T')[0];
   // Set the value of the input field to today's date
   document.getElementById("date").value = formattedDate;

   $(document).ready(function() {

      $(document).on("click", ".removebtn", function() {
         const rowCount = $("#payment_fields").find(".row").length;
         if(rowCount == 1) return;
         $(this).closest(".row").remove()
         updateBalAmount()
      });

      $(document).on("input", ".paid-amount", function(e) {
         updateBalAmount($(this))
      });



      $("#addRow").click(function() {
         const row = $("#payment_fields").find(".row").first().clone()
         row.find(".paid-amount").val("")
         row.find(".paid-amount").on('input', function(event) {
            let inputValue = event.target.value;
            inputValue = inputValue.replace(/[^0-9.]/g, '');
            inputValue = inputValue.replace(/(\..*)\./g, '$1');
            event.target.value = inputValue;
        });
         $("#payment_fields").append(row);
      })


      function updateTotalAmount() {
         const subTotal = parseFloat($('.tot').val()) || 0;
         const taxAmount = parseFloat($('.tax-amount').val()) || 0;
         const totalAmount = subTotal + taxAmount;
         $('.total-amount').text('' + totalAmount);
         $('#sub-total-input').val(subTotal);
         $('#tax-amount-input').val(taxAmount);
         $('#total-amount-input').val(totalAmount);
      }

      // Listen for changes in the Sub Total and Tax Amount input fields
      $('#sub-total, #tax-amount').on('input', updateTotalAmount);

      // Initial update of the Total Amount
      updateTotalAmount();


      // searchable dropdown
      $('#patient_id').select2(); // Initialize Select2 for the patient dropdown
      $('#patient_booking_id').select2();
      // Handle change event on the payment mode dropdown
      $(document).on("change", ".payment_mode", function(e) {
         var selectedPaymentMode = $(this).val();
         const elem = $(this)
         // alert(selectedPaymentMode);
         // Make an AJAX request to fetch the ledger names based on the selected payment mode
         $.ajax({
            url: '{{ route("getLedgerNames1") }}',
            type: 'GET',
            data: {
               payment_mode: selectedPaymentMode
            },
            success: function(data) {

               console.log($(this).closest('.row'), "hhhh");
               // Clear existing options
               elem.closest('.row').find(".deposit_to").empty()
               // $('#deposit_to').empty();

               // Add default option
               elem.closest('.row').find(".deposit_to").append('<option value="">Deposit To</option>');

               // Add options based on the response
               $.each(data, function(key, value) {
                  elem.closest('.row').find(".deposit_to").append('<option value="' + key + '">' + value + '</option>');
               });
            },
            error: function(error) {
               console.log(error);
            }
         });
      });
      
       
     $(document).on('change', "#pharmacy_id", function () {
          // alert("test")
           var x = $(this).val();
           if(x) {
               $(this).addClass("activeelement")
           }
       });   
        
     /* $("#saveFormBtn").click(function(e) {
         e.preventDefault();
        
        if(!$("#pharmacy_id").hasClass('activeelement')) {
           // alert("Please select a pharmacy.");
            selectedPharmacy = $(".activeelement").val();
            //alert(selectedPharmacy)
            if(!selectedPharmacy) {
                alert("Please select a pharmacy.");
            }
        }
    
         let formIsValid = true;
         $("#payment_fields").find(".row").each(function(row, elem) {
            const paidAmount = $(elem).find(".paid-amount").val();
            console.log(paidAmount,"paidAmount");
            const paymentModeVal = $(elem).find(".payment_mode").val();
            const depositToVal = $(elem).find(".deposit_to").val();
            // if(!paidAmount){
            //    alert('Please enter paid amount.')
            //    return;
            // }
            if (!paidAmount || !paymentModeVal || !depositToVal) {
               $(elem).find(".required-msg").remove(); // Remove existing message
               if (!paidAmount) {
                  $(elem).find(".paid-amount").after("<span class='required-msg' style='color:red;'>Please enter paid amount</span>");
               }
               if (!paymentModeVal) {
                  $(elem).find(".payment_mode").after("<span class='required-msg' style='color:red;'>Please select a payment mode</span>");
               }
               if (!depositToVal) {
                  $(elem).find(".deposit_to").after("<span class='required-msg' style='color:red;'>Please select a deposit option</span>");
               }
               formIsValid = false;
            } else {
               $(elem).find(".required-msg").remove(); // Remove existing message
            }
         });
         
         var totalPaidAmount = 0;
            // $('input[name="paid_amount[]"]').each(function() {
            //     var value = parseFloat($(this).val());
            //     if (!isNaN(value)) {
            //         totalPaidAmount += value;
            //     }
            // });
            // var payableAmount = parseInt($('.payable-amount b').text());
            // console.log(totalPaidAmount)
            var balanceAmount = parseFloat($('.balance-payable b').text());
            if(balanceAmount > 0) {
                 Swal.fire("Oops!", "There is a pending amount of " + balanceAmount.toFixed(2) + " to be paid.", "error");
            }

         if (formIsValid && balanceAmount==0) {
             
            $("#salesInvForm").submit();
         }
         
         
      });*/
      
      $("#saveFormBtn").click(function(e) {
    e.preventDefault();

    // Check if the pharmacy is selected
    if (!$("#pharmacy_id").hasClass('activeelement')) {
        selectedPharmacy = $(".activeelement").val();
        if (!selectedPharmacy) {
            alert("Please select a pharmacy.");
            return;
        }
    }

    // Check validity of payment fields
    let formIsValid = true;
    $("#payment_fields").find(".row").each(function(row, elem) {
        const paidAmount = $(elem).find(".paid-amount").val();
        const paymentModeVal = $(elem).find(".payment_mode").val();
        const depositToVal = $(elem).find(".deposit_to").val();

        $(elem).find(".required-msg").remove(); // Remove existing messages

        if (!paidAmount || !paymentModeVal || !depositToVal) {
            if (!paidAmount) {
                $(elem).find(".paid-amount").after("<span class='required-msg' style='color:red;'>Please enter paid amount</span>");
            }
            if (!paymentModeVal) {
                $(elem).find(".payment_mode").after("<span class='required-msg' style='color:red;'>Please select a payment mode</span>");
            }
            if (!depositToVal) {
                $(elem).find(".deposit_to").after("<span class='required-msg' style='color:red;'>Please select a deposit option</span>");
            }
            formIsValid = false;
        }
    });

    // Check balance amount
   

    // AJAX call to check credit
    var patient_id = $('#patient_id').val();
    $.ajax({
        url: '/check-has-credit',
        method: 'GET',
        data: { patient_id: patient_id },
        success: function(response) {
            // Handle the response
            if (response == 1) {
                // If the patient has credit, directly submit the form
                $("#salesInvForm").submit();
            } else {
                // If the patient doesn't have credit, continue with other checks
                 var balanceAmount = parseFloat($('.balance-payable b').text());
                if (balanceAmount > 0) {
                    Swal.fire("Oops!", "There is a pending amount of " + balanceAmount.toFixed(2) + " to be paid.", "error");
                    return;
                }
                if (formIsValid) {
                    $("#salesInvForm").submit();
                }
            }
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});






      function updateBalAmount(elem=null) {
         let totalPaid = 0
         $(".paid-amount").each(function() {
            totalPaid += $(this).val()?parseFloat($(this).val()):0
         })

         console.log(totalPaid, "iiiiiiiii");

         // return;

         const payableAmount = parseFloat($(".payable-amount b").first().text())
         // if (!value) {
         //    $(".balance-payable b").text(" " + payableAmount)
         //    return;
         // }
         if (totalPaid > payableAmount) {
            Swal.fire("Oops!", "The total paid amount cannot exceed the payable amount", "error");
            if(elem){
               $(elem).val("")
               updateBalAmount()
            }
            // document.getElementById("salesInvForm").reset();
         return;
      }
         const balancePayable = parseFloat($(".balance-payable b").text())
         console.log(payableAmount, balancePayable,'oooooooooooooooooo');
         const upadatedBal = payableAmount - parseFloat(totalPaid)
         $(".balance-payable b").text(" " + upadatedBal)
      }

      $('input[readonly][data-medicine-code]').each(function() {
         var input = $(this);
         var medicineCode = input.data('medicine-code');

         // Fetch product_id and unit_id
         var productUrl = '/get-product-id/' + encodeURIComponent(medicineCode);
         var unitUrl = '/get-unit-id/' + encodeURIComponent(medicineCode);

         $.get(productUrl, function(productResponse) {
            // Set the value of the corresponding input field in the same row
            input.closest('tr').find('input[name="product_id[]"]').val(productResponse.product_id);
         });

         $.get(unitUrl, function(unitResponse) {
            // Set the value of the corresponding input field in the same row
            input.closest('tr').find('input[name="unit_id[]"]').val(unitResponse.unit_id);
         });
      });

      $('select[name="product_id[]"]').each(function() {
         var select = $(this);

         // Fetch product IDs based on medicine code
         var medicineCode = select.closest('tr').find('input[name="medicine_code[]"]').val();
         var productUrl = '/get-product-id/' + encodeURIComponent(medicineCode);

         $.get(productUrl, function(productResponse) {
            // Populate the dropdown with product IDs
            productResponse.forEach(function(product) {
               var option = $('<option>', {
                  value: product.product_id,
                  text: product.product_id
               });

               select.append(option);
            });
         });
      });

      calculateTotals();

      $("#productTable tbody").on("input", 'input[name="amount[]"], input[name="discount[]"]', function() {
         calculateTotals();
      });
   });
   $(document).ready(function() {
      $("#addProductBtn").click(function(event) {

         event.preventDefault();
         // Clone the product row template
         var newRow = $("#productRowTemplate").clone();
         // Remove the "style" attribute to make the row visible
         newRow.removeAttr("style");
         newRow.find('select').addClass('medicine-select');
         newRow.find('input[type="text"]').val('');
         newRow.find('input[type="number"]').val('');
         newRow.find('.medicine-quantity input').prop("readonly", true);
         newRow.find('input').removeAttr("disabled")
         $('.medicine-quantity input').prop('readonly', true);
         // newRow.removeAttr('style')
         newRow.find('input span').remove()
         // Append the new row to the table
         $("#productTable tbody").append(newRow);
      });

   });

   function validateForm() {
      var isValid = true;

      // Loop through each row in the table
      $("#productTable tbody tr").each(function() {
         // Get the values from the input fields in the current row
         var calculatedAmount = parseFloat($(this).find('input[name="quantity[]"]').val() * $(this).find('input[name="rate[]"]').val()) - (parseFloat($(this).find('input[name="discount[]"]').val()) + parseFloat($(this).find('input[name="tax[]"]').val()));
         var amount = parseFloat($(this).find('input[name="amount[]"]').val()) || 0;

         // Compare the calculated amount with the entered amount
         var epsilon = 0.0001; // A small positive number to account for floating-point precision
         if (Math.abs(calculatedAmount - amount) > epsilon) {
            alert('Invalid Amount in row ' + ($(this).index() + 1));
            isValid = false;
            return false; // Exit the loop early if an invalid amount is found
         }
      });

      return isValid;
   }

   function calculateTotals() {
      var subTotal = 0;
      var itemDiscount = 0;
      var billDiscount = parseFloat($("#bill_discount").val()) || 0;
      var totalTax = 0;
      var roundOff = parseFloat($("#round_off").val()) || 0;

      // Loop through each row in the table
      $("#productTable tbody tr").each(function() {
         // Get the value from the "Amount" input field in the current row
         var amount = parseFloat($(this).find('input[name="amount[]"]').val()) || 0;


         subTotal += amount;

         // Get the value from the "Discount" input field in the current row
         var discount = parseFloat($(this).find('input[name="discount[]"]').val()) || 0;


         itemDiscount += discount;

         var tax = parseFloat($(this).find('input[name="tax[]"]').val()) || 0;

         totalTax += tax;
      });

      // Update the "Sub Total" input field with the calculated subtotal
      $("#sub_total").val(subTotal.toFixed(2));

      // Update the "Item-wise Discount" input field with the calculated item discount
      $("#item_discount").val(itemDiscount.toFixed(2));

      //tax field
      $("#total_tax").val(totalTax.toFixed(2));

      var total = subTotal - itemDiscount + billDiscount + totalTax + roundOff;

      // Update the "Total" input field with the calculated total
      $("#total").val(total.toFixed(2));
   }


   $('#patient_id').on('change', function() {
      var selected_patient_id = $(this).val();

      $.ajax({
         url: "{{ route('get.patient.booking.ids', '') }}/" + selected_patient_id,
         method: "patch",
         data: {
            _token: "{{ csrf_token() }}",
         },
         success: function(data) {
            // alert(1);
            $('#patient_booking_id').empty().append('<option value="">Choose Booking ID</option>');
            $.each(data, function(key, value) {

               $('#patient_booking_id').append('<option value="' + key + '">' + value + '</option>');
            });
         },
         error: function() {
            console.log('Error fetching account sub groups.');
         }
      });

   });

   function toggleStatus(checkbox) {
      if (checkbox.checked) {
         $("#statusText").text('Active');
         $("input[name=is_active]").val(1);
      } else {
         $("#statusText").text('Inactive');
         $("input[name=is_active]").val(0);
      }
   }
   function toggleStatus1(checkbox) {
      if (checkbox.checked) {
         $("#statusText1").text('Active');
      } else {
         $("#statusText1").text('Inactive');
      }
   }
 $(document).ready(function() {
   $(document).on('change', '.medicine-select', function() {


      // $("#medicineBatchModal").show()
      var selected_medicine_id = $(this).val();
      var selct = $(this);
      $(".selectedCls").removeClass("selectedCls")
      $(this).parents('tr').addClass("selectedCls");

        var field1 = $(this).parents('tr').find(".medicine-batch-no input");
        selectedPharmacy = $(".activeelement").val();
      $.ajax({
         url: "{{ route('get.medicine.batches', '') }}/" + selected_medicine_id,
         method: "patch",
         data: {
            _token: "{{ csrf_token() }}",
            pharmacy_id: selectedPharmacy,
         },
         success: function(response) {
            console.log('response', response);
            // Clear previous data in the modal
            $('#medicineBatchDetails').empty();

            // Populate the modal with the received data
            response.data.forEach(function(item, index) {
               //alert(item.id)
               var row = `<tr>
                    <td class="batch-index">${index+1}</td>
                    <td style="display:none" class="medicine-stock-id">${item.id}</td>
                    <td class="batch-medicine-batch-number">${item.medicine_batch_number}</td>
                    <td class="batch-medicine-type">${item.medicine_type}</td>
                    <td class="batch-medicine-mfd">${item.medicine_mfd}</td>
                    <td class="batch-medicine-expd">${item.medicine_expd}</td>
                    <td class="batch-current-stock">${item.medicine_current_stock}</td>
                    <td class="batch-medicine-unit">${item.medicine_unit}</td>
                    <td class="batch-medicine-unit-price">${item.medicine_unit_price}</td>
                    <td class="batch-medicine-tax-rate">${item.medicine_tax_rate}</td>
                    <td class="radio-batch-btn"><input type="radio" value="${item.id}" name="selected_batch"></td>
                </tr>`;
               $('#medicineBatchDetails').append(row);
            });
            // Show the modal
            $('#medicineBatchModal').modal('show');

         },
         error: function() {
            console.log(0);
            console.log('Error fetching medicine batches.');
         }
      });



      $(document).on('change', '.radio-batch-btn', function() {
         // $(document).on('click', '.modal-close', function() {

         //var selectedValue = $("input[name='selected_batch']:checked")
         //select.closest(".medicine-batch-no").find("input").val()
         var selectedValue = $("input[name='selected_batch']:checked")
         var id = selectedValue.closest('tr').find('.medicine-stock-id').text();
         var stock = 0
         // alert(id)


         $(".selectedCls").find(".medicine-stock-id input").val(id)

         var v1 = selectedValue.closest('tr').find('.batch-medicine-batch-number').text();
         $(".selectedCls").find(".medicine-batch-no input").val(v1)
         // field1.val(v1)

         var max = selectedValue.closest('tr').find('.batch-current-stock').text();
         $(".selectedCls").find(".medicine-quantity input").val(1)
         $(".selectedCls").find(".medicine-quantity input").attr("max", max);
         $(".selectedCls").find(".medicine-quantity input").attr("min", 0);



         var v1 = selectedValue.closest('tr').find('.batch-medicine-unit').text();
         $(".selectedCls").find(".medicine-unit-id input").val(v1)

         var v1 = selectedValue.closest('tr').find('.batch-medicine-unit-price').text();
         $(".selectedCls").find(".medicine-rate input").val(v1)

         var v2 = selectedValue.closest('tr').find('.batch-medicine-unit-price').text();
         $(".selectedCls").find(".medicine-amount input").val(v2)

         var v1 = selectedValue.closest('tr').find('.batch-medicine-mfd').text();
         $(".selectedCls").find(".medicine-mfd input").val(v1)

         var v1 = selectedValue.closest('tr').find('.batch-medicine-expd').text();
         $(".selectedCls").find(".medicine-expd input").val(v1)

         var v1 = selectedValue.closest('tr').find('.batch-medicine-tax-rate').text();
         $(".selectedCls").find(".medicine-tax-rate input").val(v1)

         var v1 = selectedValue.closest('tr').find('.medicine-stock-id').text();
         $(".selectedCls").find(".medicine-stock-id").val(v1)

         // msg 
         var v1 = selectedValue.closest('tr').find('.batch-current-stock').text();
         $(".selectedCls").find(".medicine-current-stock input").val(v1)
         var v1 = selectedValue.closest('tr').find('.batch-medicine-reorder-limit').text();
         $(".selectedCls").find(".medicine-reorder-limit input").val(v1)
         
         var v1 = selectedValue.closest('tr').find('.batch-medicine-tax-rate').text();
         $(".selectedCls").find(".medicine-tax input").val(v1)


         // med_stock_id 
         // taxCalculation() 


      });
   });
   });


   $(document).on('click', '#close-modal', function() {
      // ******************
      var selectedValue = $("input[name='selected_batch']:checked")
      //console.log("test"+ selectedValue)
      //alert(selectedValue.length)
      if (selectedValue.length != 0) {
         var id = selectedValue.closest('tr').find('.medicine-stock-id').text();
         var ids = $('#productTable .medicine-stock-id:not(:first-child) input[name="med_stock_id[]"]');
         // console.log(ids)
         var j = 0
         var max = parseFloat(selectedValue.closest('tr').find('.batch-current-stock').text());
         // var v2 = selectedValue.closest('tr').find('.batch-medicine-unit-price').text();
         //    $(".selectedCls").find(".medicine-amount input").val(v2)
         var selected = null
         //alert(id)
         selected = ids.filter(function() {
            return $(this).val() === id;
         });
         j = selected.length
         var amt = 0
         if (j > 1) {

            selected.each(function(index) {
               if (index == j - 1) {
                  $(this).closest('tr').find(".medicine-quantity input").val(j)
                  amt = $(this).closest('tr').find(".medicine-amount input").val()
                  $(this).closest('tr').find(".medicine-amount input").val(j * amt)

               } else {
                  $(this).closest('tr').remove()
               }

            });

         }
         var lmt = parseFloat(selectedValue.closest('tr').find('.batch-medicine-reorder-limit').text());
         var stck = parseFloat(selectedValue.closest('tr').find('.batch-current-stock').text());
         var quantity = parseFloat($(".selectedCls").find(".medicine-quantity input").val());
         //$(".selectedCls").find(".medicine-quantity").append('<span>Limited Stock</span>')
         var checkVal = 0
         //  if (stck > lmt) {
         //     checkVal = stck - lmt
         //     $(".selectedCls").find(".medicine-quantity span").remove()
         //  } else {
         //     $(".selectedCls").find(".medicine-quantity").append('<span>Limited Stock</span>')
         //  }
         //  if (checkVal != 0 && checkVal <= quantity) {
         //     $(".selectedCls").find(".medicine-quantity").append('<span>Limited Stock</span>')
         //  }
         //  if (checkVal > quantity) {
         //     $(".selectedCls").find(".medicine-quantity span").remove()
         //  }
         // ****************
         var inputElements = $('input[name="amount[]"]');
         var sum = 0;
         inputElements.each(function() {
            sum += parseFloat($(this).val()) || 0;
         });

         $(".tot").text(sum);
         $('#sub-total-input').val(sum);

         //   tax 
         // var inputElements = $('input[name="rate[]"]');
         var tax = $('input[name="tax_rate[]"]');
         var sum1 = 0;
         var totalTax = 0
         inputElements.each(function() {
            sum1 = parseFloat($(this).val()) || 0;
            var x = $(this).parent("td").siblings(".medicine-tax-rate").find('input').val();
            x = parseFloat(x) || 0;
            var tax = (sum1 * x) / 100;
            var y = $(this).parent("td").siblings(".medicine-tax-amount").find('input');
            y.val(tax)
            totalTax += tax
         });
         1
         $(".tax-amount").text(totalTax);
         $('#tax-amount-input').val(totalTax);
         $(".total-amount").text(sum + totalTax);
         $('#total-amount-input').val(sum + totalTax);

         var totalA = parseFloat($(".total-amount").text())

         var payable = totalA

         payable = Number(payable.toFixed(2));
         $(".payable-amount b").text('' + payable)
         $("#pay1").val(payable);
         // $(".paid-amount").val(payable)
      }
      var disable = $('input[name="batch_no[]"]');

      disable.each(function() {
         if ($(this).val() == '') {
            $(this).parent("td").siblings(".medicine-quantity").find('input').prop("readonly", true);
         } else {
            $(this).parent("td").siblings(".medicine-quantity").find('input').prop("readonly", false);
         }

      });

   });
   // calculate amount 
   function calculateAmount(input) {
      // Get the parent row
      var row = input.closest('tr');
      var inputElements = $('input[name="amount[]"]');
      // Find the rate input field in the same row
      var rateInput = row.querySelector('.medicine-rate input');

      // Find the amount input field in the same row
      var amountInput = row.querySelector('.medicine-amount input');



      // Calculate the amount based on the quantity and rate
      var quantity = parseFloat(input.value);
      var rate = parseFloat(rateInput.value);

      if (!isNaN(quantity) && !isNaN(rate)) {
         var sum = 0;
         var amount = quantity * rate;
         amountInput.value = amount.toFixed(2); // Set the value with 2 decimal places

         inputElements.each(function() {
            sum += parseFloat($(this).val()) || 0;
         });
         // var current_sub_total = $(".tot").val();
         // var updated_sub_total = current_sub_total + amount;
         $(".tot").text(sum);
         $('#sub-total-input').val(sum);
         var amount = $('input[name="amount[]"]');
         var sum1 = 0;
         var totalTax = 0
         amount.each(function() {
            sum1 = parseFloat($(this).val()) || 0;
            var x = $(this).parent("td").siblings(".medicine-tax-rate").find('input').val();
            // alert(x)
            x = parseFloat(x) || 0;
            var tax = (sum1 * x) / 100;
            var y = $(this).parent("td").siblings(".medicine-tax-amount").find('input');
            y.val(tax)
            // alert(rate)
            totalTax += tax
         });


         $(".tax-amount").text(totalTax);
         $('#tax-amount-input').val(totalTax);
         $(".total-amount").text(sum + totalTax);
         $('#total-amount-input').val(sum + totalTax);

         // Discount
         var totalA = parseFloat($(".total-amount").text())
         var discount = $("#discount-amount-input").val()
         var discountT = (totalA * discount) / 100
         console.log(discount, "discount");

         var payable = totalA - discountT
         console.log(payable);
         payable = Number(payable.toFixed(2));
         $(".payable-amount b").text('' + payable)
         $("#pay1").val(payable);
         // $(".paid-amount").val(payable)



      } else {
         amountInput.value = ''; // Clear the amount if either quantity or rate is not a number
      }


   }
   $(document).on('click', '.no-selected-item', function() {
      // var selectedValue = $("input[name='selected_batch']:checked")
      $("input[name='selected_batch']:checked").prop("checked", false);

      // Remove the "style" attribute to make the row visible
      var newRow = $('.selectedCls');
      newRow.removeAttr("style");
      // newRow.find('select').addClass('medicine-select');
      newRow.find('input[type="text"]').val('');
      newRow.find('input[type="number"]').val('');
      newRow.find('input').removeAttr("disabled")
      // newRow.removeAttr('style')
      newRow.find('input span').remove()

   });

   $(document).on('input', '.medicine-quantity input', function() {
      var stck = parseFloat($(this).closest('tr').find('.medicine-current-stock input').val());
      var lmt = parseFloat($(this).closest('tr').find('.medicine-reorder-limit input').val());
      var quantity = parseFloat($(this).val());
      // $(".selectedCls").find(".medicine-quantity").append('<span>Limited Stock</span>')
      //alert(stck)
      // alert(quantity)


      var checkVal = 0
      //   if (stck > lmt) {
      //      checkVal = stck - lmt
      //      //alert(checkVal)
      //      $(this).closest('tr').find(".medicine-quantity span").remove()
      //   } else {
      //      $(this).closest('tr').find(".medicine-quantity").append('<span>Limited Stock</span>')
      //   }
      //   if (checkVal != 0 && checkVal <= quantity) {
      //      $(this).closest('tr').find(".medicine-quantity").append('<span>Limited Stock</span>')
      //   }
      //   if (checkVal > quantity) {
      //      $(this).closest('tr').find(".medicine-quantity span").remove()
      //   }

      if (stck < quantity) {
         // $(this).closest('tr').find(".medicine-quantity").append('<div class="alert-div">Limited Stock</div>')
         var alertDiv = $('<div>').text('Value must be less than ' + stck).addClass('alert-div');
         $(this).closest('tr').find(".medicine-quantity").append(alertDiv);
         $(this).val('');

         setTimeout(function() {
            alertDiv.remove();
         }, 1000);
      } else {
         $(this).closest('tr').find(".medicine-quantity div").remove()
      }
   })
</script>
<script>
   $(document).ready(function() {
      // Add an event listener to the discount amount input
      $('#discount-amount-input').on('input', function() {
         var enteredAmount = parseFloat($(this).val());
         var maxDiscount = parseFloat('{{ $discount_percentage }}');

         // Check if the entered amount is greater than the maximum discount percentage
         if (enteredAmount > maxDiscount) {
            // Show a popup error message (you can customize this part)
            Swal.fire({
               icon: 'error',
               title: 'Error',
               text: 'Entered discount cannot be greater than ' + maxDiscount + '%',
            });

            // Reset the input value to the maximum discount
            $(this).val(maxDiscount);
         }
      });
   });
</script>
<script>
   $(document).ready(function() {
      // Add event listeners to the discount and total amount inputs
      $('#discount-amount-input, #total-amount-input').on('input', function() {
         // Get the values of discount and total amount
         var discountAmount = parseFloat($('#discount-amount-input').val()) || 0;

         var totalAmount = parseFloat($('#total-amount-input').val()) || 0;


         // Calculate payable amount
         var payableAmount = totalAmount - (totalAmount * discountAmount / 100);
         console.log(payableAmount);
         // Update the payable amount in the UI
         // $('#payable-amount').text(payableAmount.toFixed(2));
         $(".payable-amount b").text('' + payableAmount.toFixed(2));
         $("#pay1").val(payableAmount.toFixed(2));
         //  $(".paid-amount").val(payableAmount.toFixed(2));
      });
   });
</script>

<script>
   $(document).ready(function() {
      // Add event listeners to the discount, total amount, and percentage inputs
      $('#discount-amount-input, #total-amount-input, #percentage-input').on('input', function() {
         // Calculate total amount and update UI
         var sum = calculateTotalAmount();
         $(".total-amount").text(sum.toFixed(2));
         $('#total-amount-input').val(sum.toFixed(2));

         // Calculate payable amount
         var payable = calculatePayableAmount();

         // Update the payable amount in the UI
         $('#payable-amount').text(payable.toFixed(2));

         // Update the paid amount based on the percentage
         var percentage = parseFloat($('#percentage-input').val()) || 0;
         var paidAmount = (percentage / 100) * payable;

         // Update the paid amount in the UI
         $(".payable-amount b").text(paidAmount.toFixed(2));
         $("#pay1").val(paidAmount.toFixed(2));
         // $(".paid-amount").val(paidAmount.toFixed(2));
      });

      function calculateTotalAmount() {
         // Your existing code to calculate total amount
         // ...
         return sum;
      }

      function calculatePayableAmount() {
         // Your existing code to calculate payable amount
         var totalA = parseFloat($(".total-amount").text());
         var payable = totalA;
         payable = Number(payable.toFixed(2));
         return payable;
      }
   });

   function removeFn(parm) {
      //  alert("test")
      var currentRow = $(parm).closest('tr');
      currentRow.remove();

      var inputElements = $('input[name="amount[]"]');
      var sum = 0;
      inputElements.each(function() {
         sum += parseFloat($(this).val()) || 0;
      });

      $(".tot").text(sum);
      $('#sub-total-input').val(sum);

      //   tax 
      // var inputElements = $('input[name="rate[]"]');
      var tax = $('input[name="tax_rate[]"]');
      var sum1 = 0;
      var totalTax = 0
      inputElements.each(function() {
         sum1 = parseFloat($(this).val()) || 0;
         var x = $(this).parent("td").siblings(".medicine-tax-rate").find('input').val();
         x = parseFloat(x) || 0;
         var tax = (sum1 * x) / 100;
         var y = $(this).parent("td").siblings(".medicine-tax-amount").find('input');
         y.val(tax)
         totalTax += tax
      });
      1
      $(".tax-amount").text(totalTax);
      $('#tax-amount-input').val(totalTax);
      $(".total-amount").text(sum + totalTax);
      $('#total-amount-input').val(sum + totalTax);

      var totalA = parseFloat($(".total-amount").text())

      var payable = totalA

      payable = Number(payable.toFixed(2));
      $(".payable-amount b").text('' + payable)
      $("#pay1").val(payable);
      // $(".paid-amount").val(payable)
   }

   
</script>
<script>
  // Get all elements with the class name "numericInput"
const numericInputs = document.getElementsByClassName('numericInput');

// Add event listener to each numeric input element
for (let i = 0; i < numericInputs.length; i++) {
    numericInputs[i].addEventListener('input', function(event) {
        let inputValue = event.target.value;
        inputValue = inputValue.replace(/[^0-9.]/g, '');
        inputValue = inputValue.replace(/(\..*)\./g, '$1');
        event.target.value = inputValue;
    });
}
function validateAmount(input) {
    var value = parseFloat(input.value);
    if (isNaN(value) || value <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Paid amount must be a number greater than 0',
        });
        input.value = "";
        input.focus();
    }
}



    </script>
    
    <script>
    $(document).ready(function() {
        @if(session('selected_patient_id'))
            // Get the selected_patient_id value from the session
            var selectedPatientId = '{{ session('selected_patient_id') }}';

            // Set the value of the Select2 dropdown to the selected_patient_id
            $('#patient_id').val(selectedPatientId).trigger('change');
        @endif
    });
</script>
<script>
function filter(input) {
    var valThis = $(input).val();
    $(input).closest('.dropdown-select').find('ul > li').each(function () {
        var text = $(this).text();
        (text.toLowerCase().indexOf(valThis.toLowerCase()) > -1) ? $(this).show() : $(this).hide();
    });
}

// Ensure the DOM is ready before executing the script
$(document).ready(function () {
    // Function to create custom dropdowns
    function create_custom_dropdowns() {
        $('select.medsearch').each(function (i, select) {
            if (!$(this).next().hasClass('dropdown-select')) {
                $(this).after('<div class="dropdown-select wide ' + ($(this).attr('class') || '') + '" tabindex="0"><span class="current"></span><div class="list"><ul></ul></div></div>');
                var dropdown = $(this).next();
                var options = $(select).find('option');
                var selected = $(this).find('option:selected');
                dropdown.find('.current').html(selected.data('display-text') || selected.text());
                options.each(function (j, o) {
                    var display = $(o).data('display-text') || '';
                    var disabledAttribute = $(o).is(':disabled') ? 'disabled' : '';
                    dropdown.find('ul').append('<li class="option ' + ($(o).is(':selected') ? 'selected' : '') + '" data-value="' + $(o).val() + '" data-display-text="' + display + '" ' + disabledAttribute + '>' + $(o).text() + '</li>');
                });
            }
            $(this).next().find('ul').before('<div class="dd-search"><input id="" autocomplete="off" onkeyup="filter(this)" class="dd-searchbox txtSearchValue" type="text"></div>');
        });
    }

    // Call the function to create custom dropdowns
    create_custom_dropdowns();
    
     $('#productTable tbody tr').not(':first').hide();

    // Event listeners

    // Open/close
    $(document).on('click', '.dropdown-select', function (event) {
        if ($(event.target).hasClass('dd-searchbox')) {
            return;
        }
        $('.dropdown-select').not($(this)).removeClass('open');
        $(this).toggleClass('open');
        if ($(this).hasClass('open')) {
            $(this).find('.option').attr('tabindex', 0);
            $(this).find('.selected').focus();
        } else {
            $(this).find('.option').removeAttr('tabindex');
            $(this).focus();
        }
    });

    // Close when clicking outside
    $(document).on('click', function (event) {
        if ($(event.target).closest('.dropdown-select').length === 0) {
            $('.dropdown-select').removeClass('open');
            $('.dropdown-select .option').removeAttr('tabindex');
        }
        event.stopPropagation();
    });

    // Option click
    $(document).on('click', '.dropdown-select .option', function (event) {
        $(this).closest('.list').find('.selected').removeClass('selected');
        $(this).addClass('selected');
        var text = $(this).data('display-text') || $(this).text();
        $(this).closest('.dropdown-select').find('.current').text(text);
        $(this).closest('.dropdown-select').prev('select').val($(this).data('value')).trigger('change');
    });

    // Keyboard events
    $(document).on('keydown', '.dropdown-select', function (event) {
        var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
        // Space or Enter
        //if (event.keyCode == 32 || event.keyCode == 13) {
        if (event.keyCode == 13) {
            if ($(this).hasClass('open')) {
                focused_option.trigger('click');
            } else {
                $(this).trigger('click');
            }
            return false;
            // Down
        } else if (event.keyCode == 40) {
            if (!$(this).hasClass('open')) {
                $(this).trigger('click');
            } else {
                focused_option.next().focus();
            }
            return false;
            // Up
        } else if (event.keyCode == 38) {
            if (!$(this).hasClass('open')) {
                $(this).trigger('click');
            } else {
                var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
                focused_option.prev().focus();
            }
            return false;
            // Esc
        } else if (event.keyCode == 27) {
            if ($(this).hasClass('open')) {
                $(this).trigger('click');
            }
            return false;
        }
    });
});
</script>
@endsection