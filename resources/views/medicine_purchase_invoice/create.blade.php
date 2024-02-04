@extends('layouts.app')
@section('content')
@php
use App\Helpers\AdminHelper;
// dd(AdminHelper::getProductId($value->medicine_code));
@endphp
<style>
   .table th {
      font-size: 12px;
   }
select {
    display: none !important;
}

span.current {
    font-size: 10px!important;
}

.table td {
    padding: 5px 3px;
}

.pricecard .form-group label {
    font-size: 12px;
    margin:0;
}
.pricecard .form-group span, .pricecard .form-group input {
    width: auto;
    padding: 0;
    border: unset;
    line-height: 1;
    height:auto;
}
.pricecard .form-group input:focus {
   outline: unset !important;
   border: none !important;
}
.pricecard .form-group {
    display: flex;
    align-items: center;
    gap: 16px;
    margin: 5px 0;
}
.pricecard .col-md-4 {
    margin-left: auto;
}

.dropdown-select {
    background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 100%);
    background-repeat: repeat-x;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#40FFFFFF', endColorstr='#00FFFFFF', GradientType=0);
    background-color: #fff;
    border-radius: 6px;
    box-sizing: border-box;
    cursor: pointer;
    display: block;
    float: left;
    font-size: 14px;
    font-weight: normal;
    outline: none;
    padding-left: 18px;
    padding-right: 30px;
    position: relative;
    text-align: left !important;
    transition: all 0.2s ease-in-out;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    white-space: nowrap;
    width: auto;

}

.dropdown-select:focus {
    background-color: #fff;
}

.dropdown-select:hover {
    background-color: #fff;
}

.dropdown-select:active,
.dropdown-select.open {
    background-color: #fff !important;
    border-color: #bbb;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) inset;
}

.dropdown-select:after {
    height: 0;
    width: 0;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-top: 4px solid #777;
    -webkit-transform: origin(50% 20%);
    transform: origin(50% 20%);
    transition: all 0.125s ease-in-out;
    content: '';
    display: block;
    margin-top: -2px;
    pointer-events: none;
    position: absolute;
    right: 10px;
    top: 50%;
}

.dropdown-select.open:after {
    -webkit-transform: rotate(-180deg);
    transform: rotate(-180deg);
}

.dropdown-select.open .list {
    -webkit-transform: scale(1);
    transform: scale(1);
    opacity: 1;
    pointer-events: auto;
}

.dropdown-select.open .option {
    cursor: pointer;
}

.dropdown-select.wide {
    width: 100%;
}

.dropdown-select.wide .list {
    left: 0 !important;
    right: 0 !important;
}

.dropdown-select .list {
    box-sizing: border-box;
    transition: all 0.15s cubic-bezier(0.25, 0, 0.25, 1.75), opacity 0.1s linear;
    -webkit-transform: scale(0.75);
    transform: scale(0.75);
    -webkit-transform-origin: 50% 0;
    transform-origin: 50% 0;
    box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.09);
    background-color: #fff;
    border-radius: 6px;
    margin-top: 4px;
    padding: 3px 0;
    opacity: 0;
    overflow: hidden;
    pointer-events: none;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 999;
    max-height: 250px;
    overflow: auto;
    border: 1px solid #ddd;
}

.dropdown-select .list:hover .option:not(:hover) {
    background-color: transparent !important;
}
.dropdown-select .dd-search{
  overflow:hidden;
  display:flex;
  align-items:center;
  justify-content:center;
  margin:5px 0;
}

.dropdown-select .dd-searchbox{
  width:90%;
  padding:0.5rem;
  border:1px solid #999;
  border-color:#999;
  border-radius:4px;
  outline:none;
  line-height: 1;
}
.dropdown-select .dd-searchbox:focus{
  border-color:#12CBC4;
}

.dropdown-select .list ul {
    padding: 0;
}

.dropdown-select .option {
    cursor: default;
    font-weight: 400;
    line-height: 2;
    outline: none;
    padding-left: 10px;
    padding-right: 25px;
    text-align: left;
    transition: all 0.2s;
    list-style: none;
    font-size: 9px;
}

.dropdown-select .option:hover,
.dropdown-select .option:focus {
    background-color: #f6f6f6 !important;
}

