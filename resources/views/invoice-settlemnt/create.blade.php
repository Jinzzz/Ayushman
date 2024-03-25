@extends('layouts.app')

@section('content')
<style>
input[type="checkbox"] {
    transform: scale(0.4); /* Adjust the scale factor to make it smaller or larger */
}
.checkbox-row {
    display: flex;
    align-items: center; /* Align items vertically */
}

.checkbox-row input[type="checkbox"] {
    margin-left: 5px; /* Adjust margin as needed */
}
#error-msg {
    display: none;
    color: red;
}
button.no-act {
    pointer-events: none;
}

</style>
<div class="container">
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Generate Invoice</h3>
                </div>
                <div class="col-lg-12" style="background-color: #fff;">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('invoice-settlemnt.store', ['id' => $invoice->id]) }}" id="addFm" method="POST" enctype="multipart/form-data"  onsubmit="return validateForm()">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label branch" id="branchLabel">Booking Reference Number</label>
                                    <input type="text" class="form-control" name="booking_reference_number" id="booking_reference_number" value="{{ $invoice->booking_reference_number }}" placeholder="Booking Reference Number" readonly>
                                    <input type="hidden" class="form-control" name="booking_id" id="booking_id" value="{{ $booking_id }}">
                                    <input type="hidden" class="form-control" name="branch_id" id="branch_id" value="{{ $invoice->branch_id }}">
                                    <input type="hidden" class="form-control" name="patient_id" id="patient_id" value="{{ $invoice->patient_id }}">
                                    <input type="hidden" class="form-control" name="booking_type_id" id="booking_type_id" value="{{ $invoice->booking_type_id }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label branch" id="branchLabel">Patient Name</label>
                                    <input type="text" class="form-control" name="patient_name" id="booking_reference_number" value="{{ $invoice->patient_name }}" placeholder="Patient Name" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Booking Date*</label>
                                    <input type="date" class="form-control" name="booking_date" id="booking_date" value="{{ $invoice->booking_date }}" readonly>
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Invoice Date*</label>
                                    <input type="date" class="form-control" name="invoice_date" id="invoice_date" value="{{ old('invoice_date') ? old('invoice_date') : date('Y-m-d') }}">
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Doctor Name</label>
                                    <input type="text" class="form-control" name="doctor_name" id="doctor_name" value="{{ $invoice->staff_name }}"readonly>
                                </div>
                            </div> 
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Consultation Fees</label>
                                    <input type="text" class="form-control" name="consultation_fee" id="consultation_fee" value="{{ $invoice->staff_booking_fee }}" placeholder="Company" readonly>
                                </div>
                            </div> 
                        </div>
                        <div class="row">
    
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Discount (%)</label>
                                <input type="number" class="form-control paid-amount" name="discount" id="discount" placeholder="Discount Percentage" data-discount="{{ $discount }}" >
                                <div id="error-msg" style="display: none; color: red;">Discount cannot be greater than {{ $discount }}</div>
                            </div>
                        </div>
                        
                            <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Discount Amount</label>
                                <input type="number" class="form-control discount" name="discount_amount" id="discount_amount" placeholder="Discount Amount" >
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Total Amount*</label>
                                <input type="number" class="form-control paid-amount" name="paid_amount" id="paid_amount" placeholder="Total Amount" required>
                            </div>
                        </div>
                    </div>

                    <div class="row cloneDiv" id="cloneDiv">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Paid Amount</label>
                            <input type="text" class="form-control paid-amount" name="amount[]" id="amount" placeholder="Paid Amount" >
                        </div>
                    </div>
         
                        <div class="col-md-3">
                           <div class="form-group">
                              <label for="payment-type" class="form-label">Payment Mode</label>
                              <select class="form-control payment_mode"  name="payment_mode[]" placeholder="Payment Mode" id="payment_mode" onchange="updateDepositTo()">
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
                              <select class="form-control" name="deposit_to[]" id="deposit_to">
                                 <option value="">Deposit To</option>
                              </select>
                           </div>
                        </div>
                   
                  
                        <div class="col-md-2">
                           <div class="form-group">
                              <label class="form-label">Reference Number</label>
                              <input type="text" class="form-control reference_no" name="reference_no[]" id="reference_no" placeholder="Reference Number">
                           </div>
                        </div>
                        <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Action</label>
                           <button type="button" class="btn btn-raised btn-danger removebtn no-act" id="removebtn" onclick="removeFn1(this)"> Remove </button>
                        </div>
                     </div>
                    </div>
                    <button type="button" class="btn btn-raised btn-primary" id="addRow" style="margin-left: 15px; " > Add New Row </button>
                               
                         </br>
                        <div class="form-group">
                            <center>
                                <button type="submit" id="submitForm" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Bill Generate
                                </button>
                                <a class="btn btn-success" href="{{ route('invoice-settlemnt.index') }}">Reset</a>
                             
                                <a class="btn btn-danger" href="{{ route('invoice-settlemnt.index') }}">Cancel</a>
                            </center>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
    var payNowCheckbox = document.getElementById("pay_now");
    var payNowDetails = document.getElementById("payNowDetails");

    payNowCheckbox.addEventListener("change", function() {
        if (payNowCheckbox.checked) {
            payNowDetails.style.display = "block";
        } else {
            payNowDetails.style.display = "none";
        }
    });
});

