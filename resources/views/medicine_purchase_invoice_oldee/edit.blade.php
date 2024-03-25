@extends('layouts.app')
@section('content')
@php
use App\Helpers\AdminHelper;
// dd(AdminHelper::getProductId($value->medicine_code));
@endphp
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
                  <!-- <strong>Whoops!</strong> There were some problems with your input.<br><br> -->                  <ul>
                     @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
               @endif
               <form action="{{ route('excel.import') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <input type="file" name="products_file">
                  <button type="submit">Import</button>
               </form>
                <form action="{{ route('medicinePurchaseInvoice.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">Supplier*</label>
                           <select class="form-control" name="supplier_id" id="supplier_id"  required onchange="emptyOthers()">
                              <option value="">Select Supplier</option>
                              @foreach ($suppliers as $id => $supplier)
                              <option value="{{ $supplier->supplier_id }}" data-extra-value="{{ $supplier->credit_period }}">
                                 {{ $supplier->supplier_name }} 
                              </option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Invoice No*</label>
                           <input type="text" class="form-control" required name="invoice_no" maxlength="16"
                              value="{{ old('invoice_no') }}" placeholder="Invoice No">
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Invoice Date*</label>
                           <input type="date" class="form-control" required name="invoice_date"  id="invoice_date" onchange="updateDueDate()" 
                              maxlength="16" value="{{ $medicinePurchaseInvoice->invoice_date}}" placeholder="Invoice Date" >
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">Branch*</label>
                           <select class="form-control" name="branch_id" id="branch_id" required>
                              <option value="">Select Branch</option>
                              @foreach ($branch as $id => $branchName)
                              <option value="{{ $id }}">{{ $branchName }}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Due Date</label>
                           <input type="date" class="form-control" readonly name="due_date" id="due_date"
                              placeholder="Due Date" value="{{ $medicinePurchaseInvoice->due_date}}">
                        </div>
                     </div>
                  </div>
                  <div class="row" style="align-items:center">
                     <div class="col-6">
                        <p><span><strong>Credit Limit:</strong></span><span id="totalTaxRate"
                           style="color: green;"></span></p>
                        <p><span><strong>Current Credit:</strong></span> <span id="totalTaxRate"
                           style="color: green;">0%</span></p>
                     </div>
                     <div class="col-6">
                        <div class="row" style="align-items:center">
                           <div class="col-6">
                              <a class="btn btn-raised  btn-green"  href="{{route('download.products.sample')}}"><i class="fa fa-file-excel-o"></i> Download Sample Excel</a>
                           </div>
                           {{-- 
                           <div class="col-6">
                              <a class="btn btn-raised  btn-green"  href="{{route('excel.import')}}"><i class="fa fa-file-excel-o"></i> Import Excel</a>
                           </div>
                           --}}
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
                                       <th>Product Name</th>
                                       <th>Medicine Code</th>
                                       <th>Quantity</th>
                                       <th>Product Unit</th>
                                       <th>Product Rate</th>
                                       <th>Discount</th>
                                       <th>Free Quantity</th>
                                       <th>Batch No</th>
                                       <th>Manufacture Date</th>
                                       <th>Expiry Date</th>
                                       <th>Tax Amount</th>
                                       <th>Amount</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr id="productRowTemplate" style="display: none">
                                    <td>
                                        <select class="form-control" name="product_id[]" >
                                       @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{  $product->medicine_name}}</option>
                                        @endforeach
                                    </select>
                                     
                                    </td> 
                                       <td><input type="text" class="form-control" name="medicine_code[]"></td>
                                       <td><input type="text" class="form-control" name="quantity[]"></td>
                                       <td><input type="text" class="form-control" name="unit_id[]"></td>
                                       <td><input type="text" class="form-control" name="rate[]"></td>
                                       <td><input type="text" class="form-control" name="discount[]"></td>
                                       <td><input type="text" class="form-control" name="free_quantity[]"></td>
                                       <td><input type="text" class="form-control" name="batch_no[]"></td>
                                       <td><input type="date" class="form-control" name="mfd[]"></td>
                                       <td><input type="date" class="form-control" name="expd[]"></td>
                                       <td><input type="text" class="form-control" name="tax[]"></td>
                                       <td><input type="text" class="form-control" name="amount[]"></td>
                                      
                                    </tr>
                                    @if(isset($excelData))
                                    {{-- @php print_r($excelData); @endphp --}}
                                    
                                    @foreach($excelData as $data)
                                    @foreach($data as $key=>$value)
                                
                                    @if($key != 0)
                                    <tr id="productRowTemplate">
                                        <td>
                                            <select class="form-control" name="product_id[]">
                                                @foreach($products as $product)
                                                @php
                                                   $medicine_id=AdminHelper::getProductId($value[1])
                                                @endphp
                                                <option value="{{ $product->id}}" @if($product->id == $medicine_id) selected @endif>{{ $product->medicine_name }}</option>
                                            @endforeach
                                            </select>
                                        </td>
                                        
                                       <td><input type="text" class="form-control" name="medicine_code[]" value="{{ $value[1] }}"></td>
                                       <td><input type="number" class="form-control" name="quantity[]" value="{{ $value[2] ?? '' }}"></td>
                                       <td><input type="text" class="form-control" readonly name="unit_id[]" value="" data-medicine-code="{{ $value[1] }}"></td>
                                       <td><input type="text" class="form-control" name="rate[]" value="{{ $value[3] ?? '' }}"></td>
                                       <td><input type="text" class="form-control" name="discount[]" value="{{ $value[4] ?? '' }}"></td>
                                       <td><input type="text" class="form-control" name="free_quantity[]" value="{{ $value[5] ?? '' }}"></td>
                                       <td><input type="text" class="form-control" name="batch_no[]" value="{{ $value[6] ?? '' }}"></td>
                                       <td>
                                          <input type="date" class="form-control date-picker" name="mfd[]" value="{{ isset($value[7]) ? \Carbon\Carbon::parse($value[7])->format('Y-m-d') : '' }}">
                                       </td>
                                       <td>
                                          <input type="date" class="form-control date-picker" name="expd[]" value="{{ isset($value[8]) ? \Carbon\Carbon::parse($value[8])->format('Y-m-d') : '' }}">
                                       </td>
                                       <td><input type="text" class="form-control" readonly name="tax[]" value="{{ $value[9] ?? '' }}"></td>
                                       <td><input type="text" class="form-control" name="amount[]" value="{{ $value[10] ?? '' }}"></td>
                                       <td>
                                          {{-- condition checking --}}
                                          @php
                                          $calculatedAmount = ($value[2] * $value[3]) - ( $value[4] + $value[9]); // Quantity * Rate - (Discount + tax)
                                          $epsilon = 0.0001; // A small positive number to account for floating-point precision
                                          if (abs($calculatedAmount - $value[10]) > $epsilon) {
                                          echo '<span class="text-danger">Invalid Amount</span>';
                                          }
                                          @endphp
                                       </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                    @endforeach 
                                    @endif
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
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Sub Total:</label>
                        <span><input type="text" class="form-control" readonly name="sub_total" id="sub_total"
                           placeholder="Sub Total"></span>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group"> 
                        <label class="form-label">Item-wise discount:</label>
                        <input type="text" class="form-control" readonly name="item_wise_discount" id="item_discount"
                           placeholder="Item Wise Discount">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Bill discount:</label>
                        <input type="text" class="form-control"  name="bill_discount" id="bill_discount"
                           placeholder="Bill Discount" oninput="calculateTotals()">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Tax(CGST,SGST,IGST):</label>
                        <input type="text" class="form-control" readonly name="total_tax" id="total_tax"
                           placeholder="Tax">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Round Off:</label>
                        <input type="text" class="form-control"  name="round_off" id="round_off"
                           placeholder="Round Off" oninput="calculateTotals()">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Total:</label>
                        <input type="text" class="form-control" readonly name="total_amount" id="total"
                           placeholder="Total">
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <div class="form-label">Amount Paid</div>
                           <label class="custom-switch">
                              <!-- Hidden field for false value -->
                              <input type="hidden" name="is_paid" value="0">
                              <input type="checkbox" id="is_paid" name="is_paid"
                                 onchange="toggleStatus(this)" class="custom-switch-input" checked
                                 value="1">
                              <span id="statusLabel" class="custom-switch-indicator"></span>
                              <span id="statusText" class="custom-switch-description">paid</span>
                           </label>
                        </div>
                     </div>
                  </div>
                  <div id="payment_fields" style="display: none;">
                     <div class="row">
                        <div class="col-md-2">
                           <div class="form-group">
                              <label class="form-label">Paid Amount</label>
                              <input type="text" class="form-control" name="paid_amount" maxlength="16" value="{{ old('paid_amount') }}" placeholder="Paid Amount">
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group">
                              <label for="payment-type" class="form-label">Payment Mode</label>
                              <select class="form-control" required name="payment_mode" placeholder="Payment Mode">
                                 <option value="">--Select--</option>
                                 @foreach($paymentType as $id => $value)
                                 <option value="{{ $id }}">{{ $value }}</option>
                                 @endforeach
                              </select>
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="form-group">
                              <label class="form-label">Deposit To</label>
                              <input type="text" class="form-control" name="deposit_to" maxlength="16" value="{{ old('deposit_to') }}" placeholder="Deposit To">
                           </div>
                        </div>
                        <div class="col-md-2">
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
   $(document).ready(function () {
       $('input[readonly][data-medicine-code]').each(function () {
           var input = $(this);
           var medicineCode = input.data('medicine-code');
   
           // Fetch product_id and unit_id
           var productUrl = '/get-product-id/' + encodeURIComponent(medicineCode);
           var unitUrl = '/get-unit-id/' + encodeURIComponent(medicineCode);
   
           $.get(productUrl, function (productResponse) {
               // Set the value of the corresponding input field in the same row
               input.closest('tr').find('input[name="product_id[]"]').val(productResponse.product_id);
           });
   
           $.get(unitUrl, function (unitResponse) {
               // Set the value of the corresponding input field in the same row
               input.closest('tr').find('input[name="unit_id[]"]').val(unitResponse.unit_id);
           });
       });
   });

   $(document).ready(function () {
        // ... (your existing code)

        // Fetch product IDs and populate the dropdown
        $('select[name="product_id[]"]').each(function () {
            var select = $(this);

            // Fetch product IDs based on medicine code
            var medicineCode = select.closest('tr').find('input[name="medicine_code[]"]').val();
            var productUrl = '/get-product-id/' + encodeURIComponent(medicineCode);

            $.get(productUrl, function (productResponse) {
                // Populate the dropdown with product IDs
                productResponse.forEach(function (product) {
                    var option = $('<option>', {
                        value: product.product_id,
                        text: product.product_id
                    });

                    select.append(option);
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
     var roundOff = parseFloat($("#round_off").val()) || 0;
   
     // Loop through each row in the table
     $("#productTable tbody tr").each(function () {
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
   
   // Call the function initially and whenever the "amount[]" or "discount[]" values change
   $(document).ready(function () {
     calculateTotals();
   
     $("#productTable tbody").on("input", 'input[name="amount[]"], input[name="discount[]"]', function () {
         calculateTotals();
     });
   });
   
</script>
<script>
   function emptyOthers() {
       document.getElementById("invoice_date").value = '';
       document.getElementById("due_date").value = '';
   }
   
   function updateDueDate() {
       var supplierId = document.getElementById("supplier_id").value;
       var invoiceDateStr = document.getElementById("invoice_date").value;
       const selectElement = document.getElementById('supplier_id');
       const selectedOption = selectElement.options[selectElement.selectedIndex];
       const credit = selectedOption.getAttribute('data-extra-value');
   
       if (credit && invoiceDateStr) {
           const creditPeriod = parseInt(credit);
           const invoiceDate = new Date(invoiceDateStr);
   
           invoiceDate.setDate(invoiceDate.getDate() + creditPeriod);
           const year = invoiceDate.getFullYear();
           const month = String(invoiceDate.getMonth() + 1).padStart(2, '0');
           const day = String(invoiceDate.getDate()).padStart(2, '0');
           const dueDate = `${year}-${month}-${day}`;
   
           document.getElementById("due_date").value = dueDate;
       }
   }
   
   $(document).ready(function () {
       // Add Product button click event
       $("#addProductBtn").click(function (event) {
        event.preventDefault();
           // Clone the product row template
           var newRow = $("#productRowTemplate").clone();
   
           // Remove the "style" attribute to make the row visible
           newRow.removeAttr("style");
           newRow.find('input[type="text"]').val('');
           newRow.find('input[type="number"]').val('');
            newRow.removeAttr('style')
           // Append the new row to the table
           $("#productTable tbody").append(newRow);
       });
       
   });
   
   $(document).ready(function () {
        // Call the function initially to set the visibility based on the initial state
        togglePaymentFields();
   
        // Bind the function to the change event of the "is_paid" checkbox
        $("#is_paid").change(function () {
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
           $("input[name=is_active]").val(1); // Set the value to 1 when checked
       } else {
           $("#statusText").text('Not Paid');
           $("input[name=is_active]").val(0); // Set the value to 0 when unchecked
       }
   }
</script>
@endsection