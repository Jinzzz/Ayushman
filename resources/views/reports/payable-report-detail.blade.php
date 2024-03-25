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
                <form action="{{ route('payable.report.detail', ['id' => $supplier_id]) }}" method="GET" class="card-body">
                    <div class="card-body">
                        <div class="row">
                             <div class="col-md-4">
                                <label for="invoice_detail_from_date" class="form-label">Invoice From:</label>
                                <input type="date" class="form-control" name="invoice_detail_from_date" id="invoice_detail_from_date" value="{{request()->invoice_detail_from_date}}">
                            </div>
                            <div class="col-md-4">
                                <label for="invoice_detail_to_date" class="form-label">Invoice To:</label>
                                <input type="date" class="form-control" name="invoice_detail_to_date" id="invoice_detail_to_date" value="{{request()->invoice_detail_to_date}}">
                            </div>
                                
                            <div class="col-md-4">
                                <label for="purchase_invoice_no" class="form-label">Invoice No:</label>
                                <input type="text" id="purchase_invoice_no" name="purchase_invoice_no" class="form-control"
                                value="{{ request()->input('purchase_invoice_no') }}">
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Pharmacy</label>
                                    @if(Auth::check() && Auth::user()->user_type_id == 96)
                                       @php
                                        $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
                                        $mappedpharma = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
                                       @endphp
                                    <select class="form-control" name="pharmacy_id" id="pharmacy_id">
                                        <option value="">Select Pharmacy</option>
                                        @foreach ($pharmacy as $key => $pharmacies)
                                        @if(in_array($pharmacies->id, $mappedpharma))
                                        <option value="{{$pharmacies->id}}" {{request()->input('pharmacy_id') == $pharmacies->id ? 'selected':''}}>{{$pharmacies->pharmacy_name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @else
                                    <select class="form-control" name="pharmacy_id" id="pharmacy_id">
                                        <option value="">Select Pharmacy</option>
                                        @foreach ($pharmacy as $key => $pharmacies)
                                        <option value="{{$pharmacies->id}}" {{request()->input('pharmacy_id') == $pharmacies->id ? 'selected':''}}>{{$pharmacies->pharmacy_name}}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter"
                                                aria-hidden="true"></i>
                                            Filter</button>&nbsp;
                                        <a class="btn btn-primary"
                                            href="{{ route('payable.report.detail', ['id' => $supplier_id]) }}"><i
                                                class="fa fa-times" aria-hidden="true"></i> Reset</a>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="col-md-6">
                    <h3 class="card-title">Payable Report Detail</h3>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        <a class="btn btn-raised btn-primary" href="{{route('payable.report')}}">BACK</a>
                     </div>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="report" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>SL.NO</th>
                                     <th>Invoice<br>Date</th>
                                    <th>Invoice<br>Number</th>
                                    <th>Supplier</th>
                                    <th>Pharmacy</th>
                                    <th>Total<br> Payable Amount</th>
                                     <th>Total<br> Paid Amount</th>
                                    <th>Total<br>Due Amount </th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($purchase_details as $key => $purchase_detail)
                                    <tr id="dataRow_{{ $purchase_detail->purchase_invoice_details_id  }}">
                                        <td>{{ $key + 1 }}</td>
                                         <td>{{ @$purchase_detail->invoice_date }}</td>
                                        <td>{{ @$purchase_detail->purchase_invoice_no }}</td>
                                        <td>{{ @$purchase_detail->Supplier['supplier_name'] }}</td>
                                        <td>{{ @$purchase_detail->Pharmacy['pharmacy_name'] }}</td>
                                        <td>{{ @$purchase_detail->total_amount }}</td>
                                        <td>{{ @$purchase_detail->paid_amount }}</td>
                                        <td>{{ @$purchase_detail->total_amount-@$purchase_detail->paid_amount }}</td>   
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
                    buttons: [{
                            extend: 'excel',
                            text: 'Export to Excel',
                            title: 'Payabale Report Detailed',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6,7]
                            }
                        },
                        {
                            extend: 'pdf',
                            text: 'Export to PDF',
                            title: 'Payable Report Detailed',
                            footer: true,
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7],
                                alignment: 'right',
                            },
                            
                        }
                    ]
                });
            });
        </script>
        <script>
    $(document).ready(function() {
        var currentDate = new Date().toISOString().slice(0, 10);
        var invoiceFromDate = "{{ request()->invoice_detail_from_date }}";
        var invoiceToDate = "{{ request()->invoice_detail_to_date }}";
        if (invoiceFromDate === '') {
            // $('#invoice_detail_from_date').val(currentDate);
        } else {
            $('#invoice_detail_from_date').val(invoiceFromDate);
        }
        if (invoiceToDate === '') {
            // $('#invoice_detail_to_date').val(currentDate);
        } else {
            $('#invoice_detail_to_date').val(invoiceToDate);
        }
    });
    </script>
    @endsection
