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
                        <h3 class="mb-0 card-title">Salary Processing</h3>
                    </div>
                    <!-- Success message -->
                    <div class="col-lg-12 card-background" style="background-color:#fff; padding: 10px;">
                            
                                <div class="card">
									<div class="card-header">
										<h3 class="card-title">STAFF NAME : {{@$salary_process->staff->staff_username}} | SALARY MONTH : {{@$salary_process->salary_month}} </h3>
										<div class="card-options">
											<a href="#" class="btn btn-primary btn-sm">Status:@if(@$salary_process->processing_status==1) Pending @else Paid @endif </a>
										</div>
									</div>
									<div class="card-body">
									
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
                                                <span class="form-label">Account Ledger:{{@$salary_process->ledger->ledger_name}}</span>
                                            </div>
                                            </div>
                                            <div class="row">
                                            <div class="col-md-4">
                                                <span class="form-label">Reference Number:{{@$salary_process->reference_number}}</span>
                                            </div>
                                            </div>
                                           
                                             <div class="row"
                                style="    border: 1px solid #0d97c6;
                            margin: 10px 10px 0;background: #c5dfe84f;">
                                <div class="col-md-12" style="border-right: 1px solid #0d97c6;">
                                    <h2
                                        style="margin: 15px auto 0;text-align:center;font-size:16px;font-weight:500;border-bottom: 1px solid #0d97c6;
                                    padding-bottom: 10px;">
                                        Earnings</h2>
                                        @php
                                        
                                        @endphp
                                     @foreach (@$salary_process->details as $detail)
                                         @if(@$detail->salary_head->salary_head_type==1)
                                            <div class="form-group earnings" style="margin-top: 1rem">
                                                <div class="row">
                                                    <div class="col-4"><label
                                                            class="form-label">{{ @$detail->salary_head->salary_head_name }}</label>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control earning_head" required
                                                            name="earning_head" placeholder="" value="{{ $detail->amount }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                   
                                    <div class="form-group" style="margin-top: 1rem">
                                        <div class="row">
                                            <div class="col-4"><label class="form-label">Bonus</label>
                                            </div>
                                            <div class="col-8">
                                                <input type="text" class="form-control bonus" required name="bonus"
                                                    placeholder="Bonus" value="{{@$salary_process->bonus}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-top: 1rem">
                                        <div class="row">
                                            <div class="col-4"><label class="form-label">Overtime</label>
                                            </div>
                                            <div class="col-8">
                                                <input type="text" class="form-control bonus" required name="overtime"
                                                    placeholder="Overtime"  readonly value="{{@$salary_process->overtime_allowance}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-top: 1rem">
                                        <div class="row">
                                            <div class="col-4"><label class="form-label">Other Earnings</label>
                                            </div>
                                            <div class="col-8">
                                                <input type="text" class="form-control other_earnings" required
                                                    name="other_earnings" placeholder="Other Earnings" value="{{@$salary_process->other_earnings}}"  readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <h2
                                        style="margin: 15px auto 0;text-align:center;font-size:16px;font-weight:500;border-bottom: 1px solid #0d97c6;
                                    padding-bottom: 10px;">
                                        Deductions</h2>
                                      @foreach (@$salary_process->details as $detail)
                                     
                                         @if(@$detail->salary_head->salary_head_type==2)
                                            <div class="form-group earnings" style="margin-top: 1rem">
                                                <div class="row">
                                                    <div class="col-4"><label
                                                            class="form-label">{{ @$detail->salary_head->salary_head_name }}</label>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control earning_head" required
                                                            name="earning_head" placeholder="" value="{{ $detail->amount }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                  
                                    <div class="form-group" style="margin-top: 1rem">
                                        <div class="row">
                                            <div class="col-4"><label class="form-label">LOP</label>
                                            </div>
                                            <div class="col-8">
                                                <input type="text" class="form-control lop" required name="lop"
                                                    placeholder="LOP" value="{{@$salary_process->lop}}"  readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-top: 1rem">
                                        <div class="row">
                                            <div class="col-4"><label class="form-label">Other Deductions</label>
                                            </div>
                                            <div class="col-8">
                                                <input type="text" class="form-control other_deductions" required
                                                    name="other_deductions" placeholder="Other Deductions" value="{{@$salary_process->other_deductions}}"  readonly>
                                            </div>
                                        </div>
                                    </div>



                                </div>
                            </div>
                            <div class="row"
                                style="border: 1px solid #0d97c6;
                            margin: 0 10px 10px;
                            background: #c5dfe84f;">
                                <div class="col-6" style="border-right: 1px solid #0d97c6;">
                                    <div class="form-group" style="margin-top: 1rem">
                                        <div class="row">
                                            <div class="col-4"><label class="form-label">Total Earnings</label>
                                            </div>
                                            <div class="col-8">
                                                <input type="text" class="form-control total_earnings" required
                                                    name="total_earnings" placeholder="Total Earnings" id="total_earning" value="{{@$salary_process->total_earnings}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group" style="margin-top: 1rem">
                                        <div class="row">
                                            <div class="col-4"><label class="form-label">Total Deductions</label>
                                            </div>
                                            <div class="col-8">
                                                <input type="text" class="form-control total_deductions" required
                                                    name="total_deductions" placeholder="Total Deductions" id="total_deductions" value="{{@$salary_process->total_deductions}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row" style="    border: 1px solid #0d97c6;
                            margin: 10px 10px 0;background: #c5dfe84f;">
                                <div class="col-12">
                                    <div class="form-group" style="margin-top: 1rem">
                                        <div class="row">
                                            <div class="col-6"><label class="form-label">Net Earnings(Total Earnings - Total Deductions)</label>
                                            </div>
                                            <div class="col-6">
                                                <input type="text" class="form-control net_earnings" required
                                                    name="net_earnings" placeholder="Net Earnings" value="{{@$salary_process->net_earning}}" readonly>
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
                                            <a class="btn btn-danger" href="{{ route('salary-processing.index') }}"> <i class="fa fa-times"></i>
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
