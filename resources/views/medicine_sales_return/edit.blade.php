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
         bottom: -1px;
         left: 00;
         text-align: center;
         width: 100%;
      }

      td.medicine-quantity {
         position: relative;
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
               @if ($errors->any())
               <div class="alert alert-danger">
                  <!-- <strong>Whoops!</strong> There were some problems with your input.<br><br> -->                  <ul>
                     @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
               @endif
               <form action="{{ route('medicine.sales.return.update') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                  @csrf
                  <input type="hidden" name="hdn_id" value="{{$medicine_sale_invoices->sales_return_id}}">
                  <input type="hidden" name="discount_percentage" value="3" id="discount_percentage">
                  <input type="hidden" name="saved-booking-id" value="77" id="saved-booking-id">
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Select Patient*</label>
                           <select class="form-control" name="patient_id" id="patient_id" required>
                              <option value="">Select Patient</option>
                              <option value="0" {{ $medicine_sale_invoices->patient_id == 0 ? ' selected' : '' }}>Guest Patient</option>
                              @foreach ($patients as $patient)
                              <option value="{{ $patient->id }}" {{ $patient->id == $medicine_sale_invoices->patient_id ? ' selected' : '' }}>
                                 {{ $patient->patient_name }} ({{ $patient->patient_code }})
                              </option>
                              @endforeach
                           </select>
                        </div>
                     </div>

                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Select Invoice ID*</label>
                           <select class="form-control" name="patient_invoice_id" id="patient_invoice_id">
                              <option value="">Choose Invoice ID</option>
                           </select>
                        </div>
                     </div>


                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Date</label>
                           <input type="date" class="form-control" readonly name="due_date" id="date" placeholder="Date">
                        </div>
                     </div>

                     <!-- <div class="col-md-3">
                        <div class="form-group">
                           <div class="form-label">Print Invoice</div>
                           <label class="custom-switch">
                              <input type="hidden" name="is_print" value="0">
                              <input type="checkbox" id="is_print" name="is_print" value="1" checked="checked" onchange="toggleStatus(this)" class="custom-switch-input">
                              <span id="statusLabel" class="custom-switch-indicator"></span>
                              <span id="statusText" class="custom-switch-description">
                                 Print Invoice
                              </span>
                           </label>
                        </div>
                     </div> -->
                  </div>
                  <div class="row">
                     <div class="col-md-12 col-lg-12">
                        <div class="card">
                           <div class="table-responsive">
                              <table class="table card-table table-vcenter text-nowrap" id="productTable">
                                 <thead>
                                    <tr>
                                       <th>Medicine Name</th>
                                       <!-- <th>Stock ID</th> -->
                                       <th>Batch No</th>
                                       <th>Quantity</th>
                                       <!-- <th>Current stock</th> -->
                                       <!-- <th>Order limit</th> -->
                                       <th>Unit</th>
                                       <th>Rate</th>
                                       <!-- <th>Tax Rate</th> -->
                                       <!-- <th>Tax Amount</th> -->
                                       <th>Amount</th>
                                       <!-- <th>Manufacture Date</th> -->
                                       <!-- <th>Expiry Date</th> -->
                                       <th>Actions</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach ($all_medicine_sale_details as $sale_details)
                                    <tr id="productRowTemplate">
                                       <td>
                                          <select class="form-control medicine-name" name="medicine_id[]" dis>
                                             <option value="">Please select medicine</option>
                                             @foreach($medicines as $medicine)
                                             <option value="{{ $medicine->id }}" {{ $medicine->id == $sale_details['medicine_id'] ? ' selected' : '' }}>{{ $medicine->medicine_name}}</option>
                                             @endforeach
                                          </select>
                                       </td>
                                       <td class="medicine-batch-no"><input type="text" class="form-control" value="{{$sale_details['batch_id']}}" name="batch_no[]" readonly></td>
                                       <td class="medicine-quantity"><input type="number" min="1" class="form-control" value="{{intval($sale_details['quantity'])}}" name="quantity[]" oninput="calculateAmount(this)"></td>
                                       <td class="medicine-unit-id"><input type="text" class="form-control" value="{{$sale_details['unit_name']}}" name="unit_id[]" readonly></td>
                                       <td class="medicine-rate"><input type="text" class="form-control" value="{{$sale_details['rate']}}" name="rate[]" readonly></td>
                                       <td class="medicine-amount"><input type="text" class="form-control" value="{{$sale_details['amount']}}" name="amount[]" readonly></td>
                                       <td><button type="button" onclick="myClickFunction(this)" style="background-color: #007BFF; color: #FFF; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Remove</button></td>
                                       <td class="medicine-stock-id"><input type="hidden" class="form-control" value="{{$sale_details['stock_id']}}" name="med_stock_id[]" readonly></td>

                                       <td class="medicine-current-stock"><input type="hidden" class="form-control" value="{{$sale_details['current_stock']}}" name="current-stock[]" readonly></td>
                                       <td class="medicine-reorder-limit"><input type="hidden" class="form-control" value="{{$sale_details['reorder_limit']}}" name="limit[]" readonly></td>

                                       <td class="medicine-tax-rate"><input type="hidden" class="form-control" value="{{$sale_details['single_tax_rate']}}" name="tax_rate[]"></td>
                                       <td class="medicine-tax-amount"><input type="hidden" class="form-control" value="{{$sale_details['single_tax_amount']}}" name="single_tax_amount[]" readonly></td>

                                       <td class="medicine-mfd"><input type="hidden" class="form-control" value="{{$sale_details['manufactured_date']}}" name="mfd[]" readonly></td>
                                       <td class="medicine-expd"><input type="hidden" class="form-control" value="{{$sale_details['expiry_date']}}" name="expd[]" readonly></td>
                                    </tr>
                                    @endforeach
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
                              <textarea class="form-control" name="notes" placeholder="Notes">{{ $medicine_sale_invoices->notes }}</textarea>
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
                                       <td style="text-align: right;"><strong class="tot">₹{{$medicine_sale_invoices->sub_total}}</strong><input type="hidden" id="sub-total-input" name="sub_total_amount" value="{{$medicine_sale_invoices->sub_total}}"></td>
                                    </tr>
                                    <tr>
                                       <td><strong>Tax Amount</strong></td>
                                       <td style="text-align: right;"><strong class="tax-amount">₹{{$medicine_sale_invoices->total_tax}}</strong><input type="hidden" id="tax-amount-input" name="total_tax_amount" value="{{$medicine_sale_invoices->total_tax}}"></td>
                                    </tr>
                                    <tr>
                                       <td><strong>Total Amount</strong></td>
                                       <td style="text-align: right;"><strong class="total-amount">₹{{$medicine_sale_invoices->total_amount}}</strong><input type="hidden" id="total-amount-input" name="total_amount" value="{{$medicine_sale_invoices->total_amount}}"></td>
                                    </tr>
                                    <tr>
                                       <td><strong>Discount Amount</strong></td>
                                       <td style="text-align: right;"><strong class="discount-amount">₹{{$medicine_sale_invoices->total_discount}}</strong><input type="hidden" id="discount-amount-input" name="discount_amount" value="{{$medicine_sale_invoices->total_discount}}"></td>
                                    </tr>
                                 </table>
                                 <hr>
                                 <div class="form-group mb-2"> <!-- Decreased margin height -->
                                    <label class="form-label payable-amount">Payable Amount : <b>₹0</b></label>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <center>
                        <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
                        <a class="btn btn-danger" href="{{ url('/medicine-sales-return') }}">Cancel</a>
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
         $('.total-amount').text('₹' + totalAmount.toFixed(2));
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
      $('#patient_invoice_id').select2();
      // Handle change event on the payment mode dropdown
      $('#payment_mode').change(function() {
         // Get the selected value
         var selectedPaymentMode = $(this).val();
         // alert(selectedPaymentMode);
         // Make an AJAX request to fetch the ledger names based on the selected payment mode
         $.ajax({
            url: '{{ route("getLedgerNames") }}',
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
         newRow.find('.medicine-name').val('');
         newRow.find('input').removeAttr("disabled")
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
         url: "{{ route('get.patient.invoice.ids', '') }}/" + selected_patient_id,
         method: "patch",
         data: {
            _token: "{{ csrf_token() }}",
         },
         success: function(data) {
            // alert(1);
            var v = $('#saved-booking-id').val()
            $('#patient_invoice_id').empty().append('<option value="">Choose Invoice ID</option>');
            $.each(data, function(key, value) {
               var isSelected = (key === v) ? 'selected' : '';
               $('#patient_invoice_id').append('<option value="' + key + '"'+ isSelected  +'>' + value + '</option>');
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
            console.log(response.data);
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

   // function taxCalculation() {
   //    var tax = $('input[name="tax_rate[]"]');
   //    var sum1 = 0;
   //    var totalTax = 0
   //    inputElements.each(function() {
   //       sum1 = parseFloat($(this).val()) || 0;
   //       var x = $(this).parent("td").siblings(".medicine-tax-rate").find('input').val();
   //       // alert(sum1);
   //       // alert(x)
   //       x = parseFloat(x) || 0;
   //       var tax = (sum1 * x) / 100;
   //       //alert(tax)
   //       totalTax += tax
   //    });

   // }

   function myClickFunction(bt) {
      var x = bt.parentNode.parentNode
      var subtotal = parseFloat($('.tot').text())
      var totaltax = parseFloat($('.tax-amount').text())

      var totalRemove = x.querySelector('input[name="amount[]"]').value;
      var taxRemove = x.querySelector('input[name="single_tax_amount[]"]').value;
      // alert(subtotal)
      // alert(totaltax)
      // alert(totalRemove)
      // alert(taxRemove)

      var subtotal = subtotal - totalRemove
      $('.tot').text(subtotal)
      var tax = totaltax - taxRemove
      $('.tax-amount').text(tax)
      var total = subtotal + tax
      $('.total-amount').text(total)

      var discount = $("#discount_percentage").val()
      var discountT = (total * discount) / 100
      //alert(discountT)
      $("#discount-amount-input").val(discountT)
      $(".discount-amount").text('₹' + discountT)
      var payable = total - discountT

      $(".payable-amount b").text('₹' + payable)
      $(".paid-amount").val(payable)

      x.remove()
   }

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
         $(".discount-amount").text('₹' + discountT)
         var payable = totalA - discountT

         $(".payable-amount b").text('₹' + payable)
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
         $(".discount-amount").text('₹' + discountT)
         var payable = totalA - discountT

         $(".payable-amount b").text('₹' + payable)
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
@endsection