@extends('layouts.app')
@section('content')
    <style>
        .fa-eye:before {
            color: #fff !important;
        }

        .dt-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        div.dt-buttons .dt-button {
            background-color: #27c533;
            border: 1px solid #fff;
            border-radius: 5px;
            color: #fff;
        }
        .back-button{
            border:none;
            background-color: #27c533;
            border-radius:5px;
            padding:5px 20px;
            float:right;
            margin-bottom:15px;
            color:#fff;
            
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
                    <div class="col-md-6">
                        <h3 class="card-title">Stock Transfer History</h3>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        <a class="btn btn-raised btn-primary" href="{{ route('branch-transfer.index') }}">BACK</a>
                     </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Medicine Name</th>
                                    <th class="wd-15p"><br>Batch</th>
                                    <th class="wd-15p">Transfer Quantity</th>
                                    <th class="wd-15p">Transfer Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                    $i = 0;
                                @endphp
                                @foreach ($stockDetails as $key => $stockDetail)
                                    <tr id="">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{$stockDetail->medicine_name }}</td>
                                        <td>{{$stockDetail->batch_no }}</td>
                                        <td>{{$stockDetail->transfered_quantity }}</td>
                                        <td>{{$stockDetail->transfer_date }}</td>
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
