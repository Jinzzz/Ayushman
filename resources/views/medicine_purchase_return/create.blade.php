@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{$message}}</p>
            </div>
            @endif
            @if ($message = Session::get('error'))
            <div class="alert alert-danger">
                <p></p>
            </div>
            @endif
            <div class="card-header">
                <h3 class="card-title">Medicine Purchase Return</h3>
            </div>
            <form  id="medicinePurchaseReturnForm" action="{{ route('medicinePurchaseReturn.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Supplier*</label>
                            <select class="form-control" required name="supplier_id" id="supplier_id">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Purchase Invoice Id*</label>
                            <select class="form-control" name="purchase_invoice_id" id="purchase_invoice_id">
                                <option value="">Select Purchase Invoice</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Return Date*</label>
                            <input type="date" id="return-date" required name="return_date" class="form-control" value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Branch*</label>
                            <select class="form-control" required name="branch_id" id="branch_id">
                                <option value="">Select Branch</option>
                                @foreach($branches as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <!-- An empty column that spans the entire width -->
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <!-- An empty column that spans the entire width -->
                        </div>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table id="productTable" class="table card-table table-vcenter text-nowrap">
                                <thead>
                                    <tr>

                                        <th class="wd-15p">Product Name</th>
                                        <th class="wd-15p">Quantity</th>
                                        <th class="wd-15p">Product Unit</th>
                                        <th class="wd-15p">Rate</th>
                                        <th class="wd-15p">Free Quantity</th>
                                        <th class="wd-15p">Return Quantity</th>
                                        <th class="wd-15p">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="productRowTemplate" class="product-row" style="display: none">

                                        <td>
                                            <select class="form-control" readonly name="product_id[]">
                                                @foreach($product as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control" name="quantity[]" readonly></td>
                                        <td> <select class="form-control" readonly name="unit_id[]">
                                                @foreach($unit as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select></td>
                                        <td><input type="text" class="form-control" name="rate[]" readonly></td>
                                        <td><input type="text" class="form-control" name="free_quantity[]" readonly></td>
                                        <td><input type="text" class="form-control" name="return_quantity[]" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></td>
                                        <td class="text-center">
                                            <input type="checkbox" name="selected_products[]" class="form-check-input">
                                        </td>

                                    </tr>
                                    @if(isset($details))
                                    @foreach($details as $detail)
                                    <tr id="productRowTemplate" class="product-row">
                                   
                                        <td>
                                            <select class="form-control" readonly name="product_id[]">
                                                @foreach($product as $id => $name) 
                                                <option value="{{ $id }}" {{ $id == $detail->product_id ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control" name="quantity[]" value="{{ $detail->quantity }}" readonly></td>
                                        <td>
                                            <select class="form-control" readonly name="unit_id[]">
                                                @foreach($unit as $id => $name)
                                                <option value="{{ $id }}" {{ $id == $detail->unit_id ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="rate[]" value="{{ $detail->rate }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="free_quantity[]" value="{{ $detail->free_quantity }}" readonly>
                                        </td>
                                        <td>
                                        <input type="text" class="form-control" name="return_quantity[]">
                                    </td>
                                    <td class="text-center">
                                            <input type="checkbox" name="selected_products[]" class="form-check-input" id="checkbox" value="{{$detai->product_id}}" >
                                        </td>

                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card">
                            <div class="col-md-6">
                                <label class="form-label">Notes/Reason</label>
                                <textarea class="form-control" name="notes" placeholder="Notes/Reason"></textarea>
                            </div>

                      <input type="hidden"  name="checked_product" id = "checked_product"> 

                            <div class="form-group d-flex justify-content-end">
                                <button type="button" id="last-submit-button" onclick="submitForm()" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Save
                                </button>
                                <a class="btn btn-danger ml-2" href="">Close</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ROW-1 CLOSED -->
@endsection
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function() {
        // alert("tets")
        // Add Product button click event
        $("#addProductBtn2").click(function(event) {

            event.preventDefault();
            // alert("test");
            // Clone the product row template
            var newRow = $("#productRowTemplate").clone();

            // Remove the "style" attribute to make the row visible
            newRow.removeAttr("style");
            newRow.find('input[type="text"]').val('');
            newRow.find('input[type="number"]').val('');

            // Append the new row to the table
            $("#productTable tbody").append(newRow);
        });

    });

    $(document).ready(function() {
        $('#supplier_id').change(function() {
            var supplierId = $(this).val();
            $.ajax({
                url: "{{ route('getPurchaseInvoices') }}",
                type: 'GET',
                data: {
                    supplier_id: supplierId
                },
                success: function(data) {
                    var purchaseInvoiceDropdown = $('#purchase_invoice_id');
                    purchaseInvoiceDropdown.empty();
                    purchaseInvoiceDropdown.append('<option value="">Select Purchase Invoice</option>');
                    $.each(data, function(key, value) {
                        purchaseInvoiceDropdown.append('<option value="' + key + '">' + value + '</option>');
                    });
                },
                error: function() {
                    alert('An error occurred while fetching purchase invoices.');
                }
            });
        });
    });
    $(document).ready(function() {
        $('#purchase_invoice_id').change(function() {
            var purchaseInvoiceId = $(this).val();
            $.ajax({
                url: '/getPurchaseInvoiceDetails',
                method: 'GET',
                data: {
                    purchase_invoice_id: purchaseInvoiceId
                },
                success: function(response) {
                    if (response.length > 0) {
                        for (var i = 0; i < response.length; i++) {
                            var newRow = $("#productRowTemplate").clone();
                            newRow.removeAttr("style");

                            newRow.find('select[name="product_id[]"]').val(response[i].product_id);
                            newRow.find('input[name="quantity[]"]').val(response[i].quantity);
                            newRow.find('select[name="unit_id[]"]').val(response[i].unit_id);
                            newRow.find('input[name="rate[]"]').val(response[i].rate);
                            newRow.find('input[name="free_quantity[]"]').val(response[i].free_quantity);
                            $('#productTable tbody').append(newRow);
                        }
                    } else {}
                },
                error: function() {
                    alert('Error fetching purchase invoice details.');
                }
            });
        });
    });

</script>
<script>
    function submitForm() {
     .
        let values = new Array();         
     $("input:checkbox[name=selected_products]:checked").each(function(){
      values.push($(this).val());
     });
     
     $("#checked_product").val(values);

     $("#medicinePurchaseReturnForm").submit();
    }

</script>
