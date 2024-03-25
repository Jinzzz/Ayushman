@extends('layouts.app')
<style>
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
</style>
@section('content')
@php
use App\Models\Mst_Staff;
@endphp
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
                <p></p>
            </div>
            @endif
            <div class="card-header">
                <h3 class="card-title">Medicine Purchase Return</h3>
            </div>
            <form id="medicinePurchaseReturnForm" action="{{ route('medicinePurchaseReturn.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Supplier*</label>
                            <select class="form-control"  name="supplier_id" id="supplier_id"required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Purchase Invoice Id*</label>
                            <select class="form-control" name="purchase_invoice_id" id="purchase_invoice_id" required>
                                <option value="">Select Purchase Invoice</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Return Date*</label>
                            <input type="date" id="return-date" required name="return_date" class="form-control" value="{{ now()->toDateString() }}">
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
                             <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
                             @endif
                              @endforeach
                           </select>
                          
                           @else
                           @if(session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all")
                            <select class="form-control" name="pharmacy_id" id="pharmacy_id" required readonly>
                             <option value="{{ session('pharmacy_id') }}">{{ session('pharmacy_name') }}</option>
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
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <!-- An empty column that spans the entire width -->
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <!-- An empty column that spans the entire width -->
                        </div>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table id="productTable" class="table card-table table-vcenter text-nowrap">
                                <thead>
                                    <tr>

                                        <th class="wd-15p">Product Name</th>
                                        <th class="wd-15p">Quantity</th>
                                        <th class="wd-15p">Product Unit</th>
                                        <th class="wd-15p"> Purchase Rate</th>
                                        <th class="wd-15p">Return Quantity</th>
                                        <th class="wd-15p">Return Rate</th>
                                        <th class="wd-15p">Tax</th>
                                        <th class="wd-15p">Tax Amount</th>
                                        <th class="wd-15p">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="productRowTemplate" class="product-row" style="display: none">

                                        <td>
                                            <select class="form-control" readonly name="product_id[]" style="background-image: none; pointer-events:none;  -webkit-appearance: none;   -moz-appearance: none;   appearance: none;">
                                                @foreach($product as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control" name="quantity[]" readonly></td>
                                        <td> <select class="form-control" readonly name="unit_id[]" style="background-image: none; pointer-events:none;  -webkit-appearance: none;   -moz-appearance: none;   appearance: none;">
                                                @foreach($unit as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select></td>
                                        <td><input type="text" class="form-control" name="rate[]" readonly></td>
                                        <td><input type="text" class="form-control" name="return_quantity[]" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></td>
                                        <td><input type="text" class="form-control" name="return_rate[]" readonly></td>
                                        <td><input type="text" class="form-control" name="tax[]" readonly></td>
                                        <td><input type="text" class="form-control" name="tax_amount[]" readonly></td>
                                        <input type="hidden" class="form-control" name="mfd[]">
                                        <input type="hidden" class="form-control" name="expd[]"> 
                                       <input type="hidden" class="form-control" name="batch_no[]" >
                                        <td class="text-center">
                                            <button class="btn btn-danger" id="btnnew" onclick="removeThis(this)" type="button">Remove</button>
                                        </td>

                                    </tr>

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
                                <textarea class="form-control" name="notes" placeholder="Notes/Reason" required=""></textarea>
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
                                <input type="text" class="form-control" name="paid_amount" maxlength="16" value="{{ old('paid_amount') }}" placeholder="Paid Amount" oninput="if(this.value.length === 1 && this.value === '0') { this.value = ''; } else { this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); }">
                                

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

                            <input type="hidden" name="checked_product" id="checked_product">

                            <div class="form-group d-flex justify-content-end">
                                <button type="button" id="last-submit-button" onclick="submitForm()" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Save
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
<!-- ROW-1 CLOSED -->
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    function removeThis(param) {
        var currentRow = $(param).closest('tr');
        var removedReturnRate = parseFloat(currentRow.find('input[name="return_rate[]"]').val()) || 0;
        currentRow.remove();
        updateSubTotal(); // Update sub_total after removing a row
        function updateSubTotal() {
                var totalReturnRate = 0;
                var totalTax = 0;
                
                // Iterate through each row and update return_rate
                $('input[name="return_rate[]"]').each(function () {
                    totalReturnRate += parseFloat($(this).val()) || 0;
                });
                
                // Iterate through each tax input and calculate total tax
                $('input[name="tax[]"]').each(function () {
                    totalTax += parseFloat($(this).val()) || 0;
                });
                
                // Update the sub_total with totalReturnRate
                var subTotal = totalReturnRate;
                $('input[name="sub_total"]').val(subTotal.toFixed(2));
                
                // Update the total_tax with totalTax
                $('input[name="total_tax"]').val(totalTax.toFixed(2));
                
                
                // Update the total_amount with totalTax + subTotal
                $('input[name="total_amount"]').val((totalTax + subTotal).toFixed(2));
                
                var cgst = totalTax / 2;
                var sgst = totalTax / 2;
                var igst = 0;
                // Update the "CGST" and "SGST" input fields with the calculated values
                $("#cgst").val(cgst.toFixed(2));
                $("#sgst").val(sgst.toFixed(2));
                $("#igst").val(igst.toFixed(2));

        }

    }
</script>
<script>
    $(document).ready(function() {
        // alert("tets")
        // Add Product button click event
        $("#addProductBtn2").click(function(event) {

            event.preventDefault();
            // alert("test");
            // Clone the product row template
            var newRow = $("#productRowTemplate").clone();

            // Remove the "style" attribute to make the row visible
            newRow.removeAttr("style");
            newRow.find('input[type="text"]').val('');
            newRow.find('input[type="number"]').val('');

            // Append the new row to the table
            $("#productTable tbody").append(newRow);
        });

    });

    $(document).ready(function() {
        $('#supplier_id').change(function() {
            var supplierId = $(this).val();
            $("#productTable tbody tr:not(:first)").remove();
            $.ajax({
                url: "{{ route('getPurchaseInvoices') }}",
                type: 'GET',
                data: {
                    supplier_id: supplierId
                },
                success: function(data) {
                    var purchaseInvoiceDropdown = $('#purchase_invoice_id');
                    purchaseInvoiceDropdown.empty();
                    purchaseInvoiceDropdown.append('<option value="">Select Purchase Invoice</option>');
                    $.each(data, function(key, value) {
                        purchaseInvoiceDropdown.append('<option value="' + key + '">' + value + '</option>');
                    });
                },
                error: function() {
                    alert('An error occurred while fetching purchase invoices.');
                }
            });
        });
    });
$(document).ready(function() {
    $('#purchase_invoice_id').change(function() {
        var purchaseInvoiceId = $(this).val();
        $.ajax({
            url: '/getPurchaseInvoiceDetails',
            method: 'GET',
            data: {
                purchase_invoice_id: purchaseInvoiceId
            },
            success: function(response) {
                if (response.length > 0) {
                    console.log(response);
                    $("#productTable tbody tr:not(:first)").remove();
                    var sum = 0;
                    for (var i = 0; i < response.length; i++) {

                        var newRow = $("#productRowTemplate").clone();
                        newRow.removeAttr("style");


                        newRow.find('select[name="product_id[]"]').val(response[i].product_id);
                        newRow.find('input[name="quantity[]"]').val(response[i].returnQty);
                        newRow.find('input[name="mfd[]"]').val(response[i].mfd);
                        newRow.find('input[name="expd[]"]').val(response[i].expd);
                        newRow.find('input[name="batch_no[]"]').val(response[i].batch_no);

                        // set return quantity max limit
                        newRow.find('input[name="return_quantity[]"]').attr('max', response[i].quantity);
                        
                        newRow.find('select[name="unit_id[]"]').val(response[i].unit_id);
                        var originalRate = response[i].rate;
                        var roundedRate = parseFloat(originalRate).toFixed(2);
                        newRow.find('input[name="rate[]"]').val(roundedRate);
                        newRow.find('input[name="tax[]"]').val(response[i].tax);
                        newRow.find('input[name="free_quantity[]"]').val(response[i].free_quantity);
                        $('#productTable tbody').append(newRow);
                        console.log(newRow.find('input[name="quantity[]"]').val());
                    }

                } else {
                    // Handle case when response is empty
                }

            },
            error: function() {
                alert('Error fetching purchase invoice details.');
            }
        });
    });
});

</script>
<script>
    function submitForm() {
    let form = document.getElementById('medicinePurchaseReturnForm');
    if (form.checkValidity()) {
        let values = new Array();
            $("input:checkbox[name=selected_products]:checked").each(function() {
                values.push($(this).val());
            });
    
            $("#checked_product").val(values);
    
            $("#medicinePurchaseReturnForm").submit();
        }
    else {
        form.reportValidity();
    }
    }
</script>
<script>
    $(document).ready(function () {
        $(document).on('input', 'input[name="return_quantity[]"]', function () {
            var thisRow = $(this).closest('tr');
            var returnQuantity = parseFloat($(this).val());
            var quantity = parseFloat(thisRow.find('input[name="quantity[]"]').val());
            
            if(returnQuantity <= quantity) {
               updateReturnRate(); 
            }
            else {
                var alertDiv = $('<div>').text('Return quantity must be less than quantity').addClass('alert-div');
                thisRow.append(alertDiv);
        
                setTimeout(function () {
                    alertDiv.remove();
                }, 1000);
                
                $(this).val('');
                thisRow.find('input[name="return_rate[]"]').val('');
                
            }
            
        });

        function updateReturnRate() {
            var totalReturnRate = 0;
            var totalTax = 0;
            $('input[name="return_quantity[]"]').each(function () {
                var currentRow = $(this).closest('tr');
                
                
                var currentRow = $(this).closest('tr');
                var returnQuantity = parseFloat($(this).val()) || 0;
                var quantity = parseFloat(currentRow.find('input[name="quantity[]"]').val());
                var purchaseRate = parseFloat(currentRow.find('input[name="rate[]"]').val()) || 0;
                var returnRate = parseFloat(returnQuantity * purchaseRate ); 
                currentRow.find('input[name="return_rate[]"]').val(returnRate.toFixed(2));
                var tax = parseFloat(currentRow.find('input[name="tax[]"]').val()) || 0;
                //var igst = parseFloat(currentRow.find('input[name="igst[]"').val()) || 0;
                
                 var taxp = (returnRate/100)*tax;
                 currentRow.find('input[name="tax_amount[]"]').val(taxp.toFixed(2))
                
                totalTax += taxp
                totalReturnRate += returnRate;
                
                  
                
            });
            $('input[name="sub_total"]').val(totalReturnRate.toFixed(2));
            $('input[name="total_tax"]').val(totalTax.toFixed(2));
            var cgst = totalTax / 2;
            var sgst = totalTax / 2;
            var igst = 0;
            // Update the "CGST" and "SGST" input fields with the calculated values
            $("#cgst").val(cgst.toFixed(2));
            $("#sgst").val(sgst.toFixed(2));
            $("#igst").val(igst.toFixed(2));
              
            $('input[name="total_amount"]').val((totalTax+totalReturnRate).toFixed(2));
            $('input[name="paid_amount"]').val((totalTax + totalReturnRate).toFixed(2)).prop('readonly', true);

            
            
            
            
            
        } 

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








