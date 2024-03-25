@extends('layouts.app')

@section('content')
@php
use App\Models\Mst_Staff;
@endphp
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
                <form action="{{ route('stock.transfer.report') }}" method="GET" class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="transfer_from_date" class="form-label">From Date:</label>
                                <input type="date" class="form-control" name="transfer_from_date" id="transfer_from_date" value="{{request()->transfer_from_date}}">
                            </div>
                            <div class="col-md-6">
                                <label for="transfer_to_date" class="form-label">To Date:</label>
                                <input type="date" class="form-control" name="transfer_to_date" id="transfer_to_date" value="{{request()->transfer_to_date}}">
                            </div>
                            <div class="col-md-4">
                                <label for="transfer_code" class="form-label">Transfer Code:</label>
                                <input type="text" id="transfer_code" name="transfer_code" class="form-control"
                                value="{{ request()->input('transfer_code') }}">
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Pharmacy From</label>
                                    @if(Auth::check() && Auth::user()->user_type_id == 96)
                                       @php
                                        $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
                                        $mappedpharma = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
                                       @endphp
                                    <select class="form-control" name="from_pharmacy_id" id="from_pharmacy_id">
                                        <option value="">Select Pharmacy</option>
                                        @foreach ($pharmacy as $key => $pharmacies)
                                        @if(in_array($pharmacies->id, $mappedpharma))
                                        <option value="{{$pharmacies->id}}" {{request()->input('from_pharmacy_id') == $pharmacies->id ? 'selected':''}}>{{$pharmacies->pharmacy_name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @else
                                    <select class="form-control" name="from_pharmacy_id" id="from_pharmacy_id">
                                        <option value="">Select Pharmacy</option>
                                        @foreach ($pharmacy as $key => $pharmacies)
                                        <option value="{{$pharmacies->id}}" {{request()->input('from_pharmacy_id') == $pharmacies->id ? 'selected':''}}>{{$pharmacies->pharmacy_name}}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Pharmacy To</label>
                                    <select class="form-control" name="to_pharmacy_id" id="to_pharmacy_id">
                                        <option value="">Select Pharmacy</option>
                                        @foreach ($pharmacy as $key => $pharmacies)
                                        <option value="{{$pharmacies->id}}" {{request()->input('to_pharmacy_id') == $pharmacies->id ? 'selected':''}}>{{$pharmacies->pharmacy_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i>
                                            Filter</button>&nbsp;
                                        <a class="btn btn-primary" href="{{ route('stock.transfer.report') }}"><i class="fa fa-times"
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
            <h3 class="card-title">Stock Transfer Report</h3>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table id="report" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th>SL.NO</th>
                            <th>Transfer<br>Date</th>
                            <th>Transfer<br>Code</th>
                            <th>Pharmacy From</th>
                            <th>Pharmacy To</th>
                            <th>Total<br>Items</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach ($stock_transfers as $key=>$transfer)
                        <tr id="dataRow_{{ $transfer->stock_transfer_id  }}">
                            <td>{{ $key+1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d-m-Y') }} </td>
                            <td>{{$transfer->transfer_code}}</td>
                            <td>{{@$transfer->pharmacy['pharmacy_name']}}</td>
                            <td>{{@$transfer->pharmacys['pharmacy_name']}}</td>
                            <td>{{$transfer->transfer_item_count }}</td>
                            <td><a class="btn btn-primary btn-sm" href="{{ route('stock-transfer.report.detail', ['id' => $transfer->id]) }}">
                                Detail
                            </a></td>
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
                title: 'Stock Transfer Report',
                exportOptions: 
                {
                    columns: [0,1,2,3,4,5]
                }
            },
            {
                extend: 'pdf',
                text: 'Export to PDF',
                title: 'Stock Transfer Report',
                footer: true,
                orientation : 'landscape',
                pageSize : 'LEGAL',
                exportOptions: 
                {
                    columns: [0,1,2,3,4,5],
                   
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


