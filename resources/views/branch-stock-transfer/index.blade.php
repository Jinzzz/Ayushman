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
                    <h3 class="card-title">Stock Transfer to Pharmacies</h3>
                </div>
                <div class="card-body">
                    <a href="{{route('create.branch-stock-transfer')}}" class="btn btn-block btn-info">
                        <i class="fa fa-plus"></i>
                        Add Stock Transfer to Pharmacy
                    </a>
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Transfer<br>Date</th>
                                    <th class="wd-15p">Pharmacy From</th>
                                    <th class="wd-15p">Pharmacy To</th>
                                    <th class="wd-15p">Total Items</th>
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
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <a class="btn btn-primary" href=""><i class="fa fa-pencil-square-o" aria-hidden="true"></i> View </a>
                                            <a class="btn btn-danger" href=""><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Delete </a>
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
