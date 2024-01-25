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
                    <p>{{$message}}</p>
                </div>
            @endif
            <div class="card-header">
                <h3 class="card-title">Edit Medicine Purchase Return</h3>
            </div>
            <form action="{{ route('medicinePurchaseReturn.update', $medicinePurchaseReturn->purchase_return_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Supplier</label>
                            <select class="form-control" name="supplier_id" id="supplier_id">
                            <option value="">Choose Branch</option>
                            @foreach($suppliers as $supplierId => $supplierName)
                                <option value="{{ $supplierId }}" {{ $medicinePurchaseReturn->supplier_id == $supplierId ? 'selected' : '' }}>
                                    {{ $supplierName }}
                                </option>
                            @endforeach
                        </select>

                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Purchase Invoice Id*</label>
                            <input type="text" id="return-date" required name="purchase_invoice_id" class="form-control" value="{{ $medicinePurchaseReturn->purchase_invoice_id}}" readonly> 
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Return Date*</label>
                            <input type="date" id="return-date" required name="return_date" class="form-control" value="{{ $medicinePurchaseReturn->return_date}}">
                        </div>
                        <div class="col-md-3">
                        <label class="form-label">Pharmacy*</label>
                        <select class="form-control" name="pharmacy" id="pharmacy_id" required>
                            <option value="">Select Pharmacy</option>
                            @foreach ($pharmacies as $id => $branchName)
                                <option value="{{ $id }}" {{ $id == $medicinePurchaseReturn->pharmacy_id ? 'selected' : '' }}>{{ $branchName->pharmacy_name }}</option>
                            @endforeach
                        </select>
                        </div>

                    </div>

                    <!-- Details Table -->
                    <div class="card">
                        <div class="table-responsive">
                            <table id="productTable" class="table card-table table-vcenter text-nowrap">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">Product Name</th>
                                        <th class="wd-15p">Product Unit</th>
                                        <th class="wd-15p">Return Quantity</th>
                                        <th class="wd-15p">Final Rate</th>
                                        <th class="wd-15p">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($medicinePurchase as $medicine)
                           <input type="hidden" class="form-control" name="quantity[]" value="{{ $medicine->quantity_id }}">
                                    <tr id="productRowTemplate" class="product-row">
                                            <td>
                                                <select class="form-control" readonly name="product_id[]">
                                                    @foreach($product as $id => $name)
                                                        <option value="{{ $id }}" {{ $id == $medicine->product_id ? 'selected' : '' }}>{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                            <select class="form-control" readonly name="unit_id[]">
                                                @foreach($unit as $id => $name)
                                                    <option value="{{ $id }}" {{ $id == optional($medicine)->unit_id ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                      
                                            <td><input type="number" class="form-control" name="return_quantity[]" value="{{ $medicine->return_quantity }}" max ="{{ $medicine->quantity_id }}">
                                            <input type="hidden" id="hd-val" value="{{ $medicine->return_quantity }}" ></td>
                                            <td>
                                                <input type="text" class="form-control" name="rate[]" value="{{ $medicine->return_rate }}">
                                                <input type="hidden" id ="hid-val"  value="{{ $medicine->return_rate }}">
                                            </td>
                                           
                                            <td>
                                                <button type="button" onclick="deleteRow(this)" class="btn-danger btn-sm">
                                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="card">
                        <div class="col-md-6">
                            <label class="form-label">Notes/Reason</label>
                            <textarea class="form-control" name="notes" placeholder="Notes/Reason">{{ $medicinePurchaseReturn->reason }}</textarea>
                        </div>

                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-raised btn-primary">
                                <i class="fa fa-check-square-o"></i> Update
                            </button>
                            <a class="btn btn-danger ml-2" href="{{ route('medicinePurchaseReturn.index') }}">Close</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
   $(document).ready(function () {
       // Add Product button click event
       $("#addProductBtn2").click(function (event) {
           event.preventDefault();
           var newRow = $("#productRowTemplate").clone();
           newRow.removeAttr("style");
           newRow.find('input[type="text"]').val('');
           newRow.find('input[type="number"]').val('');
           $("#productTable tbody").append(newRow);
       });

       // Event listener for deleting a row
       $(document).on('click', '.btn-danger', function () {
           $(this).closest('tr.product-row').remove();
       });
   });

   //fetching purchase invoice id for a particular supplier:

   $(document).ready(function () {
        // Event listener for supplier selection
        $('#supplier_id').change(function () {
         // console.log('Supplier selected');
            var supplierId = $(this).val();

            // Make an AJAX request to fetch purchase invoices based on the selected supplier
            $.ajax({
                url: "{{ route('getPurchaseInvoices') }}", // Replace with your route for fetching purchase invoices
                type: 'GET',
                data: {
                    supplier_id: supplierId
                },
                success: function (data) {
                    // Populate the purchase invoice dropdown with the fetched data
                    var purchaseInvoiceDropdown = $('#purchase_invoice_id');
                    purchaseInvoiceDropdown.empty(); // Clear previous options
                    purchaseInvoiceDropdown.append('<option value="">Select Purchase Invoice</option>'); // Add default option

                    // Add options based on the fetched data
                    $.each(data, function (key, value) {
                        purchaseInvoiceDropdown.append('<option value="' + key + '">' + value + '</option>');
                    });
                },
                error: function () {
                    alert('An error occurred while fetching purchase invoices.');
                }
            });
        });
      });

      //fetching product details based on the purchase invoice number:
   
$(document).ready(function () {
    $('#purchase_invoice_id').change(function () {
        var purchaseInvoiceId = $(this).val();
        $.ajax({
            url: '/getPurchaseInvoiceDetails', 
            method: 'GET',
            data: { purchase_invoice_id: purchaseInvoiceId },
            success: function (response) {
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
                } else {
                }
            },
            error: function () {
                alert('Error fetching purchase invoice details.');
            }
        });
    });
}); 
</script>
<script>
    $(document).ready(function () {
        // Validate return quantity on input change
        $('input[name="return_quantity[]"]').on('input', function () {

              //alert("t")
              var x = $(this).parents('td').siblings('td').find('#hid-val');
              var y = x.val();
              var singleVal = $(this).next('#hd-val').val();
              var z = y/singleVal;
            //alert(z)
              var newV = $(this).val();
             // alert(newV)
            //   if(newV == '' || newV == null || newV == undefined ){
            //     newV = singleVal
            //   }
            var d = $(this).parents('td').siblings('td').find('input[name="rate[]"]');
              var newTotal = newV * z ;
              //alert(newTotal)
             d.val(newTotal);





        });

    });
</script>

