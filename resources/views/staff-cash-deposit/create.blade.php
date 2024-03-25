@extends('layouts.app')
@section('content')
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">Add Cash Deposit Transfer</h3>
                    </div>
                    <!-- Success message -->
                    <div class="col-lg-12 card-background" style="background-color:#fff";>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    <form action="{{ route('staff.cash.deposit.store') }}" id="addFm" method="POST" enctype="multipart/form-data">
                        @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Transfer Date*</label>
                                        <input type="date" class="form-control" required name="transfer_date"
                                            id="transfer_date" placeholder="Date" value="{{ old('transfer_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Branch*</label>
                                        <select class="form-control" required name="branch_id" id="branch_id">
                                            <option value="">--Select Branch--</option>
                                            @foreach ($branches as $id => $branche)
                                            <option value="{{ $branche->branch_id }}">{{ $branche->branch_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Reference Number</label>
                                        <input type="text" class="form-control" name="reference_number"
                                            id="reference_number" placeholder="Reference Number" value="{{ old('reference_number') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Account From*</label>
                                        <select class="form-control" required name="transfer_from_account" id="transfer_from_account">
                                            <option value="">--Select Account--</option>
                                            @php
                                                $groupedAccounts = $ledgerAccounts->groupBy('account_sub_group_id');
                                            @endphp
                                            @foreach ($groupedAccounts as $groupId => $accounts)
                                                <optgroup label="{{ $groupId == 5 ? 'Cash Accounts' : 'Bank Accounts' }}">
                                                    @foreach ($accounts as $ledgerAccount)
                                                        <option value="{{ $ledgerAccount->id }}">{{ $ledgerAccount->ledger_name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Account To*</label>
                                        <select class="form-control" required name="transfer_to_account" id="transfer_to_account">
                                            <option value="">--Select Account--</option>
                                            @php
                                                $groupedAccounts = $ledgerAccounts->groupBy('account_sub_group_id');
                                            @endphp
                                            @foreach ($groupedAccounts as $groupId => $accounts)
                                                <optgroup label="{{ $groupId == 5 ? 'Cash Accounts' : 'Bank Accounts' }}">
                                                    @foreach ($accounts as $ledgerAccount)
                                                        <option value="{{ $ledgerAccount->id }}">{{ $ledgerAccount->ledger_name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Amount*</label>
                                        <input type="text" class="form-control numericInputvalue" required name="transfer_amount"
                                            id="transfer_amount" placeholder="Amount" value="{{ old('transfer_amount') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Remarks</label>
                                        <textarea class="form-control" name="remarks" placeholder="Remarks"></textarea>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="form-group">
                                <center>
                                    <button type="submit" class="btn btn-raised btn-primary">
                                        <i class="fa fa-check-square-o"></i> Add</button>
                                    <button type="reset" class="btn btn-raised btn-success">
                                        <i class="fa fa-refresh"></i> Reset</button>
                                    <a class="btn btn-danger" href="{{ route('staff.cash.deposit.index') }}"> <i class="fa fa-times"></i>
                                        Cancel</a>
                                </center>
                            </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
@endsection
@section('js')
@endsection
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<!--<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Add event listener to the numeric input element
        $('#transfer_amount').on('input', function(event) {
            let inputValue = $(this).val();
            inputValue = inputValue.replace(/[^0-9.]/g, '');
            inputValue = inputValue.replace(/(\..*)\./g, '$1');
            $(this).val(inputValue);
        });
    });
</script>
<script>
//exclude ledger from dropdown
$(document).ready(function() {
    $('#transfer_from_account').change(function() {
        var selectedValue = $(this).val();
        $('#transfer_to_account option').show(); 
        if (selectedValue) {
            $('#transfer_to_account option[value="' + selectedValue + '"]').hide();
        }
        $('#transfer_to_account').val('');
    });
});
//set current date in transfer date
$(document).ready(function() {
    var currentDate = new Date().toISOString().slice(0,10);
    $('#transfer_date').val(currentDate);
});

</script>

