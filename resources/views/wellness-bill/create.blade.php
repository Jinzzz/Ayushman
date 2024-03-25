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
                    <form action="{{ route('wellness-bill.store', ['id' => $booking_id]) }}" id="addFm" method="POST" onsubmit="return validateForm()" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label branch" id="branchLabel">Booking Reference Number</label>
                                    <input type="text" class="form-control" name="booking_reference_number" id="booking_reference_number" value="{{ $invoice->booking_reference_number }}" placeholder="Booking Reference Number" readonly>
                                    <input type="hidden" class="form-control" name="booking_id" id="booking_id" value="{{ $booking_id }}">
                                    <input type="hidden" class="form-control" name="branch_id" id="branch_id" value="{{ $invoice->branch_id }}">
                                    <input type="hidden" class="form-control" name="patient_id" id="patient_id" value="{{ $invoice->patient_id }}">
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
                        <div class="col-md-6">
                            <div class="form-group checkbox-row" style="display:flex; align-items:center;">
                                <input type="checkbox" class="form-control" id="pay_now" name="pay_now" style="width:100%; max-width:30px;">
                               <strong> <label for="pay_now" style="margin-bottom:0;">Pay Now</label> </strong>
                            </div>
                        </div>
                      </div>
                      <div id="payNowDetails" style="display: none;">

                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group checkbox-row" style="display:flex; align-items:center;">
                                <input type="checkbox" class="form-control" id="no_bill" name="no_bill" style="width:100%; max-width:30px;">
                                <strong> <label for="no_bill" style="margin-bottom:0;">No Bill</label> </strong>
                            </div>
                        </div>
                      </div>
                      <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Paid Amount</label>
                            <input type="text" class="form-control paid-amount" name="paid_amount" id="paid_amount" maxlength="16" value="{{ $invoice->staff_booking_fee }}" readonly placeholder="Paid Amount">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Discount</label>
                            <input type="text" class="form-control discount" name="discount" id="discount" placeholder="Discount Percentage" data-discount="{{ $discount }}">
                            <div id="error-msg" style="display: none; color: red;">Discount cannot be greater than {{ $discount }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Total Amount</label>
                            <input type="text" class="form-control paid-amount" name="paid-amount" id="amount" placeholder="Total Amount" readonly>
                        </div>
                    </div>
                </div>


 

                                        
                <div class="row cloneDiv" id="cloneDiv">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Paid Amount</label>
                            <input type="text" class="form-control paid-amount" name="amount[]" id="amount" placeholder="Total Amount" >
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
                                <a class="btn btn-success" href="{{ route('wellness-bill.index') }}">Reset</a>
                             
                                <a class="btn btn-danger" href="{{ route('wellness-bill.index') }}">Cancel</a>
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
         var rowNew = $(this).closest(".row");
            var dep = rowNew.find('select[name="deposit_to[]"]')
         // Make an AJAX request to fetch the ledger names based on the selected payment mode
         $.ajax({
            url: '{{ route("getLedgerNames1") }}',
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

   document.addEventListener("DOMContentLoaded", function() {
    var discountInput = document.getElementById("discount");
    var maxDiscount = parseFloat(discountInput.getAttribute("data-discount"));
    var errorMsg = document.getElementById("error-msg");

    discountInput.addEventListener("input", function() {
        var enteredDiscount = parseFloat(discountInput.value);
        if (enteredDiscount > maxDiscount) {
            discountInput.value = maxDiscount; // Reset the value to the maximum discount
            errorMsg.style.display = "block"; // Display the error message
        } else {
            errorMsg.style.display = "none"; // Hide the error message
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    var paidAmountInput = document.getElementById("paid_amount");
    var discountInput = document.getElementById("discount");
    var totalAmountInput = document.getElementById("amount");
    var errorMsg = document.getElementById("error-msg");
    var maxDiscount = parseFloat(discountInput.getAttribute("data-discount"));

    // Function to calculate total amount
    function calculateTotalAmount() {
        var paidAmount = parseFloat(paidAmountInput.value);
        var discount = parseFloat(discountInput.value);
        if (isNaN(discount) || discount === 0) {
            totalAmountInput.value = paidAmount.toFixed(2);
        } else {
            var totalAmount = paidAmount - (paidAmount * discount / 100);
            totalAmountInput.value = totalAmount.toFixed(2);
        }
    }

    // Autofill amount field with paid amount on page load
    calculateTotalAmount();

    // Event listener for discount input
    discountInput.addEventListener("input", function() {
        var enteredDiscount = parseFloat(discountInput.value);
        if (enteredDiscount > maxDiscount) {
            discountInput.value = maxDiscount;
            errorMsg.style.display = "block";
        } else {
            errorMsg.style.display = "none";
        }
        calculateTotalAmount(); // Update total amount when discount changes
    });
});

$(document).ready(function() {
        // Store original values when the page loads
        var originalPaidAmount = $("#paid_amount").val();
        var originalTotalAmount = $("#amount").val();

        $("#no_bill").change(function() {
            if ($(this).is(":checked")) {
                // Set the values of Paid Amount and Total Amount fields to 0 when unchecked
                $("#paid_amount").val("0");
                $("#amount").val("0");
                $("#discount").val("0");
            } else {
                // Restore original values when unchecked
                $("#paid_amount").val(originalPaidAmount);
                $("#amount").val(originalTotalAmount);

                // Show the payment details section and total amount row
                $("#paymentDetails").show(); 
                $("#totalAmountRow").show(); 
            }
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
            // newRow.find('#removebtn').removeClass("no-act");
            newRow.find('select').val('');
            newRow.find('input').val('');
            // $("#productTable tbody").append(newRow);
            newRow.insertAfter("#cloneDiv");
        });
    });

    $(document).on('change', 'input[name="amount[]"]', function() {
        var r = $(this).closest(".cloneDiv");
        if($(this).val()) {
            r.find("select").attr("required", true);
            
        }
        else {
            r.find("select").attr("required", false);
        }
    })

    function validateForm() {
    var isValid = false;
    var totalBookingFee = 0;
    var noBill = false;
    
    if ($('input[name="no_bill"]').prop('checked')) {
        noBill = true;
    } else {
        noBill = false;
    }
    
    // $('.product-row').each(function() {
        var bookingFee = parseFloat($("#paid_amount").val());
        totalBookingFee += bookingFee;
    // });
    
    var total = 0;
    var discount = parseFloat($("#discount").val()) || 0;
    
    $('input[name="amount[]"]').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
     var x = totalBookingFee;
    if (discount !== 0) {
        totalBookingFee = x * (1 - discount / 100); // Adjust total based on the discount
    }
//     alert(total);
//  alert(totalBookingFee)&&
//     alert(noBill)
    if (total !== totalBookingFee) {
        alert("Total payable amount should be " + totalBookingFee);
        isValid = false;
    }
    // else {
    //     alert("test")
    // }
    
    return isValid;
}

</script>
@endsection



