@extends('layouts.app')
@section('content')
    <style>
        .fa-eye:before {
            color: #fff !important;
        }
    </style>
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                <div class="card-header">
                    <h3 class="card-title">Advance Salary</h3>
                </div>
                <div class="card-body">
                    <a href="{{route('staff.advance-salary.create')}}" class="btn btn-block btn-info">
                        <i class="fa fa-plus"></i>
                        Add Advance Salary
                    </a>
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Salary<br>Month</th>
                                    <th class="wd-15p">Staff</th>
                                    <th class="wd-15p">Payed <br> Date</th>
                                    <th class="wd-15p">Branch</th>
                                    <th class="wd-15p">Payed Amount</th>
                                    <th class="wd-20p">Net Earnings</th>
                                    <th class="wd-15p">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($processDatas as $key => $processData)
                                    <tr id="">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{@$processData->salary_month}}</td>
                                        <td>{{@$processData->staff->staff_username}}</td>
                                        <td>{{@$processData->payed_date}}</td>
                                        <td>{{@$processData->branch->branch_name}}</td>
                                        <td>{{@$processData->paid_amount}}</td>
                                        <td>{{@$processData->net_earnings}}</td>
                                        <td>
                                            <a class="btn btn-primary" href="{{ route('staff.advance-salary.view', ['id' => @$processData->id]) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> View </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- TABLE WRAPPER -->
                </div>
                <!-- SECTION WRAPPER -->
            </div>
        </div>
    </div>
    <!-- ROW-1 CLOSED -->
@endsection
