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
               <form action="{{ route('medicine.sales.invoices.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                  @csrf
                  <input type="hidden" name="discount_percentage" value="3" id="discount_percentage">
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Select Patient*</label>
                           <select disabled class="form-control" readonly name="patient_id" id="patient_id" required>
                              @if($medicine_sale_invoices->patient_id == 0)
                              <option selected value="0">Guest Patient</option>
                              @else
                              @foreach ($patients as $patient)
                              <option value="{{ $patient->id }}" {{ $patient->id == $medicine_sale_invoices->patient_id ? ' selected' : '' }}>
                                 {{ $patient->patient_name }} ({{ $patient->patient_code }})
                              </option>
                              @endforeach
                              @endif
                           </select>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Select Booking ID*</label>
                           <select class="form-control" name="patient_booking_id" disabled>
                              <option selected readonly>{{$booking_id}}</option>
                           </select>
                        </div>


                     </div>


                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Date</label>
                           <input type="text" class="form-control" readonly name="due_date" value="{{ date('d-m-y', strtotime($medicine_sale_invoices->invoice_date)) }}" placeholder="Date">
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
                                       <!-- <th>MFD</th> -->
                                       <!-- <th>EXPD</th> -->
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach ($medicine_sale_details as $sale_details)
                                    <tr>
                                       <td>
                                          <select class="form-control " name="medicine_id[]" dis>
                                             <option value="">Please select medicine</option>
                                             @foreach($medicines as $medicine)
                                             <option value="{{ $medicine->id }}" {{ $medicine->id == $sale_details->medicine_id ? ' selected' : '' }}>{{ $medicine->medicine_name}}</option>
                                             @endforeach
                                          </select>
                                       </td>
                                       <td class="medicine-batch-no"><input type="text" class="form-control" value="{{$sale_details->batch_id}}" readonly></td>
                                       <td class="medicine-quantity"><input type="number" class="form-control" value="{{ intval($sale_details->quantity) }}" readonly></td>
                                       <td class="medicine-unit-id"><input type="text" class="form-control" value="{{$sale_details->unit->unit_name}}" readonly></td>
                                       <td class="medicine-rate"><input type="text" class="form-control" value="{{$sale_details->rate}}" readonly></td>
                                       <td class="medicine-amount"><input type="text" class="form-control" value="{{$sale_details->amount}}" readonly></td>
                                       <!-- <td class="medicine-mfd"><input type="text" class="form-control" value="{{$sale_details->manufactured_date}}" readonly></td> -->
                                       <!-- <td class="medicine-expd"><input type="text" class="form-control" value="{{$sale_details->expiry_date}}" readonly></td> -->
                                    </tr>
                                    @endforeach
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
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
                              <textarea class="form-control" name="notes" readonly placeholder="Notes">{{$medicine_sale_invoices->notes}}</textarea>
                           </div>
                           <div class="form-group">
                              <label class="form-label">Terms and Conditions:</label>
                              <textarea class="form-control" name="terms_condition" readonly placeholder="Terms and Conditions">{{$medicine_sale_invoices->terms_and_conditions}}</textarea>
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
                                       <td style="text-align: right;"><strong class="tot"></strong><input type="hidden" id="sub-total-input" name="sub_total_amount">₹{{$medicine_sale_invoices->sub_total}}</td>
                                    </tr>
                                    <tr>
                                       <td><strong>Tax Amount</strong></td>
                                       <td style="text-align: right;"><strong class="tax-amount"></strong><input type="hidden" id="tax-amount-input" name="total_tax_amount">₹{{$medicine_sale_invoices->total_tax_amount}}</td>
                                    </tr>
                                    <tr>
                                       <td><strong>Total Amount</strong></td>
                                       <td style="text-align: right;"><strong class=""></strong><input type="hidden" name="total_amount">₹{{$medicine_sale_invoices->total_amount}}</td>
                                    </tr>
                                    <tr>
                                       <td><strong>Discount Amount</strong></td>
                                       <td style="text-align: right;"><strong class="discount-amount"></strong><input type="hidden" id="discount-amount-input" name="discount_amount">₹{{$medicine_sale_invoices->discount_amount}}</td>
                                    </tr>
                                 </table>
                                 <hr>
                                 <div class="form-group mb-2"> <!-- Decreased margin height -->
                                    <label class="form-label payable-amount">Payable Amount : <b>₹{{$medicine_sale_invoices->payable_amount}}</b></label>
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
                              <input type="text" class="form-control paid-amount" name="paid_amount" maxlength="16" readonly placeholder="Paid Amount" value="{{$medicine_sale_invoices->payable_amount}}">
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group">
                              <label for="payment-type" class="form-label">Payment Mode</label>
                              <select disabled readonly class="form-control" required name="payment_mode" placeholder="Payment Mode" id="payment_mode" onchange="updateDepositTo()">
                                 <option value="">--Select--</option>
                                 @foreach($paymentType as $id => $value)
                                 <option value="{{ $id }}" {{ $id == $medicine_sale_invoices->payment_mode ? ' selected' : '' }}>{{ $value }}</option>
                                 @endforeach
                              </select>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group">
                              <label class="form-label">Deposit To</label>
                              <select class="form-control" readonly name="deposit_to" id="deposit_to" disabled>
                                 <option value="" selected readonly>{{$deposit_to}}</option>
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
      $('#patient_booking_id').select2();
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
         newRow.removeAttr('style')
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


         var selectedValue = $("input[name='selected_batch']:checked")
         //select.closest(".medicine-batch-no").find("input").val()


         var id = selectedValue.closest('tr').find('.medicine-stock-id').text();
         //var foundInput = $('input[name="' + specificName + '"][value="' + specificValue + '"]');

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
      x.remove()
   }

   $(document).on('click', '.modal-close', function() {
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
</script>
@endsection