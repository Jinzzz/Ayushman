@extends('layouts.app')
@section('content')
@php
use App\Models\Mst_Staff;
@endphp
    <style>
        .card-header {
            display: flex;
            justify-content: space-between;
        }

        .card-title {
            margin-top: 0;
            /* Optional: Adjust margin if needed */
        }

    .equal-width-td {
        width: 14.28%;
    }
    </style>
 
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                    @if ($message = Session::get('errors'))
                        <div class="alert alert-danger">
                            <p>{{ $errors }}</p>
                        </div>
                    @endif
                    <div class="card-header">
                        <div class="col-md-6">
                            <h3 class="mb-0 card-title">Medicine Initial Stock Update</h3>
                        </div>
                    </div>
                    <div class="col-lg-12" style="background-color: #fff;">
   
                        <form action="{{ route('updatestockmedicine') }}" method="POST" id="myForm" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="_method" value="PUT">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Pharmacy*</label>
                                       @if(Auth::check() && Auth::user()->user_type_id == 96)
                                       @php
                                        $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
                                        $mappedpharma = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
                                       @endphp
                                      
                                           <select class="form-control" name="pharmacy_id" id="pharmacy_id" required>
                                               <option value="">Select Pharmacy</option>
                                               @foreach ($pharmacies as $pharmacy)
                                                   @if(in_array($pharmacy->id, $mappedpharma))
                                                       <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
                                                   @endif
                                               @endforeach
                                           </select>
                                       
                                   @else
                                    @if(session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all")
                                           <select class="form-control" name="pharmacy_id" id="pharmacy_id" required readonly>
                                               <option value="{{ session('pharmacy_id') }}">{{ session('pharmacy_name') }}</option>
                                           </select>
                                       @else
                                       <select class="form-control" name="pharmacy_id" id="pharmacy_id" required>
                                           <option value="">Select Pharmacy</option>
                                           @foreach ($pharmacies as $pharmacy)
                                               <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
                                           @endforeach
                                       </select>
                                       @endif
                                   @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="card">
                                        <div class="table-responsive">
                                            <table class="table card-table table-vcenter text-nowrap" id="productTable">
                                                <thead>
                                                    <tr>
                                                        <th>Medicine</th>
                                                        <th>Batch No</th>
                                                        <th>MFD / EXP</th>
                                                        <th>Stock</th>
                                                        <th>Purchase<br>rate<br><span style="text-transform: none;font-size: 8px !important;">(Excluding GST)</span></th>
                                                        <th>Sale<br>Rate<br><span style="text-transform: none;font-size: 8px !important;">(Excluding GST)</span></th>
                                                        <th>Actions</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id="productRowTemplate" style="display: none">
                                                        <td class="equal-width-td">
                                                            <select class="form-control medicine-name" name="medicine_id[]"
                                                                dis>
                                                                <option value="">Please select medicine</option>
                                                                
                                                                @foreach ($meds as $id => $medicine)
                                                                <option value="{{ $medicine->id }}">{{ $medicine->medicine_name }}</option>
                                                                @endforeach

                                                            </select>
                                                        </td>
                                                        <td class="medicine-batch-no equal-width-td">
                                                            <input type="text" class="form-control"  name="batch_no[]" id="batch_no">
                                                    </td>
                                                        <td class="medicine-stock-mfd equal-width-td">
                                                            <input type="date" class="form-control"  name="mfd[]" id="mfd"><br>
                                                            <input type="date" class="form-control"  name="expd[]" id="expd">
                                                        </td>
                                                        <td class="medicine-stock equal-width-td">
                                                            <input type="number" class="form-control" min="0"  name="new_stock[]" placeholder="New Stock"></td>
                                                        <td class="medicine-purchase-rate equal-width-td">
                                                            <input type="text" class="form-control"  name="purchase_rate[]" placeholder="Purchase Rate" oninput="if(this.value.length === 1 && this.value === '0') { this.value = ''; } else { this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); }"></td>
                                                        <td class="medicine-sale-rate equal-width-td">
                                                            <input type="text" class="form-control sale-rate"  name="sale_rate[]" placeholder="Sale Rate" oninput="if(this.value.length === 1 && this.value === '0') { this.value = ''; } else { this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); }"></td>
                                                        <td class="equal-width-td"><button type="button" onclick="removeFn(this)"
                                                                style="background-color: #007BFF; color: #FFF; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Remove</button>
                                                        </td>
                                                        <td class="display-med-row medicine-stock-id">
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addProductBtn">Add Row</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <center>
                                    <button type="submit" class="btn btn-raised btn-primary"> Update</button>
                                    <button type="reset" class="btn btn-raised btn-success">
                                        Reset</button>
                                </center>
                            </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
 
@endsection
@section('js')
    <!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
    <script src="https://cdn.ckeditor.com/ckeditor5/34.0.1/classic/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>



    <!-- Add the correct path to the CKEditor script -->
    <script src="https://cdn.ckeditor.com/ckeditor5/34.0.1/classic/ckeditor.js"></script>

    <script>
 
        function removeFn(parm) {
            var currentRow = $(parm).closest('tr');
            currentRow.remove();
        }
    $(document).ready(function() {
        $("#addProductBtn").click(function(event) {
            event.preventDefault();
            var newRow = $("#productRowTemplate").clone().removeAttr("style");
            
            newRow.find('td').addClass('equal-width-td');
            newRow.find('select.medicine-name').addClass('medicine-select');
            newRow.find('input').val('').prop('readonly', false);
            newRow.find('input').siblings('span').remove(); 
            
           
            newRow.find('input[name="mfd[]"]').val('');
            newRow.find('input[name="expd[]"]').val('')
            
            $("#productTable tbody").append(newRow);
            //fetchBatchDetailsForRow(newRow);
            
           initializeValidation();
        });
        
    });

    </script>
<script>
     $(document).ready(function() {
       //  alert("test")
        $("#addProductBtn").click();
    });
</script>
<script>
jQuery(document).ready(function($) {
    var validationRules = {
    pharmacy_id: 'required',
    'medicine_id[]': 'required',
    'batch_no[]': 'required',
    'mfd[]': 'required',
    'expd[]': 'required',
    'new_stock[]': {
        required: true,
        min: 0
    },
    'purchase_rate[]': 'required',
    'sale_rate[]': 'required'
};

var validationMessages = {
    pharmacy_id: 'Please select a pharmacy',
    'medicine_id[]': 'Please select a medicine',
    'batch_no[]': 'Please enter a batch number',
    'mfd[]': 'Please enter a manufacturing date',
    'expd[]': 'Please enter an expiry date',
    'new_stock[]': {
        required: 'Please enter the new stock',
        min: 'Stock cannot be negative'
    },
    'purchase_rate[]': 'Please enter the purchase rate',
    'sale_rate[]': 'Please enter the sale rate'
};
var validationGroups = {
    'medicine_id[]': 'medicine',
    'batch_no[]': 'batch',
    'mfd[]': 'date',
    'expd[]': 'date',
    'new_stock[]': 'stock',
    'purchase_rate[]': 'rate',
    'sale_rate[]': 'rate'
};

function initializeValidation() {
    $('#myForm').validate({
        rules: validationRules,
        messages: validationMessages,
        groups: validationGroups,
        ignore: ":hidden:not(#myForm tr:first)"
    });
}

initializeValidation();
});
$(document).ready(function() {
    // Set min and initial value for expd[] inputs based on mfd[] inputs
    $('input[name="mfd[]"]').each(function() {
        var mfdValue = $(this).val();
        if (mfdValue) {
            var mfdDate = new Date(mfdValue);
            mfdDate.setDate(mfdDate.getDate() + 1);
            var minExpDate = mfdDate.toISOString().split('T')[0];
            var expInput = $(this).closest('tr').find('input[name="expd[]"]');
            expInput.attr('min', minExpDate);
            expInput.val(minExpDate);
        }
    });

    // Update min attribute of expd[] inputs when mfd[] inputs change
    $(document).on('change', 'input[name="mfd[]"]', function() {
        var mfdValue = $(this).val();
        if (mfdValue) {
            var expInput = $(this).closest('tr').find('input[name="expd[]"]');
            expInput.attr('min', mfdValue);
            // If you want to clear the expd[] value when mfd[] is changed, uncomment the next line
            expInput.val('');
        }
    });
});

</script>

<script>
    $(document).on('change', '.medicine-name', function() {
    var selectedMedicineId = $(this).val();
    var saleRateField = $(this).closest('tr').find('.sale-rate');

    // AJAX request to fetch unit price
    $.ajax({
        type: 'GET',
        url: '/initialstock/getUnitPrice/' + selectedMedicineId,
        success: function(response) {
            if (response.success) {
                saleRateField.val(response.unitPrice);
            } else {
                saleRateField.val('');
                alert('Failed to fetch unit price for the selected medicine.');
            }
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            alert('An error occurred while fetching unit price.');
        }
    });
});
</script>


