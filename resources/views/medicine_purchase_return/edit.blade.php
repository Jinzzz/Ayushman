@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{$message}}</p>
                </div>
            @endif
            @if ($message = Session::get('error'))
                <div class="alert alert-danger">
                    <p>{{$message}}</p>
                </div>
            @endif
            <div class="card-header">
                <h3 class="card-title">Edit Medicine Purchase Return</h3>
            </div>
            <form action="{{ route('medicinePurchaseReturn.update', $medicinePurchaseReturn->purchase_return_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Supplier</label>
                            <input type="text" id="return-date" required name="supplier_id" id="supplier_id" class="form-control" value="{{ $medicinePurchaseReturn->supplier_name}}" readonly>

                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Purchase Invoice Id*</label>
                            <input type="text" id="return-date" required name="purchase_invoice_id" class="form-control" value="{{ $medicinePurchaseReturn->purchase_invoice_no}}" readonly> 
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Return Date*</label>
                            <input type="date" id="return-date" required name="return_date" class="form-control" value="{{ $medicinePurchaseReturn->return_date}}">
                        </div>
                        <div class="col-md-3">
                        <label class="form-label">Pharmacy*</label>

                                        <select class="form-control" name="pharmacy_id" id="pharmacy_id" required>
                                          <option value="">Select Pharmacy</option>
                                           @foreach ($pharmacies as $id => $branchName)
                                            <option value="{{ $id }}" {{ $id == $medicinePurchaseReturn->pharmacy_id ? 'selected' : '' }}>{{ $branchName->pharmacy_name }}</option>
                                            @endforeach
                                       </select>
                                      
                         
                        </div>

                    </div>

                    <!-- Details Table -->
                    <div class="card">
                        <div class="table-responsive">
                            <table id="productTable" class="table card-table table-vcenter text-nowrap">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">Product Name</th>
                                        <th class="wd-15p">Product Unit</th>
                                        <th class="wd-15p">Quantity</th>
                                        <th class="wd-15p">Return Quantity</th>
                                       <th class="wd-15p">Purchase Rate</th>
                                        <th class="wd-15p">Return Rate</th>
                                        <th class="wd-15p">Tax</th>
                                         <th class="wd-15p">Tax Amount</th>
                                        <th class="wd-15p">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($medicinePurchase as $medicine)
                           
                                    <tr id="productRowTemplate" class="product-row">
                                            <td>
                                                <select class="form-control" readonly name="product_id[]" style="background-image: none; pointer-events:none;  -webkit-appearance: none;   -moz-appearance: none;   appearance: none;">
                                                    @foreach($product as $id => $name)
                                                        @if($id == $medicine->product_id)
                                                            <option value="{{ $id }}" selected>{{ $name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                  <input type="hidden" class="form-control" name="return_rate[]" value="{{ $medicine->return_rate }}">
                                            </td>
                                            <td>
                                            <select class="form-control" readonly name="unit_id[]" style="background-image: none; pointer-events:none;  -webkit-appearance: none;   -moz-appearance: none;   appearance: none;">
                                                @foreach($unit as $id => $name)
                                                @if($id == optional($medicine)->unit_id)
                                                    <option value="{{ $id }}" {{ $id == optional($medicine)->unit_id ? 'selected' : 'disabled' }}>{{ $name }}</option>
                                                     @endif
                                                @endforeach
                                            </select>
                                        </td>
                                 
                                          <td><input type="text" class="form-control" name="quantity[]" value="{{ $medicine->quantity_id }}"> </td>
                                           
                                            <td><input type="number" class="form-control" name="return_quantity[]" value="{{ $medicine->return_quantity }}" max ="{{ $medicine->quantity_id }}">
                                            <input type="hidden" id="hd-val" value="{{ $medicine->return_quantity }}" ></td>
                                            <td> <input type="text"  value="{{ $medicine->unit_price }}" readonly></td> 
                                            <td>
                                                <input type="text" class="form-control" name="rate[]" value="{{ $medicine->return_rate }}">
                                                <input type="hidden" id ="hid-val"  value="{{ $medicine->return_rate }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="tax[]" value="{{ $medicine->tax_rate }}">
                                                <input type="hidden" id ="hid-val"  value="{{ $medicine->tax_rate }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="tax_amount[]" value="{{ $medicine->return_rate / 100 * $medicine->tax_rate  }}">
                                                <input type="hidden" id ="hid-val"  value="{{ $medicine->return_rate / 100 * $medicine->tax_rate }}">
                                            </td>
                                            <td>
                                                <button type="button" onclick="deleteRow(this)" class="btn-danger btn-sm">
                                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                                <label class="form-label">Sub Total</label>
                                <input type="text" class="form-control" name="sub_total" readonly>
                            </div>
                                <div class="col-md-6">
                                <label class="form-label">Total Tax</label>
                                <input type="text" class="form-control" name="total_tax" readonly>
                            </div>
                        <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">CGST:</label>
                        <input type="text" class="form-control" readonly name="cgst" id="cgst" placeholder="CGST">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">SGST:</label>
                        <input type="text" class="form-control" readonly name="sgst" id="sgst" placeholder="SGST">
                     </div>
                  </div>

                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">IGST:</label>
                        <input type="text" class="form-control" readonly name="igst" id="igst" placeholder="IGST">
                     </div>
                  </div>
                                <div class="col-md-6">
                                <label class="form-label">Total Amount</label>
                                <input type="text" class="form-control" name="total_amount" readonly>
                            </div>
                    <div class="row">
                        <div class="card">
                            <div class="col-md-6">
                                <label class="form-label">Notes/Reason*</label>
                                <textarea class="form-control" name="notes" placeholder="Notes/Reason" required="">{{$medicinePurchaseReturn->reason}}</textarea>
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
                                <input type="text" class="form-control" name="paid_amount" maxlength="16"  placeholder="Paid Amount" oninput="if(this.value.length === 1 && this.value === '0') { this.value = ''; } else { this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); }">
                                

                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label for="payment-type" class="form-label">Payment Mode</label>
                              <select class="form-control"  name="payment_mode" placeholder="Payment Mode" id="payment_mode" onchange="updateDepositTo()" >
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


                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-raised btn-primary">
                                <i class="fa fa-check-square-o"></i> Update
                            </button>
                            <a class="btn btn-danger ml-2" href="{{ route('medicinePurchaseReturn.index') }}">Close</a>
                        </div>
                    </div>
                </div>
                </div>

    
            </form>
        </div>
    </div>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        // Validate return quantity on input change
        $('input[name="return_quantity[]"]').on('input', function () {
            var quantity = parseFloat($(this).parents('td').siblings('td').find('input[name="quantity[]"]').val());
            var change = parseFloat($(this).val());
            //alert(quantity)
            if(change <= quantity) {
              //alert("t")
              var x = $(this).parents('td').siblings('td').find('#hid-val');
              var y = x.val();
              var singleVal = $(this).next('#hd-val').val();
              var z = y/singleVal;
            //alert(z)
              var newV = $(this).val();
             // alert(newV)
            //   if(newV == '' || newV == null || newV == undefined ){
            //     newV = singleVal
            //   }
            var d = $(this).parents('td').siblings('td').find('input[name="rate[]"]');
            var taxp = parseFloat($(this).parents('td').siblings('td').find('input[name="tax[]"]').val());
              var newTotal = newV * z ;
              //alert(newTotal)
             d.val(newTotal);
             
             var tax = newTotal / 100 * taxp; 
             $(this).parents('td').siblings('td').find('input[name="tax_amount[]"]').val(tax)
            }
            else {
                alert("should be less");
                $(this).parents('td').siblings('td').find('input[name="rate[]"]').val('')
            }





        });

    });
