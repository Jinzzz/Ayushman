@extends('layouts.app')

@section('content')
<style>
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
</style>
<!-- exports starts -->
<link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- exports ends -->
    <style>
        .fa-eye:before {
            color: #fff !important;
        }
    </style>
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <button class="btn btn-blue displayfilter"><i class="fa fa-filter" aria-hidden="true"></i>
                <span>Show Filters</span></button>
            <div class="card displaycard ShowFilterBox">
                <div class="card-header">
                    <h3 class="card-title">Search Reports</h3>
                </div>
                <form action="{{ route('sales.report') }}" method="GET" class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="invoice_from_date" class="form-label">Invoice From:</label>
                                <input type="date" class="form-control" name="invoice_from_date" id="invoice_from_date" value="{{request()->invoice_from_date}}">
                            </div>
                            <div class="col-md-6">
                                <label for="invoice_to_date" class="form-label">Invoice To:</label>
                                <input type="date" class="form-control" name="invoice_to_date" id="invoice_to_date" value="{{request()->invoice_to_date}}">
                            </div>
                            <div class="col-md-6">
                                <label for="sales_invoice_number" class="form-label">Invoice No:</label>
                                <input type="text" id="sales_invoice_number" name="sales_invoice_number" class="form-control"
                                value="{{ request()->input('sales_invoice_number') }}">
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Pharmacy</label>
                                    <select class="form-control" name="pharmacy_id" id="pharmacy_id">
                                        <option value="">Select Pharmacy</option>
                                        @foreach ($pharmacy as $key => $pharmacies)
                                        <option value="{{$pharmacies->id}}" {{request()->input('pharmacy_id') == $pharmacies->id ? 'selected':''}}>{{$pharmacies->pharmacy_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i>
                                            Filter</button>&nbsp;
                                        <a class="btn btn-primary" href="{{ route('sales.report') }}"><i class="fa fa-times"
                                                aria-hidden="true"></i> Reset</a>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
        </form>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Sales Report</h3>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table id="report" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th>SL.NO</th>
                            <th>Invoice<br>Number</th>
                            <th>Invoice<br>Date</th>
                            <th>Pharmacy</th>
                            <th>Total<br>Items</th>
                            <th>Total<br>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach ($sales as $key=>$medicine_sales)
                            <tr id="dataRow_{{ $medicine_sales->sales_invoice_id }}">
                                <td>{{ $key+1 }}</td>
                                <td>{{$medicine_sales->sales_invoice_number}}</td>
                                <td>{{ \Carbon\Carbon::parse($medicine_sales->invoice_date)->format('d-m-Y') }} </td>
                                <td>{{$medicine_sales->pharmacy['pharmacy_name']}}</td>
                                <td>{{$medicine_sales->sales_invoice_details_count }}</td>
                                <td>{{$medicine_sales->total_amount }}</td>
                                
                                <td><a class="btn btn-primary btn-sm" href="{{ route('sales.report.detail', ['id' => $medicine_sales->sales_invoice_id]) }}">
                                    Detail
                                </a></td>
                            </tr>
                        @endforeach
                        {{ $sales->links() }}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- </div> --}}
    </div>
    <script>
    
        $(document).ready(function() {
       
        $('#report').DataTable({
            dom: 'Bfrtip',
            buttons: [
            {
                extend: 'excel',
                text: 'Export to Excel',
                title: 'Sales Report',
                exportOptions: 
                {
                    columns: [0,1,2,3,4,5]
                }
            },
            {
                extend: 'pdf',
                text: 'Export to PDF',
                title: 'Sales Report',
                footer: true,
                orientation : 'landscape',
                pageSize : 'LEGAL',
                exportOptions: 
                {
                    columns: [0,1,2,3,4,5],
                    alignment: 'right',
                },
                    customize: function(doc) {
                    doc.content[1].margin = [ 100, 0, 100, 0 ]; //left, top, right, bottom
                    doc.content.forEach(function(item) {
                    if (item.table) {
                        item.table.widths = [40, '*','*','*','*','*']
                    }
                    })
                    }
            }
            ]
        });
    });
    </script>
    
@endsection


