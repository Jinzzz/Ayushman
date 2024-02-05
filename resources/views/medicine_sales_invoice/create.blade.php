@extends('layouts.app')
@section('content')
@php
use App\Helpers\AdminHelper;
// dd(AdminHelper::getProductId($value->medicine_code));
@endphp
<div class="container">
   <style>
      /* Custom CSS for ash color border */
      .card-ash-border {
         border: 1px solid #b0b0b0;
         /* Adjust the color as needed */
      }

      td.medicine-quantity span {
         color: red;
         font-size: 10px;
         position: absolute;
         bottom: -2px;
         left: 00;
         text-align: center;
         width: 100%;
      }

      td.medicine-quantity {
         position: relative;
      }

      .display-med-row {
         display: none;
      }
   
.card-table {
    margin-bottom: 15px;
}

      .table th {   font-size: 12px;} select.medicine-name { display: none !important; } span.current {
    font-size: 10px!important; } .table td {padding: 5px 3px;} .pricecard .form-group label {font-size: 12px;margin:0;} .pricecard .form-group span, .pricecard .form-group input {width: auto;padding: 0;border: unset;line-height: 1;height:auto;} .pricecard .form-group input:focus {outline: unset !important;border: none !important;} .pricecard .form-group {display: flex;align-items: center;gap: 16px;margin: 5px 0;}.pricecard .col-md-4 {margin-left: auto;}.dropdown-select {background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 100%);background-repeat: repeat-x;filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#40FFFFFF', endColorstr='#00FFFFFF', GradientType=0);background-color: #fff;border-radius: 6px;box-sizing: border-box;cursor: pointer;display: block;float: left;font-size: 14px;font-weight: normal;outline: none;padding-left: 18px;padding-right: 30px;position: relative;text-align: left !important;transition: all 0.2s ease-in-out;-webkit-user-select: none;-moz-user-select: none; -ms-user-select: none;user-select: none;white-space: nowrap; width: auto;}.dropdown-select:focus {background-color: #fff;}.dropdown-select:hover {background-color: #fff;}.dropdown-select:active,.dropdown-select.open {background-color: #fff !important;border-color: #bbb;box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) inset;}.dropdown-select:after {height: 0; width: 0; border-left: 4px solid transparent; border-right: 4px solid transparent; border-top: 4px solid #777;-webkit-transform: origin(50% 20%); transform: origin(50% 20%); transition: all 0.125s ease-in-out; content: ''; display: block; margin-top: -2px; pointer-events: none; position: absolute; right: 10px; top: 50%;}.dropdown-select.open:after { -webkit-transform: rotate(-180deg); transform: rotate(-180deg);}.dropdown-select.open .list { -webkit-transform: scale(1); transform: scale(1); opacity: 1; pointer-events: auto;}.dropdown-select.open .option {cursor: pointer;}.dropdown-select.wide {width: 100%;}.dropdown-select.wide .list {left: 0 !important;right: 0 !important;}.dropdown-select .list {box-sizing: border-box; transition: all 0.15s cubic-bezier(0.25, 0, 0.25, 1.75), opacity 0.1s linear; -webkit-transform: scale(0.75); transform: scale(0.75); -webkit-transform-origin: 50% 0; transform-origin: 50% 0; box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.09); background-color: #fff; border-radius: 6px; margin-top: 4px; padding: 3px 0; opacity: 0; overflow: hidden; pointer-events: none; position: absolute; top: 100%; left: 0; z-index: 999; max-height: 250px; overflow: auto; border: 1px solid #ddd;}.dropdown-select .list:hover .option:not(:hover) { background-color: transparent !important;}.dropdown-select .dd-search{overflow:hidden;display:flex;align-items:center;justify-content:center;margin:5px 0;}.dropdown-select .dd-searchbox{width:90%;padding:0.5rem;border:1px solid #999;border-color:#999;border-radius:4px;outline:none;line-height: 1;}.dropdown-select .dd-searchbox:focus{border-color:#12CBC4;}.dropdown-select .list ul { padding: 0;}.dropdown-select .option {cursor: default; font-weight: 400; line-height: 2; outline: none; padding-left: 10px; padding-right: 25px; text-align: left; transition: all 0.2s; list-style: none; font-size: 10px;}.dropdown-select .option:hover,.dropdown-select .option:focus {background-color: #f6f6f6 !important;}.dropdown-select .option.selected { font-weight: 600; color: #12cbc4;}.dropdown-select .option.selected:focus {background: #f6f6f6;}.dropdown-select a {color: #aaa; text-decoration: none; transition: all 0.2s ease-in-out;}.dropdown-select a:hover {
    color: #666;}
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
                  <!-- <strong>Whoops!</strong> There were some problems with your input.<br><br> -->                  <ul>
                     @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
               @endif
               <form action="{{ route('medicine.sales.invoices.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                  @csrf
                  <input type="hidden" name="discount_percentage" value="3" id="discount_percentage">
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">Select Patient*</label>
                           <select class="form-control" name="patient_id" id="patient_id" required>
                              <option value="">Select Patient</option>
                              <option value="0">Guest Patient</option>
                              @foreach ($patients as $patient)
                              <option value="{{ $patient->id }}">
                                 {{ $patient->patient_name }} ({{ $patient->patient_code }})
                              </option>
                              @endforeach
                           </select>
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
                           <select class="form-control" name="pharmacy_id" id="pharmacy_id" required>
                              <option value="">Select Pharmacy</option>
                              @foreach ($pharmacies as $pharmacy)
                              <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
                              @endforeach
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
                                       <th>Medicine Name</th>
                                       <th>Batch No</th>
                                       <th>Quantity</th>
                                       <th>Unit</th>
                                       <th>Rate</th>
                                       <th>Amount</th>
                                       <th>Actions</th>
                                       <!-- <th>Stock ID</th>
                                       <th>Current stock</th>
                                       <th>Order limit</th>
                                       <th>Tax Rate</th>
                                       <th>Tax Amount</th>
                                       <th>Manufacture Date</th>
                                       <th>Expiry Date</th> -->
                                    </tr>
                                 </thead>
                                 <tbody>
                                 <tr id="productRowTemplate" style="display: none">
                                       <td>
                                          <select class="form-control medicine-name" name="medicine_id[]" dis>
                                             <option value="">Please select medicine</option>
                                             @foreach($medicines as $medicine)
                                             <option value="{{ $medicine->id }}">{{ $medicine->medicine_name}}</option> @endforeach
                                          </select>
                                       </td>
                                       <td class="medicine-batch-no"><input type="text" class="form-control" value="" name="batch_no[]" readonly></td>
                                       <td class="medicine-quantity"><input type="number" min="1" class="form-control" value="" name="quantity[]" oninput="calculateAmount(this)"></td>
                                       <td class="medicine-unit-id"><input type="text" class="form-control" value="" name="unit_id[]" readonly></td>
                                       <td class="medicine-rate"><input type="text" class="form-control" value="" name="rate[]" readonly></td>
                                       <td class="medicine-amount"><input type="text" class="form-control" value="" name="amount[]" readonly></td>
                                       <td><button type="button" onclick="myClickFunction(this)" style="background-color: #007BFF; color: #FFF; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Remove</button></td>
                                       <td class="display-med-row medicine-stock-id"><input type="hidden" class="form-control" name="med_stock_id[]" readonly></td>
                                       <td class="display-med-row medicine-current-stock"><input type="hidden" class="form-control" name="current-stock[]" readonly></td>
                                       <td class="display-med-row medicine-reorder-limit"><input type="hidden" class="form-control" name="limit[]" readonly></td>
                                       <td class="display-med-row medicine-tax-rate"><input type="hidden" class="form-control" name="tax_rate[]"></td>
                                       <td class="display-med-row medicine-tax-amount"><input type="hidden" class="form-control" name="single_tax_amount[]" readonly></td>
                                       <td class="display-med-row medicine-mfd"><input type="hidden" class="form-control" name="mfd[]" readonly></td>
                                       <td class="display-med-row medicine-expd"><input type="hidden" class="form-control" name="expd[]" readonly></td>
                                    </tr>
                                    <tr id="productRowTemplate" style="display: none">
                                       <td>
                                          <select class="form-control " name="medicine_id[]" dis>
                                             <option value="">Please select medicine</option>
                                             @foreach($medicines as $medicine)
                                             <option value="{{ $medicine->id }}">{{ $medicine->medicine_name}}</option>
                                             @endforeach
                                          </select>
                                       </td>
                                       <td class="medicine-batch-no"><input type="text" class="form-control" name="batch_no[]" readonly></td>
                                       <td class="medicine-quantity"><input type="number" min="1" class="form-control" name="quantity[]" oninput="calculateAmount(this)"></td>
                                       <td class="medicine-unit-id"><input type="text" class="form-control" name="unit_id[]" readonly></td>
                                       <td class="medicine-rate"><input type="text" class="form-control" name="rate[]" readonly></td>
                                       <td class="medicine-amount"><input type="text" class="form-control" name="amount[]" readonly></td>
                                       <td><button type="button" onclick="myClickFunction(this)" style="background-color: #007BFF; color: #FFF; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Remove</button></td>
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
                                       <th>Stock ID</th>
                                       <th>Batch Number</th>
                                       <th>Type</th>
                                       <th>MFD</th>
                                       <th>EXPD</th>
                                       <th>Current Stock</th>
                                       <th>Reorder Limit</th>
                                       <th>Unit</th>
                                       <th>Unit Price</th>
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
                                       <td><strong>Discount Amount</strong></td>
                                       <td style="text-align: right;"><strong class="discount-amount">0</strong><input type="hidden" id="discount-amount-input" name="discount_amount" value="0"></td>
                                    </tr>
                                 </table>
                                 <hr>
                                 <div class="form-group mb-2"> <!-- Decreased margin height -->
                                    <label class="form-label payable-amount">Payable Amount : <b>0</b></label>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- payment   -->
                  <div id="payment_fields">
                     <div class="row">
                        <div class="col-md-4">
                           <div class="form-group">
                              <label class="form-label">Paid Amount</label>
                              <input type="text" class="form-control paid-amount" name="paid_amount" maxlength="16" value="0" readonly placeholder="Paid Amount">
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group">
                              <label for="payment-type" class="form-label">Payment Mode</label>
                              <select class="form-control" required name="payment_mode" placeholder="Payment Mode" id="payment_mode" onchange="updateDepositTo()">
                                 <option value="">--Select--</option>
                                 @foreach($paymentType as $id => $value)
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
                  </div>
                  <div class="form-group">
                     <center>
                        <button type="submit" class="btn btn-raised btn-primary">
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
</div>
@endsection
@section('js')
<!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


