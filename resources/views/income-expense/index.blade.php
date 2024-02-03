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
                    <h3 class="card-title">List Miscellaneous Income and Expense</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('income-expense.create') }}" class="btn btn-block btn-info">
                        <i class="fa fa-plus"></i>
                        Add Income/Expense
                    </a>
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Type</th>
                                    <th class="wd-15p">Date</th>
                                    <th class="wd-15p">Account</th>
                                    <th class="wd-15p">Amount</th>
                                    <th class="wd-20p">Transaction</th>
                                    <th class="wd-15p">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($incexpdata as $key => $rawdata)
                                    <tr id="">
                                        <td>{{ $key + 1 }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <a class="btn btn-primary btn-sm edit-custom" href=""><i
                                                    class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a>
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
