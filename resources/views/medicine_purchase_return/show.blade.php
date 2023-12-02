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
                <h3 class="card-title">View Medicine Purchase Return</h3>
            </div>
           
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Supplier*</label>
                            <input type="text" class="form-control" readonly name="supplier_id" value="{{$viewPurchaseReturn->supplier->supplier_name}}" id="supplier_id">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Purchase Invoice Id*</label>
                            <select class="form-control" readonly name="purchase_invoice_id" id="purchase_invoice_id" value="{{ $viewPurchaseReturn->purchase_invoice_id}}">
                                <option value="">Select Purchase Invoice</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Return Date*</label>
                            <input type="date" id="return-date" readonly name="return_date" class="form-control" value="{{ $viewPurchaseReturn->return_date}}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Branch*</label>
                            <input type="text" class="form-control" readonly name="branch_id" value="{{$viewPurchaseReturn->Branch->branch_name}}" >
                              
                        </div>
                    </div>

                    <!-- Details Table -->
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
                                        <th class="wd-15p">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($showDetails  as $detail)
                                    <tr id="productRowTemplate" class="product-row">
                                            <td>
                                                <input type="text" class="form-control" readonly name="product_id[]" value="{{ $detail->product_id }}">
                                            </td>
                                            <td><input type="text" class="form-control" readonly name="quantity[]" value="{{ $detail->quantity_id }}"></td>
                                            <td>
                                                <input type="text" class="form-control" readonly name="unit_id[]" value="{{ $unit[$detail->unit_id] }}" />
                                               
                                            </td>
                                            <td><input type="text" class="form-control" readonly name="rate[]" value="{{ $detail->rate }}"></td>
                                            <td><input type="text" class="form-control" readonly name="free_quantity[]" value="{{ $detail->free_quantity }}"></td>
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

                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-primary" id="addProductBtn2" type="button">Add Product</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="card">
                        <div class="col-md-6">
                            <label class="form-label">Notes/Reason</label>
                            <textarea class="form-control" readonly name="notes" placeholder="Notes/Reason">{{ $viewPurchaseReturn->reason }}</textarea>
                        </div>

                        <div class="form-group d-flex justify-content-end">
                           
                            <a class="btn btn-danger ml-2" href="{{ route('medicinePurchaseReturn.index') }}">Close</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
    // Event listener for purchase invoice selection
    $('#purchase_invoice_id').change(function () {
        var purchaseInvoiceId = $(this).val();
      //alert(purchaseInvoiceId)
        // Make an AJAX request to get details for the selected purchase invoice
        // Update the table content with the details
        // Example AJAX request using jQuery:
        $.ajax({
            url: '/getPurchaseInvoiceDetails', // Replace with your route
            method: 'GET',
            data: { purchase_invoice_id: purchaseInvoiceId },
            success: function (response) {
                // Check if the response is not empty and has details
               //  console.log(response)
                if (response.length > 0) {
                    // Clear the existing rows in the table body
                  //   $('#productTable tbody').empty();

                    // Loop through the response and append new rows to the table
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
                    // Handle the case where there are no details for the selected invoice
                  //   alert('No details found for the selected purchase invoice.');
                }
            },
            error: function () {
                alert('Error fetching purchase invoice details.');
            }
        });
    });
});
</script> --}}
