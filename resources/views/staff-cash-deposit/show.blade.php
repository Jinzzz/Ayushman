@extends('layouts.app')
@section('content')
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">View Cash Deposit Transfer Detils</h3>
                    </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Transfer Date*</label>
                                        <input type="text" class="form-control" readonly name="transfer_date"
                                            id="transfer_date" placeholder="Date" value="{{ $processDatas->transfer_date}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Branch*</label>
                                        <input type="text" class="form-control" readonly name="branch_id"
                                            id="branch_id" placeholder="Date" value="{{ $processDatas->transfer_date}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Reference Number</label>
                                        <input type="text" class="form-control" name="reference_number"
                                            id="reference_number" placeholder="Reference Number"  value="{{ $processDatas->reference_number}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Account From*</label>
 
                                        <input type="text" class="form-control" name="transfer_from_account"
                                            id="transfer_from_account" placeholder="Reference Number"  value="{{ $processDatas->transfer_date}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Account To*</label>
                                        <input type="text" class="form-control" name="transfer_to_account"
                                            id="transfer_to_account" placeholder="Reference Number"  value="{{ $processDatas->transfer_date}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Amount*</label>
                                        <input type="text" class="form-control numericInputvalue" required name="transfer_amount"
                                            id="transfer_amount" placeholder="Amount" value="{{ $processDatas->transfer_amount}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Remarks</label>
                                        <textarea class="form-control" name="remarks" placeholder="Remarks" readonly>{{ $processDatas->remarks}}</textarea>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="form-group">
                                <center>s
                                    <a class="btn btn-danger" href="{{ route('staff.cash.deposit.index') }}"> <i class="fa fa-times"></i>
                                        Bank</a>
                                </center>
                            </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('js')
@endsection


