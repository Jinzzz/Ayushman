@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">View Purchase Invoice</h3>
                    </div>

                    <div class="col-lg-12" style="background-color: #fff;">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Supplier Name</label>
                                    <input type="text" class="form-control" 
                                        value="{{@$purchase_invoice->Supplier['supplier_name'] }}({{@$purchase_invoice->Supplier['supplier_code'] }})" readonly disabled >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Invoice No.</label>
                                    <input type="text" class="form-control" readonly name="medicine_name"
                                        value="{{ @$purchase_invoice->purchase_invoice_no }}" placeholder="Medicine Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Invoice Date</label>
                                    <input type="text" class="form-control" readonly disabled
                                        value="{{ date('d-M-Y',strtotime(@$purchase_invoice->invoice_date)) }}" \>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Due Date</label>
                                    <input type="text" class="form-control" readonly disabled
                                        value="{{ date('d-M-Y',strtotime(@$purchase_invoice->due_date)) }}">
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Payment Mode</label>
                                    <input type="text" class="form-control" readonly disabled
                                        value="{{ @$purchase_invoice->paymentMode['master_value'] }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Paid Amount</label>
                                    <input type="text" class="form-control" readonly disabled
                                        value="{{ @$purchase_invoice->paid_amount }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Total Amount</label>
                                    <input type="text" class="form-control" readonly disabled
                                        value="{{ @$purchase_invoice->total_amount }}">
                                </div>
                            </div>
                           
                            
                            


                            <div class="col-md-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Invoice Details</h3>
                                    </div>
                                    <div class="table-responsive">
                                        <table
                                            class="table card-table table-bordered table-vcenter text-nowrap table-gray-dark">
                                            <thead class="bg-gray-dark text-white">
                                                <tr>
                                                    <th class="text-white">ID</th>
                                                    <th class="text-white">Product Name</th>
                                                    <th class="text-white">Product Code</th>
                                                    <th class="text-white">Quantity</th>
                                                    <th class="text-white">Unit</th>
                                                    <th class="text-white">Sales Rate</th>
                                                    <th class="text-white">Purchase Rate</th>
                                                    <th class="text-white">Free Quantity</th>
                                                    <th class="text-white">Batch No</th>
                                                    <th class="text-white">MFD/EXP</th>
                                                    <th class="text-white">Discount</th>
                                                    <th class="text-white">Tax Amount</th>
                                                    <th class="text-white">Amount</th>
    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($purchase_invoice->purchaseInvoiceDetails as $key => $item)
                                                    <tr>
                                                        <th scope="row">{{ $key + 1 }}</th>
                                                        <td>{{ @$item->Medicine['medicine_name'] ?? '' }}</td>
                                                        <td>{{ @$item->medicine_code ?? '' }}</td>
                                                        <td>{{@$item->quantity}} </td>
                                                        <td>{{@$item->Unit['unit_name']}}</td>
                                                        <td>{{@$item->sales_rate}} </td>
                                                        <td>{{@$item->rate}} </td>
                                                        <td>{{@$item->free_quantity??0.00}}</td>
                                                        <td>{{@$item->batch_no??0}}</td>
                                                        <td>MFD : {{ date('d-M-Y',strtotime(@$item->mfd)) ?? '' }} <br>
                                                            EXP: {{ date('d-M-Y',strtotime(@$item->expd)) ?? ''}}
                                                        </td>
                                                        <td>{{@$item->discount??0.00}}</td>
                                                        <td>{{@$item->tax_amount??0.00}}</td>
                                                        <td>{{@$item->amount??0.00}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
                            </div>

                            <!-- ... -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>

                                        <a class="btn btn-danger" href="{{ route('medicinePurchaseInvoice.index') }}">Back</a>
                                    </center>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        @endsection
        @section('js')
            <script>
                function toggleStatus(checkbox) {
                    if (checkbox.checked) {
                        $("#statusText").text('Active');
                        $("input[name=is_active]").val(1); // Set the value to 1 when checked
                    } else {
                        $("#statusText").text('Inactive');
                        $("input[name=is_active]").val(0); // Set the value to 0 when unchecked
                    }
                }
            </script>
        @endsection
