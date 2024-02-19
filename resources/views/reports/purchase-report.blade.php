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
                <form action="{{ route('purchase-report') }}" method="GET" class="card-body">
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
                            <div class="col-md-4">
                                <label for="purchase_invoice_no" class="form-label">Invoice No:</label>
                                <input type="text" id="purchase_invoice_no" name="purchase_invoice_no" class="form-control"
                                value="{{ request()->input('purchase_invoice_no') }}">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Supplier</label>
                                    <select class="form-control" name="supplier_id" id="supplier_id">
                                        <option value="">Select supplier</option>
                                        @foreach ($suppliers as $key => $supplier)
                                        <option value="{{$supplier->supplier_id}}" {{request()->input('supplier_id') == $supplier->supplier_id ? 'selected':''}}>{{$supplier->supplier_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i>
                                            Filter</button>&nbsp;
                                        <a class="btn btn-primary" href="{{ route('purchase-report') }}"><i class="fa fa-times"
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
            <h3 class="card-title">Purchase Report</h3>
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
                            <th>Supplier</th>
                            <th>Total<br>Items</th>
                            <th>Total<br>Amount</th>
                            <th>Payment<br>Mode</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach ($purchase as $key=>$purchases)
                        <tr id="dataRow_{{ $purchases->purchase_invoice_id  }}">
                            <td>{{ $key+1 }}</td>
                            <td>{{$purchases->purchase_invoice_no}}</td>
                            <td>{{ \Carbon\Carbon::parse($purchases->invoice_date)->format('d-m-Y') }} </td>
                            <td>{{@$purchases->Pharmacy['pharmacy_name']}}</td>
                            <td>{{@$purchases->Supplier['supplier_name']}}</td>
                            <td>{{$purchases->purchase_invoice_details_count }}</td>
                            <td>{{$purchases->total_amount }}</td>
                            <td>{{@$purchases->paymentMode['master_value'] }}</td>
                            
                            <td><a class="btn btn-primary btn-sm" href="{{ route('purchase.report.detail', ['id' => $purchases->purchase_invoice_id]) }}">
                                Detail
                            </a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="pagination" style="justify-content:flex-end;margin-top:-10px">
                    {{ $purchase->onEachSide(1)->links() }}
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
                title: 'Purchase Report',
                exportOptions: 
                {
                    columns: [0,1,2,3,4,5,6,7]
                }
            },
            {
                extend: 'pdf',
                text: 'Export to PDF',
                title: 'Purchase Report',
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