<script>
 
   // total amount 
   // Get the current date
   var currentDate = new Date();
   // Format the date as "YYYY-MM-DD" (required by input type="date)
   var formattedDate = currentDate.toISOString().split('T')[0];
   // Set the value of the input field to today's date
   document.getElementById("date").value = formattedDate;

   $(document).ready(function() {
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
      $('#payment_mode').change(function() {
         // Get the selected value
         var selectedPaymentMode = $(this).val();
         // alert(selectedPaymentMode);
         // Make an AJAX request to fetch the ledger names based on the selected payment mode
         $.ajax({
            url: '{{ route("getLedgerNames1") }}',
            type: 'GET',
            data: {
               payment_mode: selectedPaymentMode
            },
            success: function(data) {
               // Clear existing options
               $('#deposit_to').empty();

               // Add default option
               $('#deposit_to').append('<option value="">Deposit To</option>');

               // Add options based on the response
               $.each(data, function(key, value) {
                  $('#deposit_to').append('<option value="' + key + '">' + value + '</option>');
               });
            },
            error: function(error) {
               console.log(error);
            }
         });
      });

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
         $("#statusText").text('Print Invoice');
         $("input[name=is_print]").val(1); // Set the value to 1 when checked
      } else {
         $("#statusText").text('Do Not Print');
         $("input[name=is_print]").val(0); // Set the value to 0 when unchecked
      }
   }

   $(document).on('change', '.medicine-select', function() {


      // $("#medicineBatchModal").show()
      var selected_medicine_id = $(this).val();
      var selct = $(this);
      $(".selectedCls").removeClass("selectedCls")
      $(this).parents('tr').addClass("selectedCls");

      var field1 = $(this).parents('tr').find(".medicine-batch-no input")
      $.ajax({
         url: "{{ route('get.medicine.batches', '') }}/" + selected_medicine_id,
         method: "patch",
         data: {
            _token: "{{ csrf_token() }}",
         },
         success: function(response) {
            console.log('response',response);
            // Clear previous data in the modal
            $('#medicineBatchDetails').empty();

            // Populate the modal with the received data
            response.data.forEach(function(item, index) {
               //alert(item.id)
               var row = `<tr>
                    <td class="batch-index">${index+1}</td>
                    <td class="medicine-stock-id">${item.id}</td>
                    <td class="batch-medicine-batch-number">${item.medicine_batch_number}</td>
                    <td class="batch-medicine-type">${item.medicine_type}</td>
                    <td class="batch-medicine-mfd">${item.medicine_mfd}</td>
                    <td class="batch-medicine-expd">${item.medicine_expd}</td>
                    <td class="batch-current-stock">${item.medicine_current_stock}</td>
                    <td class="batch-medicine-reorder-limit">${item.medicine_reorder_limit}</td>
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


         // med_stock_id 
         // taxCalculation() 


      });
   });


   $(document).on('click', '#close-modal', function() {
      // ******************
      var selectedValue = $("input[name='selected_batch']:checked")
      // console.log("test"+ selectedValue)
      if (selectedValue.length != 0) {
         var id = selectedValue.closest('tr').find('.medicine-stock-id').text();
         var ids = $('input[name="med_stock_id[]"]');
         var j = 0
         var max = parseFloat(selectedValue.closest('tr').find('.batch-current-stock').text());
         // var v2 = selectedValue.closest('tr').find('.batch-medicine-unit-price').text();
         //    $(".selectedCls").find(".medicine-amount input").val(v2)
         var selected = null
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
         if (stck > lmt) {
            checkVal = stck - lmt
            $(".selectedCls").find(".medicine-quantity span").remove()
         } else {
            $(".selectedCls").find(".medicine-quantity").append('<span>Limited Stock</span>')
         }
         if (checkVal != 0 && checkVal <= quantity) {
            $(".selectedCls").find(".medicine-quantity").append('<span>Limited Stock</span>')
         }
         if (checkVal > quantity) {
            $(".selectedCls").find(".medicine-quantity span").remove()
         }
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
         var discount = $("#discount_percentage").val()
         var discountT = (totalA * discount) / 100
         //alert(discountT)
         $("#discount-amount-input").val(discountT)
         $(".discount-amount").text('' + discountT)
         var payable = totalA - discountT
         
         payable = Number(payable.toFixed(2));
         $(".payable-amount b").text('' + payable)
         $(".paid-amount").val(payable)
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
         var discount = $("#discount_percentage").val()
         var discountT = (totalA * discount) / 100
         //alert(discountT)
         $("#discount-amount-input").val(discountT)
         $(".discount-amount").text('' + discountT)
         var payable = totalA - discountT
         payable = Number(payable.toFixed(2));
         $(".payable-amount b").text('' + payable)
         $(".paid-amount").val(payable)



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

   $(document).on('change', '.medicine-quantity input', function() {
      var stck = parseFloat($(this).closest('tr').find('.medicine-current-stock input').val());
      var lmt = parseFloat($(this).closest('tr').find('.medicine-reorder-limit input').val());
      var quantity = parseFloat($(this).val());
      // $(".selectedCls").find(".medicine-quantity").append('<span>Limited Stock</span>')
      // alert(lmt)
      // alert(quantity)


      var checkVal = 0
      if (stck > lmt) {
         checkVal = stck - lmt
         //alert(checkVal)
         $(this).closest('tr').find(".medicine-quantity span").remove()
      } else {
         $(this).closest('tr').find(".medicine-quantity").append('<span>Limited Stock</span>')
      }
      if (checkVal != 0 && checkVal <= quantity) {
         $(this).closest('tr').find(".medicine-quantity").append('<span>Limited Stock</span>')
      }
      if (checkVal > quantity) {
         $(this).closest('tr').find(".medicine-quantity span").remove()
      }
   })

   
</script>

<script>

   function create_custom_dropdowns() {
    $('select.medicine-name').each(function (i, select) {
        if (!$(this).next().hasClass('dropdown-select')) {
            $(this).after('<div class="dropdown-select wide ' + ($(this).attr('class') || '') + '" tabindex="0"><span class="current"></span><div class="list"><ul></ul></div></div>');
            var dropdown = $(this).next();
            var options = $(select).find('option');
            var selected = $(this).find('option:selected');
            dropdown.find('.current').html(selected.data('display-text') || selected.text());
            options.each(function (j, o) {
                var display = $(o).data('display-text') || '';
                dropdown.find('ul').append('<li class="option ' + ($(o).is(':selected') ? 'selected' : '') + '" data-value="' + $(o).val() + '" data-display-text="' + display + '">' + $(o).text() + '</li>');
            });
        }
        $(this).next().find('ul').before('<div class="dd-search"><input id="" autocomplete="off" onkeyup="filter(this)" class="dd-searchbox txtSearchValue" type="text"></div>');
    });

    
}

// Event listeners

// Open/close
$(document).on('click', '.dropdown-select', function (event) {
    if($(event.target).hasClass('dd-searchbox')){
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

function filter(i){
    var valThis = $(i).val();
    $(i).closest('.dropdown-select').find('ul > li').each(function(){
     var text = $(this).text();
        (text.toLowerCase().indexOf(valThis.toLowerCase()) > -1) ? $(this).show() : $(this).hide();         
   });
};
// Search

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

$(document).ready(function () {
    create_custom_dropdowns();
    $("#addProductBtn").click();
});

</script>


@endsection