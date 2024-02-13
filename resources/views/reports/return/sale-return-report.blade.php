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
                <form action="{{ route('sales.return.report') }}" method="GET" class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="return_from_date" class="form-label">Return From:</label>
                                <input type="date" class="form-control" name="return_from_date" id="return_from_date" value="{{request()->return_from_date}}">
                            </div>
                            <div class="col-md-6">
                                <label for="return_to_date" class="form-label">Return To:</label>
                                <input type="date" class="form-control" name="return_to_date" id="return_to_date" value="{{request()->return_to_date}}">
                            </div>
                            <div class="col-md-6">
                                <label for="sales_return_no" class="form-label">Return No:</label>
                                <input type="text" id="sales_return_no" name="sales_return_no" class="form-control"
                                value="{{ request()->input('sales_return_no') }}">
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
                                        <a class="btn btn-primary" href="{{ route('sales.return.report') }}"><i class="fa fa-times"
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
            <h3 class="card-title">Sales Return Report</h3>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table id="report" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th>SL.NO</th>
                            <th>Return<br>Number</th>
                            <th>Invoice<br>Number</th>
                            <th>Return<br>Date</th>
                            <th>Pharmacy</th>
                            <th>Patient</th>
                            <th>Total<br>Items</th>
                            <th>Total<br>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach ($sales_returns as $key=>$return)
                        <tr id="dataRow_{{ $return->sales_return_id  }}">
                            <td>{{ $key+1 }}</td>
                            <td>{{$return->sales_return_no}}</td>
                            <td>{{@$return->Invoice['sales_invoice_number']}}</td>
                            <td>{{ \Carbon\Carbon::parse($return->return_date)->format('d-m-Y') }} </td>
                            <td>{{@$return->Pharmacy['pharmacy_name']}}</td>
                            <td>
                                @if($return->patient_id == 0)
                                Guest Patient
                                @else
                                {{@$return->Patient['patient_name']}}
                                @endif
                            </td>
                            <td>{{$return->sales_return_detail_count }}</td>
                            <td>{{$return->total_amount }}</td>
                            <td><a class="btn btn-primary btn-sm" href="{{ route('sales.return.report.detail', ['id' => $return->sales_return_id]) }}">
                                Detail
                            </a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="pagination" style="justify-content:flex-end;margin-top:-10px">
                    {{ $sales_returns->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>
    {{-- </div> --}}
    </div>
    <script>
    
        $(document).ready(function() {
       
        $('#report').DataTable({
            paging: false,
            dom: 'Bfrtip<"pagination"lp>',
            buttons: [
            {
                extend: 'excel',
                text: 'Export to Excel',
                title: 'Sales Return Report',
                exportOptions: 
                {
                    columns: [0,1,2,3,4,5,6,7]
                }
            },
            {
                extend: 'pdf',
                text: 'Export to PDF',
                title: 'Sales Return Report',
                footer: true,
                orientation : 'landscape',
                pageSize : 'LEGAL',
                exportOptions: 
                {
                    columns: [0,1,2,3,4,5,6,7],
                    alignment: 'right',
                },
                    customize: function(doc) {
                    doc.content[1].margin = [ 100, 0, 100, 0 ]; //left, top, right, bottom
                    doc.content.forEach(function(item) {
                    if (item.table) {
                        item.table.widths = [40, '*','*','*','*','*','*','*']
                    }
                    })
                    }
            }
            ]
        });
    });
    </script>
    
@endsection