.dropdown-select .option.selected {
    font-weight: 600;
    color: #12cbc4;
}

.dropdown-select .option.selected:focus {
    background: #f6f6f6;
}

.dropdown-select a {
    color: #aaa;
    text-decoration: none;
    transition: all 0.2s ease-in-out;
}

.dropdown-select a:hover {
    color: #666;
}

</style>
<div class="container">
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
                  <!-- <strong>Whoops!</strong> There were some problems with your input.<br><br> -->
                  <ul>
                     @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
               @endif
               
               <div class="card-body border" style="background-color: #13b75229 !important;    padding: 10px 10px;">
                  
                     
                  
                  <form action="{{ route('excel.import') }}" method="POST" enctype="multipart/form-data">
                     @csrf
                  <div class="row">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <a class="btn btn-raised btn-blue" href="{{ route('download.products.sample') }}" style="padding:5px;float:right;">
                                    <i class="fa fa-file-excel-o"></i> Download Sample Excel
                                 </a> <br>
                                  <label class="form-label">Import Product Excel* (Supported Format: .xlsx, .xls)</label>
                                  <input type="file" class="form-control custom-file-input" name="products_file" style="opacity:1; background-color: #29c7315e;" accept=".xlsx, .xls">
                              </div>
                          </div>
                          <div class="col-md-12">
                           <div class="form-group">
                              <center>
                              <button type="submit" class="btn btn-primary">Import</button>
                              <center>
                           </div>
                           </div> 
                     </form></br> </br>
                  </div>
               </div>

               <form action="{{ route('medicinePurchaseInvoice.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                  @csrf
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">Supplier*</label>
                           <select class="form-control" name="supplier_id" id="supplier_id" required onchange="updateCreditSection();">
                              <option value="">Select Supplier</option>
                              @foreach ($suppliers as $id => $supplier)
                              <option value="{{ $supplier->supplier_id }}" data-credit-limit="{{ $supplier->credit_limit }}" data-current-credit="{{ $supplier->current_credit }}">
                                 {{ $supplier->supplier_name }}
                              </option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Invoice No*</label>
                           <input type="text" class="form-control" required name="invoice_no" maxlength="16" value="{{ old('invoice_no') }}" placeholder="Invoice No">
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Invoice Date*</label>
                           <input type="date" class="form-control" required name="invoice_date" id="invoice_date" onchange="updateDueDate()" maxlength="16" value="{{ old('invoice_date') ?: now()->format('Y-m-d') }}" placeholder="Invoice Date">

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
                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Due Date</label>
                           <input type="date" class="form-control"  name="due_date" id="due_date" placeholder="Due Date" value="{{ old('due_date') ?: now()->format('Y-m-d') }}">
                        </div>
                     </div>
                  </div>
                  <div class="row align-items-center" id="creditSection">
                     <div class="col-6">
                        <p><span><strong>Credit Limit:</strong></span><span id="creditLimitDisplay" name="credit_limit" style="color: green;"></span></p>
                        <p><span><strong>Current Credit:</strong></span> <span id="currentCreditDisplay" name="current_credit" style="color: green;">0%</span></p>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12 col-lg-12">
                        <div class="card">
                           <div class="table-responsive" style="min-height: 200px;">
                              <table class="table card-table table-vcenter text-nowrap" id="productTable">
                                 <thead>
                                    <tr>
                                       <th>Product Name</th>
                                       <th>Medicine Code</th>
                                       <th>Quantity</th>
                                       <th>Product Unit</th>
                                       <th>Product Rate</th>
                                       <th>Free Quantity</th>
                                       <th>Batch No</th>
                                       <th>Manufacture Date</th>
                                       <th>Expiry Date</th>
                                       <th>Tax Amount</th>
                                       <th>Amount</th>
                                       <th>Discount</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr id="productRowTemplate" style="display: none">
                                       <td>
                                       <select class="form-control medsearch" name="product_id[]" onchange="fetchMedicineDetails(this);">
                                          <option value="" selected disabled>Select medicine</option>
                                          @foreach($products as $product)
                                             <option value="{{ $product->id }}">{{ $product->medicine_name }}</option>
                                          @endforeach
                                       </select>


                                       </td>
                                       <td><input type="text" class="form-control" name="medicine_code[]" readonly></td>
                                       <td><input type="text" class="form-control" name="quantity[]" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></td>
                                       <td><input type="text" class="form-control" name="unit_id[]" readonly></td>
                                       <td><input type="text" class="form-control" name="rate[]"></td>
                                       <td><input type="text" class="form-control" name="free_quantity[]" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></td>
                                       <td><input type="text" class="form-control" name="batch_no[]"></td>
                                       <td><input type="date" class="form-control" name="mfd[]" value="{{ now()->toDateString() }}"></td>
                                       <td><input type="date" class="form-control" name="expd[]" value="{{ now()->toDateString() }}"></td>
                                       <td><input type="text" class="form-control" name="tax[]" readonly></td>
                                       <td><input type="text" class="form-control" name="amount[]" readonly ></td>
                                       <td><input type="text" class="form-control" name="discount[]" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></td>

                                     </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                  <div class="col-md-12">
                  <button class="btn btn-primary" id="addProductBtn">Add Product</button>
               </div>
                  </div>
                  <!-- ROW-1 CLOSED -->
                  <div class="pricecard">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Sub Total:</label>
                        <span><input type="text" class="form-control" readonly name="sub_total" id="sub_total" placeholder="Sub Total"></span>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Item-wise discount:</label>
                        <input type="text" class="form-control" readonly name="item_wise_discount" id="item_discount" placeholder="Item Wise Discount"  oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Bill discount:</label>
                        <input type="text" class="form-control" name="bill_discount" id="bill_discount" placeholder="Bill Discount"  oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Total Tax:</label>
                        <input type="text" class="form-control" readonly name="total_tax" id="total_tax" placeholder="Tax">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">CGST:</label>
                        <input type="text" class="form-control" readonly name="cgst" id="cgst" placeholder="CGST">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">SGST:</label>
                        <input type="text" class="form-control" readonly name="sgst" id="sgst" placeholder="SGST">
                     </div>
                  </div>

                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">IGST:</label>
                        <input type="text" class="form-control" readonly name="igst" id="igst" placeholder="IGST">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Round Off:</label>
                        <input type="text" class="form-control" name="round_off" id="round_off" placeholder="Round Off"  oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Total:</label>
                        <input type="text" class="form-control" readonly name="total_amount" id="total" placeholder="Total">
                     </div>
                  </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <div class="form-label">Amount Paid</div>
                           <label class="custom-switch">
                              <!-- Hidden field for false value -->
                              <input type="hidden" name="is_paid" value="0">
                              <input type="checkbox" id="is_paid" name="is_paid" onchange="toggleStatus(this)" class="custom-switch-input" checked value="1">
                              <span id="statusLabel" class="custom-switch-indicator"></span>
                              <span id="statusText" class="custom-switch-description">paid</span>
                           </label>
                        </div>
                     </div>
                  </div>
                  <div id="payment_fields" style="display: none;">
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="form-label">Paid Amount</label>
                              <input type="text" class="form-control" name="paid_amount" maxlength="16" value="{{ old('paid_amount') }}" placeholder="Paid Amount"  oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                           </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="form-label">Deposit To</label>
                              <select class="form-control" name="deposit_to" id="deposit_to">
                                 <option value="">Deposit To</option>
                              </select>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="form-label">Reference Code</label>
                              <input type="text" class="form-control" name="reference_code" maxlength="16" value="{{ old('reference_code') }}" placeholder="Reference Code">
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <center>
                        <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Save</button>
                        <a class="btn btn-danger" href="">Close</a>
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
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
            return false; // Exit the loop early if an invalid amount is found1`
      });

      return isValid;
   }
