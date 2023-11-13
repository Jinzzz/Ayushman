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
                        <!-- <strong>Whoops!</strong> There were some problems with your input.<br><br> -->
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('journel.entry.update') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        @csrf
                        <input type="hidden" value="{{ $journel_entries->journal_entry_id }}" name="hidden_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Select Journel Entry Type*</label>
                                    <select required class="form-control" name="journel_entry_type_id" id="journel_entry_type_id">
                                        <option value="">Choose Journel Entry Type</option>
                                        @foreach($journel_entry_types as $journel_entry_type)
                                        <option value="{{ $journel_entry_type->journal_entry_type_id }}" {{ $journel_entry_type->journal_entry_type_id == $journel_entries->journel_entry_type_id ? ' selected' : '' }}>{{ $journel_entry_type->journal_entry_type_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Date</label>
                                    <input type="text" class="form-control" value="{{ date('d-m-Y', strtotime($journel_entries->journel_date)) }}" readonly name="journel_date" placeholder="Date">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" name="notes" placeholder="Notes">{{$journel_entries->notes}}</textarea>
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
                                                    <th>Account</th>
                                                    <th>Description</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($all_entry_details as $entry_details)
                                                <tr id="productRowTemplate">
                                                    <td>
                                                        <select class="form-control " name="ledger_id[]">
                                                            <option value="">Please select account</option>
                                                            @foreach($ledgers as $ledger)
                                                            <option value="{{ $ledger->id }}"  {{ $ledger->id == $entry_details->account_ledger_id ? ' selected' : '' }}>{{ $ledger->ledger_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><textarea class="form-control" name="description[]" placeholder="Description">{{$entry_details->description}}</textarea></td>
                                                    <td><input type="number" class="form-control" value="{{$entry_details->debit}}" name="debit[]"></td>
                                                    <td><input type="number" readonly class="form-control" value="{{$entry_details->credit}}" name="credit[]"></td>
                                                    <td><button type="button" onclick="myClickFunction(this)" style="background-color: #007BFF; color: #FFF; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Remove</button></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" id="addProductBtn">Add Row</button>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Total Debit:</label>
                                    <input type="text" class="form-control" readonly name="total_debit" value="{{$journel_entries->total_debit}}" id="totalDebit" placeholder="Total Debit">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Total Credit:</label>
                                    <input type="text" class="form-control" readonly name="total_credit" value="{{$journel_entries->total_credit}}" id="totalCredit" placeholder="Total Credit">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <center>
                                <button type="submit" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Save</button>
                                <a class="btn btn-danger" href="{{ route('journel.entry.index') }}">Cancel</a>
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
    $(document).ready(function() {
        // Add an event listener for input changes in debit fields
        $("#productTable tbody").on("input", 'input[name="debit[]"]', function() {
            var debitValue = parseFloat($(this).val()) || 0;

            // Set the corresponding credit field to the same value
            $(this).closest("tr").find('input[name="credit[]"]').val(debitValue.toFixed(2)).prop('readonly', true);

            // Recalculate totals
            calculateTotals();
        });

        // Function to calculate and update total debit and credit
        function calculateTotals() {
            var totalDebit = 0;
            var totalCredit = 0;

            // Loop through each row in the table
            $("#productTable tbody tr").each(function() {
                var debitValue = parseFloat($(this).find('input[name="debit[]"]').val()) || 0;
                var creditValue = parseFloat($(this).find('input[name="credit[]"]').val()) || 0;

                totalDebit += debitValue;
                totalCredit += creditValue;
            });

            // Update the total debit and credit input fields
            $("#totalDebit").val(totalDebit.toFixed(2));
            $("#totalCredit").val(totalCredit.toFixed(2));
        }
        $("#addProductBtn").click(function(event) {
            event.preventDefault();
            // Clone the product row template
            var newRow = $("#productRowTemplate").clone();
            // Remove the "style" attribute to make the row visible
            newRow.removeAttr("style");
            newRow.find('select').val('');
            newRow.find('textarea').val('');
            newRow.find('input[type="text"]').val('');
            newRow.find('input[type="number"]').val('');
            newRow.removeAttr('style')
            // Append the new row to the table
            $("#productTable tbody").append(newRow);
            // Recalculate totals after adding a new row
            calculateTotals();
        });

    });

    function myClickFunction(bt) {
        var x = bt.parentNode.parentNode
        x.remove()
    }
</script>
@endsection