@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Create Invoice</h3>
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
                    <form action="{{ route('consultation_billing.generateisnvoice') }}" id="addFm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $consultation_id }}">
                        <input type="hidden" name="patient_id" value="{{ $data->patient_id }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label branch">Booking Reference</label>
                                    <input type="text" class="form-control" name="booking_reference_number" id="booking_reference_number"  value="{{ $data->booking_reference_number }}" readonly>
                                </div>
                            </div>
                           
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label branch">Invoice Date</label>
                                    <input type="date" class="form-control" name="invoice_date" id="invoice_date" value="{{ old('invoice_date') ?? now()->format('Y-m-d') }}" placeholder="Invoice Date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Booking Date</label>
                                    <input type="date" class="form-control" name="booking_date" id="booking_date" value="{{ $data->booking_date }}" readonly>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Patient Name</label>
                                    <input type="text" class="form-control" name="patient_name" id="patient_name" value="{{ $data->patient_name }}" readonly>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Patient Contact</label>
                                    <input type="text" class="form-control" name="patient_contact" id="patient_contact" value="{{ $data->patient_mobile }}"  readonly>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"> Amount</label>
                                    <input type="text" class="form-control" name="due_amount" id="due_amount" value="{{ $data->booking_fee }}" readonly>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                <label class="form-label">Deposit To</label>
                              <select class="form-control" name="deposit_to" id="deposit_to">
                                 <option value="">Deposit To</option>
                              </select>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                               
                              <label for="payment-type" class="form-label">Reference Code</label>
                              <input type="text" class="form-control" name="reference_code" id="reference_code" placeholder="Referenece Code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">                      
                              <label for="payment-type" class="form-label">Discount</label>
                              <input type="text" class="form-control" name="discount_percentage" id="discount" placeholder="Discount %" max="{{ $percentage }}" min="0" onkeyup="checkDiscount()">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                               
                              <label for="payment-type" class="form-label">Total Amount</label>
                              <input type="text" class="form-control" name="total_amount" id="total_amount" placeholder="Total Amount">
                                </div>
                            </div>
                            </div>
                        </div>
                        </br></br>
                        <div class="form-group">
                            <center>
                                <button type="submit" id="submitForm" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Generate Invoice
                                </button>
                            </center>
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
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
   $(document).ready(function() {
      $('#payment_mode').change(function() {
         var selectedPaymentMode = $(this).val();
         $.ajax({
            url: '{{ route("getLedgerNames") }}',
            type: 'GET',
            data: {
               payment_mode: selectedPaymentMode
            },
            success: function(data) {
               $('#deposit_to').empty();
               $('#deposit_to').append('<option value="">Deposit To</option>');
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
    function checkDiscount() {
        var discountInput = document.getElementById("discount");
        var maxDiscount = parseFloat(discountInput.getAttribute("max"));
        var enteredDiscount = parseFloat(discountInput.value);

        if (enteredDiscount > maxDiscount) {
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Discount cannot be greater than ' + maxDiscount + '%',
            });
            discountInput.value = maxDiscount;
        }

        var dueAmount = parseFloat(document.getElementById("due_amount").value) || 0;
        var discountInput = document.getElementById("discount");
        var discount = parseFloat(discountInput.value) || 0;

        var maxDiscount = parseFloat(discountInput.getAttribute("max")) || 100;
        discount = Math.min(discount, maxDiscount);

        var totalAmount = dueAmount - (dueAmount * discount / 100);
        totalAmount = totalAmount.toFixed(2);
        document.getElementById("total_amount").value = totalAmount
    }
</script>
@endsection