$(document).ready(function() {
      // Handle change event on the payment mode dropdown
 $(document).on('change', '.payment_mode', function() {
         // Get the selected value
         var selectedPaymentMode = $(this).val();
            //alert(selectedPaymentMode)
            var rowNew = $(this).closest(".cloneDiv");
            var dep = rowNew.find('select[name="deposit_to[]"]')
         // Make an AJAX request to fetch the ledger names based on the selected payment mode
         $.ajax({
            url: '{{ route("getLedgerNames1") }}',
            type: 'GET',
            data: {
               payment_mode: selectedPaymentMode
            },
            success: function(data) {
               // Clear existing options
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
    // Function to update total amount
    function updateTotalAmount() {
        var paidAmount = parseFloat($("#consultation_fee").val());
        var discount = parseFloat($("#discount").val());
        var discountLimit = parseFloat($("#discount").data("discount"));
       // var discountAmount = parseFloat($("#discount_amount").val());

        // Check if discount exceeds the limit
        if (!isNaN(discount) && discount > discountLimit) {
            $("#error-msg").show();
            // $("#discount").val(discountLimit);
            $("#discount").val(0);
            return;
        } else {
            $("#error-msg").hide();
        }

        // Calculate total amount after discount if discount is present
        var totalAmount = paidAmount;
        if (!isNaN(discount)) {
            totalAmount -= (paidAmount * (discount / 100));
            var x = paidAmount * (discount / 100);
            $("#discount_amount").val(x)
        }

        // Update total amount field
        $("#paid_amount").val(totalAmount.toFixed(2));
    }

    // Set initial value of total amount to paid amount
    $("#paid_amount").val($("#consultation_fee").val());

    // Listen for changes in paid amount and discount fields
    $("#discount").on("input", function() {
        updateTotalAmount();
    });
});

function removeFn1(parm) {
             // alert("test")
            var currentRow = $(parm).closest('#cloneDiv');
            // alert(currentRow)
            currentRow.remove();
             //$('#sub-butn').removeClass("disabled");
        }
    $(document).ready(function() { 
        $('#addRow').click(function(){
            event.preventDefault();
            var newRow = $("#cloneDiv").clone().removeAttr("style");
            // newRow.find('select').addClass('medicine-select');
            // newRow.find('input').val('').prop('readonly', false);
            // newRow.find('button').prop('disabled', false);
            // newRow.find('input span').remove();
            newRow.find('#removebtn').removeClass("no-act");
            newRow.find('select').val('');
            newRow.find('input').val('');
            // $("#productTable tbody").append(newRow);
            newRow.insertBefore("#addRow");
        });
    });
$(document).on('change', 'input[name="amount[]"]', function() {
   var Nrow = $(this).closest(".cloneDiv");
   if($(this).val()) {
       Nrow.find("select").prop('required', true);
   }
   else {
        Nrow.find("select").prop('required', false);
   }
   
});
function validateForm() {
    var isValid = true;
    var totalSum = 0;
    var noBill = false;
    
        var totalAmount = parseFloat($("#consultation_fee").val());
        totalSum += totalAmount;
   
   // alert(totalSum)
    var total = 0;
    var discount = parseFloat($("#discount").val()) || 0;
    
    $('input[name="amount[]"]').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
     var x = totalSum;
    if (discount !== 0) {
        totalSum = x * (1 - discount / 100); // Adjust total based on the discount
    }
    if (total !== totalSum) {
        alert("Total payable amount should be " + totalSum);
        isValid = false;
    }
    return isValid;
}
</script>
@endsection