</script>

<script>
   $(document).ready(function() {
      // Handle change event on the payment mode dropdown
      $('#payment_mode').change(function() {
         // Get the selected value
         var selectedPaymentMode = $(this).val();

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
   });
</script>
<script>
   $(document).ready(function() {
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
   });

   $(document).ready(function() {
   $('select[name="product_id[]"]').each(function() {
      var select = $(this);
      var medicineCode = select.closest('tr').find('input[name="medicine_code[]"]').val();
      var productUrl = '/get-product-id/' + encodeURIComponent(medicineCode);

      $.get(productUrl, function(productResponse) {
         productResponse.forEach(function(product) {
            var option = $('<option>', {
               value: product.product_id,
               text: product.product_id
            });

            select.append(option); // Fix: append the 'option' variable
         });
      });
   });
});

</script>
<script>
   function calculateTotals() {
      var subTotal = 0;
      var itemDiscount = 0;
      var billDiscount = parseFloat($("#bill_discount").val()) || 0;
      var totalTax = 0;
      var igst = 0;
      var roundOff = parseFloat($("#round_off").val()) || 0;

      // Loop through each row in the table
      $("#productTable tbody tr").each(function() {
         var amount = parseFloat($(this).find('input[name="amount[]"]').val()) || 0;
         subTotal += amount;

         var discount = parseFloat($(this).find('input[name="discount[]"]').val()) || 0;
         itemDiscount += discount;

         var tax = parseFloat($(this).find('input[name="tax[]"]').val()) || 0;
         totalTax += tax;

         var igstValue = parseFloat($(this).find('input[name="igst[]"]').val()) || 0;
         igst += igstValue;
      });

      // Update the "Sub Total" input field with the calculated subtotal
      $("#sub_total").val(subTotal.toFixed(2));

      // Update the "Item-wise Discount" input field with the calculated item discount
      $("#item_discount").val(itemDiscount.toFixed(2));

      // Update the "Total Tax" input field with the calculated total tax
      $("#total_tax").val(totalTax.toFixed(2));

      // Split total tax equally into CGST and SGST
      var cgst = totalTax / 2;
      var sgst = totalTax / 2;

      // Update the "CGST" and "SGST" input fields with the calculated values
      $("#cgst").val(cgst.toFixed(2));
      $("#sgst").val(sgst.toFixed(2));
      $("#igst").val(igst.toFixed(2));
      // Calculate the total amount
      var total = subTotal - itemDiscount - billDiscount + totalTax + cgst + sgst + igst + roundOff;

      // Update the "Total" input field with the calculated total
      $("#total").val(total.toFixed(2));
   }

   // Call the function initially and whenever the "amount[]" or "discount[]" values change
   $(document).ready(function() {
      calculateTotals();

      $("#productTable tbody").on("input", 'input[name="amount[]"], input[name="discount[]"], input[name="tax[]"]', function() {
         calculateTotals();
      });
   });
</script>
<script>
   function updateCreditLimit() {
      // Get the selected supplier_id
      var selectedSupplierId = $("#supplier_id").val();

      // Make an AJAX request to fetch the credit limit based on the selected supplier_id
      $.ajax({
         url: '/get-credit-details/' + encodeURIComponent(selectedSupplierId),
         type: 'GET',
         success: function(creditDetails) {
            // Update the content of the credit limit display element
            $("#creditLimitDisplay").text(creditDetails.creditLimit);
            $("#currentCreditDisplay").text('Current Credit: ' + creditDetails.currentCredit);

            console.log('Current Credit: ' + creditDetails.currentCredit);

            // Call the emptyOthers function
            emptyOthers();
         },
         error: function(error) {
            console.log(error);
         }
      });
   }

   function emptyOthers() {
      document.getElementById("invoice_date").value = '';
      document.getElementById("due_date").value = '';
   }
</script>


<script>
   function emptyOthers() {
      document.getElementById("invoice_date").value = '';
      document.getElementById("due_date").value = '';
   }

    $(document).ready(function() {
        $("#addProductBtn").on("click", function(event) {
            event.preventDefault();
            var newRow = $("#productRowTemplate").clone();
            newRow.removeAttr("style");
            newRow.find('input[type="text"]').val('');
            newRow.find('input[type="number"]').val('');
            // Append the new row to the table
            $("#productTable tbody").append(newRow);
        });
    });



   $(document).ready(function() {
      // Call the function initially to set the visibility based on the initial state
      togglePaymentFields();

      // Bind the function to the change event of the "is_paid" checkbox
      $("#is_paid").change(function() {
         togglePaymentFields();
      });


   });

   function togglePaymentFields() {
      var isPaidToggle = $("#is_paid");
      var paymentFields = $("#payment_fields");

      if (isPaidToggle.prop("checked")) {
         paymentFields.show(); // Show the payment fields
      } else {
         paymentFields.hide(); // Hide the payment fields
      }
   }

   function toggleStatus(checkbox) {
      if (checkbox.checked) {
         $("#statusText").text('Paid');
         $("#statusLabel").removeClass("custom-switch-indicator-danger");
         $("input[name=is_paid]").val(1); // Set the value to 1 when checked
      } else {
         $("#statusText").text('Not Paid');
         $("#statusLabel").addClass("custom-switch-indicator-danger");
         $("input[name=is_paid]").val(0); // Set the value to 0 when unchecked
      }
   }
</script>
<script>
   // Function to update credit section visibility and content
   function updateCreditSection() {
      var selectedSupplier = document.getElementById('supplier_id');
      var creditSection = document.getElementById('creditSection');
      var creditLimitDisplay = document.getElementById('creditLimitDisplay');
      var currentCreditDisplay = document.getElementById('currentCreditDisplay');

      // Check if a supplier is selected
      if (selectedSupplier.value !== "") {
         // Show the credit section
         creditSection.style.display = 'block';

         // Get the selected supplier's ID
         var supplierId = selectedSupplier.value;

         // Make an AJAX request to fetch credit-related data
         $.ajax({
            url: '{{ route("medicinePurchaseInvoice.getcreditinfo", ["supplierId" => "_supplierId_"]) }}'.replace('_supplierId_', supplierId),
            method: 'GET',
            success: function(data) {
               creditLimitDisplay.innerText = data.creditLimit;
               currentCreditDisplay.innerText = data.currentCredit;
            },
            error: function(error) {
               console.error('Error fetching data:', error);
            }
         });
      } else {
         creditSection.style.display = 'none';
      }
   }
</script>
<script>
    function fetchMedicineDetails(select) {
        var selectedProductId = $(select).val();
        $.ajax({
            url: '{{ route("getMedicineDetails", ["productId" => "_productId_"]) }}'.replace('_productId_', selectedProductId),
            method: 'GET',
            success: function (data) {
                var row = $(select).closest('tr');
                row.find('[name="medicine_code[]"]').val(data.medicine_code);
                row.find('[name="unit_id[]"]').val(data.unit_id);
                //row.find('[name="rate[]"]').val(data.unit_price);
                var currentDate = new Date().toISOString().split('T')[0];
               row.find('[name="mfd[]"]').val(currentDate);
               row.find('[name="expd[]"]').val(currentDate);
                row.find('[name="tax[]"]').val(data.tax_rate);
                
            var quantityInput = row.find('[name="quantity[]"]');
            var rateInput = row.find('[name="rate[]"]');
            var amountInput = row.find('[name="amount[]"]');
            // Event handler for quantity field
            quantityInput.on('keyup', function () {
                calculateAmount();
            });
            rateInput.on('keyup', function () {
                calculateAmount();
            });
            calculateAmount();
                
            //   row.find('[name="quantity[]"]').on('keyup', function () {
            //   var quantityValue = parseFloat($(this).val()) || 0;
            // //   var rateValue = parseFloat(data.unit_price) || 0;
            //  var rateValue = parseFloat(row.find('[name="rate[]"]').val()) || 0;
            //   var amountValue = quantityValue * rateValue;
            //   row.find('[name="amount[]"]').val(amountValue);
            // });
            // row.find('[name="quantity[]"]').keyup();
            
            function calculateAmount() {
                var quantityValue = parseFloat(quantityInput.val()) || 0;
                var rateValue = parseFloat(row.find('[name="rate[]"]').val()) || 0;
                var amountValue = quantityValue * rateValue;
                row.find('[name="amount[]"]').val(amountValue);
            }
            
            
            },
            error: function (error) {
                console.error('Error fetching data:', error);
            }
        });
    }
    // Attach an input event listener to the quantity input field
$('input[name="quantity[]"]').on('input', function () {
    // Get the entered value
    var enteredValue = $(this).val();
    var parsedValue = parseFloat(enteredValue);
    if (isNaN(parsedValue) || parsedValue < 0) {
        $(this).val('');
    }
});

</script>

<script>

   function create_custom_dropdowns() {
    $('select').each(function (i, select) {
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