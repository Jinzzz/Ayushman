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
                <form action="{{ route('current.stock.report') }}" method="GET" class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="medicine_code" class="form-label">Medicine Code:</label>
                                <input type="text" id="medicine_code" name="medicine_code" class="form-control"
                                value="{{ request()->input('medicine_code') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="medicine_name" class="form-label">Medicine Name:</label>
                                <input type="text" id="medicine_name" name="medicine_name" class="form-control"
                                value="{{ request()->input('medicine_name') }}">
                            </div>
                            <div class="col-md-4">
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
                                        <a class="btn btn-primary" href="{{ route('current.stock.report') }}"><i class="fa fa-times"
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
            <h3 class="card-title">Current Stock Report</h3>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table id="report" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th>SL.NO</th>
                            <th>Medicine<br>Code</th>
                            <th>Medicine<br>Name</th>
                            <th>Batch</th>
                            <th>Sales Rate</th>
                            <th>Purchase Rate</th>
                            <th>Pharmacy</th>
                            <th>Current<br>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach ($current_stocks as $key=>$current_stock)
                        <tr id="dataRow_{{ $current_stock->stock_id  }}">
                            <td>{{ $key+1 }}</td>
                            <td>{{@$current_stock->medicines['medicine_code']}}</td>
                            <td>{{@$current_stock->medicines['medicine_name']}}</td>
                            <td>Batch: {{$current_stock->batch_no }} <br>
                                MFD: {{$current_stock->mfd }} <br>
                                EXP: {{$current_stock->expd }}
                            </td>
                            <td>{{$current_stock->sale_rate }}</td>
                            <td>{{$current_stock->purchase_rate }}</td>
                            <td>{{@$current_stock->pharmacy['pharmacy_name']}}</td>
                            <td>{{$current_stock->current_stock }}</td>
                        </tr>
                    @endforeach
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
                title: 'Current Stocks Report',
                exportOptions: 
                {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'pdf',
                text: 'Export to PDF',
                title: 'Current Stocks Report',
                footer: true,
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                },
                customize: function(doc) {
                    doc.content[1].margin = [20, 0, 20, 0]; //left, top, right, bottom
                    doc.content.forEach(function(item) {
                        if (item.table) {
                            item.table.widths = ['auto', 'auto', '*', 'auto', 'auto', 'auto', '*', 'auto']; // Set width to auto for all columns
                        }
                    });
                }
            }
            ]
        });
    });
    </script>
    
@endsection


