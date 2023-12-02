@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Search Staff</h3>
            </div>
            <form action="{{ route('sales.report') }}" method="GET">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="patient_name" class="form-label">Patient Name:</label>
                            <input type="text" id="patient_name" name="patient_name" class="form-control" value="{{ request('patient_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="from_date" class="form-label">From Date:</label>
                            <input type="date" id="from_date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="to_date" class="form-label">To Date:</label>
                            <input type="date" id="to_date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>&nbsp;
                            <a class="btn btn-primary" href="{{ route('staffleave.index') }}"><i class="fa fa-times" aria-hidden="true"></i> Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th class="wd-15p">SL.NO</th>
                            <th class="wd-10p">Sales Order</th>
                            <th class="wd-10p">Date</th>
                            <th class="wd-10p">Branch</th>
                            <th class="wd-10p">Customer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach($data as $sale)
                        <tr id="dataRow_{{ $sale->patient_id }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ $sale->sales_order_no }}</td>
                            <td>{{ $sale->sales_order_date }}</td>
                            <td>{{ $sale->branch_name }}</td>
                            <td>{{ $sale->patient_name }}</td>
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

<script>
</script>