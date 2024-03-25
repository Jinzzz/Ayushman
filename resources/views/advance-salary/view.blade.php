@extends('layouts.app')
@section('content')

    <style>
        .form-control[readonly] {
            background-color: #c7c7c7 !important;
        }

        .page input[type=text][readonly] {
            background-color: #c7c7c7 !important;
        }

        .form-group .last-row {
            border-top: 1px solid #0d97c6;
            padding-top: 15px;
        }
       

    </style>
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">Advance Salary</h3>
                    </div>
                    <!-- Success message -->
                    <div class="col-lg-12 card-background" style="background-color:#fff; padding: 10px;">
                            
                                <div class="card">
									<div class="card-header">
										<h3 class="card-title">STAFF NAME : {{@$salary_process->staff->staff_username}} | SALARY MONTH : {{@$salary_process->salary_month}} </h3>
										<div class="card-options">
											
										</div>
									</div>
									<div class="card-body">
									     <div class="row">
                                            <div class="col-md-4">
                                                <span class="form-label">Payment Date:{{@$salary_process->payed_date}}</span>
                                            </div>
                                            </div>
									
                                            <div class="row">
                                            <div class="col-md-4">
                                                <span class="form-label">Branch:{{@$salary_process->branch->branch_name}} </span>
                                            </div>
                                            </div>
                                            <div class="row">
                                            <div class="col-md-4">
                                                <span class="form-label">Payment Mode:{{@$salary_process->paymentmode->master_value}}</span>
                                            </div>
                                             </div>
                                             <div class="row">
                                            <div class="col-md-4">
                                                <span class="form-label">Paid through Mode:{{@$salary_process->payedthroughmode->master_value}}</span>
                                            </div>
                                             </div>
                                            <div class="row">
                                            <div class="col-md-4">
                                                <span class="form-label">Account Ledger:{{@$salary_process->ledger->ledger_name}}</span>
                                            </div>
                                            </div>
                                            <div class="row">
                                            <div class="col-md-4">
                                                <span class="form-label">Reference Number:{{@$salary_process->reference_number}}</span>
                                            </div>
                                            </div>
                                            <div class="row">
                                            <div class="col-md-4">
                                                <span class="form-label">Remarks:{{@$salary_process->remarks}}</span>
                                            </div>
                                            </div>
                                           
                                             
                            
                             <div class="row" style="    border: 1px solid #0d97c6;
                            margin: 10px 10px 0;background: #c5dfe84f;">
                                <div class="col-12">
                                    <div class="form-group" style="margin-top: 1rem">
                                        <div class="row">
                                            <div class="col-6"><label class="form-label">Net Earnings</label>
                                            </div>
                                            <div class="col-6">
                                                <input type="text" class="form-control net_earnings" required
                                                    name="net_earnings" placeholder="Net Earnings" value="{{@$salary_process->net_earnings}}" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><label class="form-label">Total Paid</label>
                                            </div>
                                            <div class="col-6">
                                                <input type="text" class="form-control net_earnings" required
                                                    name="net_earnings" placeholder="Net Earnings" value="{{@$salary_process->paid_amount}}" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><label class="form-label">Remaing Amount</label>
                                            </div>
                                            <div class="col-6">
                                                <input type="text" class="form-control net_earnings" required
                                                    name="net_earnings" placeholder="Net Earnings" value="{{@$salary_process->net_earnings-@$salary_process->paid_amount}}" readonly>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            
                                 <br> <br> 
                            
                             <div class="row" style="margin-top:20px;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <center>
                                            <a class="btn btn-danger" href="{{ route('advance-salary.index') }}"> <i class="fa fa-times"></i>
                                                Back</a>
                                        </center>
                                    </div>
                                </div>
                            </div>
                                            
                                        
                                        
									</div>
                                </div>

                    </div>
                </div>
            </div>
        </div>
            
       

@endsection
