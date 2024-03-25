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
                <form action="{{ route('sales.report.detail', ['id' => $id]) }}" method="GET" class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="medicine_name" class="form-label">Medicine</label>
                                    <input type="text" id="medicine_name" name="medicine_name" class="form-control"
                                    value="{{ request('medicine_name') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter"
                                                aria-hidden="true"></i>
                                            Filter</button>&nbsp;
                                        <a class="btn btn-primary"
                                            href="{{ route('sales.report.detail', ['id' => $invoice_id]) }}"><i
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
                    <h3 class="card-title">Sales Report Detail</h3>
                </div>
                <div class="card-body">
             <a  href="{{ route('sales.report') }} "><button class="back-button">Back</button></a>
                    <div class="table-responsive">
                        <table id="report" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>SL.NO</th>
                                    <th>Invoice<br>Number</th>
                                    <th>Medicine</th>
                                    <th>Unit</th>
                                    <th>Batch</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Tax</th>
                                    <th>Amount</th>
                                    <th>Manufacture</th>
                                    <th>Expiry</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($sales_details as $key => $sales_detail)
                                    <tr id="dataRow_{{ $sales_detail->sales_invoice_details_id }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $sales_detail->sales_invoice_number }}</td>
                                        <td>{{ $sales_detail->Medicine['medicine_name'] }}</td>
                                        <td>{{ $sales_detail->Unit['unit_name'] }}</td>
                                        <td>{{ $sales_detail->batch_id }}</td>
                                        <td>{{ $sales_detail->quantity }}</td>
                                        <td>{{ $sales_detail->rate }}</td>
                                        <td>{{ $sales_detail->med_quantity_tax_amount }}</td>
                                        <td>{{ $sales_detail->amount }}</td>
                                        <td>{{ $sales_detail->manufactured_date }}</td>
                                        <td>{{ $sales_detail->expiry_date }}</td>
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
                            title: 'Sales Report',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                            }
                        },
                        {
                            extend: 'pdf',
                            text: 'Export to PDF',
                            title: 'Sales Report',
                            footer: true,
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                                alignment: 'right',
                            },
                            customize: function(doc) {
                                doc.content[1].margin = [100, 0, 100, 0]; //left, top, right, bottom
                                doc.content.forEach(function(item) {
                                    if (item.table) {
                                        item.table.widths = [40, '*', '*', '*', '*', '*', '*',
                                            '*', '*', '*'
                                        ]
                                    }
                                })
                            }
                        }
                    ]
                });
            });
        </script>
    @endsection