</script>
<script>
   $(document).ready(function () {
       // Add Product button click event
       $("#addProductBtn2").click(function (event) {
           event.preventDefault();
           var newRow = $("#productRowTemplate").clone();
           newRow.removeAttr("style");
           newRow.find('input[type="text"]').val('');
           newRow.find('input[type="number"]').val('');
           $("#productTable tbody").append(newRow);
       });

       // Event listener for deleting a row
       $(document).on('click', '.btn-danger', function () {
           $(this).closest('tr.product-row').remove();
       });
       
       $(document).on('click', '.btn-danger', function () {
           $(this).closest('tr.product-row').remove();
       });
       
    //   update Total fields
       changeValues()
       
    //   On return quantity change
       $(document).on('input', 'input[name="return_quantity[]"]', function () {
           changeValues()
       });
   });
     function deleteRow(param) {
        var currentRow = $(param).closest('tr');
        currentRow.remove();
        changeValues()
     }
     function changeValues() {
         //alert()
            var subTotal = 0;
            $('input[name="rate[]').each(function () {
                subTotal += parseFloat($(this).val()) || 0;
            });
             $('input[name="sub_total"]').val(subTotal.toFixed(2));
             
             var totalTax = 0;
            $('input[name="tax_amount[]').each(function () {
                totalTax += parseFloat($(this).val()) || 0;
            });
            
            
            
            
             $('input[name="total_tax"]').val(totalTax.toFixed(2));
             
                var cgst = totalTax / 2;
                var sgst = totalTax / 2;
                var igst = 0;
                // Update the "CGST" and "SGST" input fields with the calculated values
                $("#cgst").val(cgst.toFixed(2));
                $("#sgst").val(sgst.toFixed(2));
                $("#igst").val(igst.toFixed(2));
                
            $('input[name="total_amount"]').val((totalTax + subTotal).toFixed(2));
            $('input[name="paid_amount"]').val((totalTax + subTotal).toFixed(2));
     }
       
   //fetching purchase invoice id for a particular supplier:

   $(document).ready(function () {
        // Event listener for supplier selection
        $('#supplier_id').change(function () {
         // console.log('Supplier selected');
            var supplierId = $(this).val();

            // Make an AJAX request to fetch purchase invoices based on the selected supplier
            $.ajax({
                url: "{{ route('getPurchaseInvoices') }}", // Replace with your route for fetching purchase invoices
                type: 'GET',
                data: {
                    supplier_id: supplierId
                },
                success: function (data) {
                    // Populate the purchase invoice dropdown with the fetched data
                    var purchaseInvoiceDropdown = $('#purchase_invoice_id');
                    purchaseInvoiceDropdown.empty(); // Clear previous options
                    purchaseInvoiceDropdown.append('<option value="">Select Purchase Invoice</option>'); // Add default option

                    // Add options based on the fetched data
                    $.each(data, function (key, value) {
                        purchaseInvoiceDropdown.append('<option value="' + key + '">' + value + '</option>');
                    });
                },
                error: function () {
                    alert('An error occurred while fetching purchase invoices.');
                }
            });
        });
      });

      //fetching product details based on the purchase invoice number:
   
