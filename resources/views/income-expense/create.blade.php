@extends('layouts.app')
@section('content')
<style>
    .disabledC {
    pointer-events: none;
}
</style>
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Add Miscellaneous Income and Expense</h3>
            </div>
            <!-- Success message -->
            <div class="col-lg-12 card-background" style="background-color:#fff";>
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
               <form action="{{ route ('income-expense.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                                <label class="form-label" for="income_expense_type_id">Type</label>
                                <select class="form-control" name="income_expense_type_id" required="">
                                    <option value="">--Choose Type--</option>
                                    <option value="1">Income</option>
                                    <option value="2">Expense</option>
                                </select>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Date*</label>
                          <input type="date" class="form-control" required name="income_expense_date" placeholder="Date" value="{{ old('income_expense_date') ?: now()->toDateString() }}">

                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                                <label class="form-label" for="income_expense_ledger_id">Account*</label>
                                <select class="form-control" name="income_expense_ledger_id" required="">
                                    <option value="">Choose Account</option>
                                    @foreach($ledgerList as $ledgers)
                                    <option value="{{$ledgers->id}}" {{old('income_expense_ledger_id') == $ledgers->id ? 'selected' : ''}}>{{$ledgers->ledger_name}}</option>
                                    @endforeach
                                </select>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Amount*</label>
                           <input type="text" required class="form-control"  name="income_expense_amount" id="numericInput"   value="{{old('income_expense_amount')}}" placeholder="Amount" pattern="\d+(\.\d{0,2})?">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Transaction Mode*</label>
                           <select class="form-control" required name="transaction_mode_id" placeholder="Payment Mode" id="payment_mode" onchange="updateDepositTo()">
                            <option value="">--Select Mode--</option>
                            @foreach($payment_type as $id => $value)
                            <option value="{{ $id }}">{{ $value }}</option>
                            @endforeach
                         </select>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Transaction Account*</label>
                           <select class="form-control" required="" name="transaction_account_id" id="deposit_to">
                              <option value="">--Transaction Account--</option>
                           </select>
                        </div>
                     </div>
                     
                                             <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Branch*</label>
                            <select class="form-control" required name="branch" placeholder="Branch" id="branch">
                            <option value="">--Select Branch--</option>
                            @foreach($branchs as $branch)
                            <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                         </select>
                        </div>
                     </div>
                     <div class="col-md-6">
                           <label class="form-label">Reference</label>
                           <input type="text" class="form-control"  name="reference"  id="reference" value="{{old('reference')}}" placeholder="Reference">
                        </div>
                     
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Notes</label>
                           <input type="text" class="form-control"  name="notes"  id="notes" value="{{old('notes')}}" placeholder="Notes">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group errorFm">
                           <label class="form-label">Reference File (Supported Formats: .pdf, .doc, .docx. Max size: 2 MB)</label>
                            <input type="file" class="form-control" accept=".pdf, .doc, .docx" name="reference_file" >
                            
                            <div class="name">
                                @if ($errors->has('reference_file'))
                                    <span class="text-danger errbk">{{ $errors->first('reference_file') }}</span>
                                @endif
                             </div>
                        </div>
                     </div>
                  </div>
                  <!-- ... -->
                  <div class="form-group">
                     <center>
                        <button type="submit" class="btn btn-raised btn-primary" id="submitFm" >
                        <i class="fa fa-check-square-o"></i> Add</button>
                        <button type="reset" class="btn btn-raised btn-success">
                        <i class="fa fa-refresh"></i> Reset</button>
                        <a class="btn btn-danger" href="{{route('income-expense.index')}}"> <i class="fa fa-times"></i> Cancel</a>
                     </center>
                  </div>
            </div>
         </div>
         </form>
      </div>
   </div>
</div>
@endsection
@section('js')
<script>
   function toggleStatus(checkbox) {
       if (checkbox.checked) {
           $("#statusText").text('Active');
           $("input[name=is_active]").val(1); // Set the value to 1 when checked
       } else {
           $("#statusText").text('Inactive');
           $("input[name=is_active]").val(0); // Set the value to 0 when unchecked
       }
   }
</script>

<script>
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
               $('#deposit_to').append('<option value="">--Transaction Account--</option>');

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
      
      $(document).ready(function() {
          $('input[name="reference_file"]').on('change', function() {
            if($(this).val()){
                var fileSize = this.files[0].size;
            var maxSize = 2 * 1024 * 1024; 
        
            if (fileSize > maxSize) {
            //   $('#submitFm').prop('disabled', true);
            $('#submitFm').addClass("disabledC");
              if (!$('#error_message').length) {
                $('<div id="error_message" style="color: red;">File size exceeds 2 MB</div>').appendTo('.errorFm');
              }
            } else {
             $('#submitFm').removeClass("disabledC");
              $('#error_message').remove();
            }
            }
            else {
                $('#submitFm').removeClass("disabledC");
              $('#error_message').remove();
            }
            
          });
        });

</script>

<script>
    document.getElementById('numericInput').addEventListener('input', function(event) {
        let inputValue = event.target.value;
        inputValue = inputValue.replace(/[^0-9.]/g, '');
        inputValue = inputValue.replace(/(\..*)\./g, '$1');
        event.target.value = inputValue;
    });
    </script>
@endsection