$(document).ready(function () {
    $('#purchase_invoice_id').change(function () {
        var purchaseInvoiceId = $(this).val();
        $.ajax({
            url: '/getPurchaseInvoiceDetails', 
            method: 'GET',
            data: { purchase_invoice_id: purchaseInvoiceId },
            success: function (response) {
                if (response.length > 0) {
                       for (var i = 0; i < response.length; i++) {
                        var newRow = $("#productRowTemplate").clone();
                        newRow.removeAttr("style");
                        newRow.find('select[name="product_id[]"]').val(response[i].product_id);
                        newRow.find('input[name="quantity[]"]').val(response[i].quantity);
                        newRow.find('select[name="unit_id[]"]').val(response[i].unit_id);
                        newRow.find('input[name="rate[]"]').val(response[i].rate);
                        newRow.find('input[name="free_quantity[]"]').val(response[i].free_quantity);
                        $('#productTable tbody').append(newRow);
                    }
                } else {
                }
            },
            error: function () {
                alert('Error fetching purchase invoice details.');
            }
        });
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
         
          $('#payment_fields input, #payment_fields select').prop('required', true);
      } else {
          $('#payment_fields input, #payment_fields select').prop('required', false);
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
      $(document).ready(function() {
      // Handle change event on the payment mode dropdown
      $('#payment_mode').change(function() {
         // Get the selected value
         var selectedPaymentMode = $(this).val();

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
   });
</script>


