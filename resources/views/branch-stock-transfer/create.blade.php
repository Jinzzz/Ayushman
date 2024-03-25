@extends('layouts.app')
@section('content')
@php
use App\Models\Mst_Staff;
@endphp
    {{-- <style>
        .form-control[readonly] {
            background-color: #c7c7c7 !important;
        }

        .page input[type=text][readonly] {
            background-color: #c7c7c7 !important;
        }

        .form-group .last-row {
            border-top: 1px solid #0d97c6;
            padding-top: 15px;
        }
        .hideRadio{
            display: none;
        }
        button.noFunction {
            pointer-events: none;
        }
    </style> --}}
    <style>
        button.noFunction {
            pointer-events: none;
        }
       span.error {
    margin-top: 0px;
    display: block;
    /* margin-left: 4px; */
    position: absolute;
    background-color: #;
    color: #721c24;
    /* border: 1px solid #f5c6cb; */
    padding: 6px 10px;
    border-radius: 3px;
    font-size: 0.875em;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    z-index: 9;
    background: white;
    color: red;
    box-shadow: 2px 2px 5px rgb(0 0 0 / 37%);
    /* bottom: -16px; */
    margin-top: 5px;
    margin-left: 5px;
    z-index: 99;
}
    </style>
    <!-- <div class="container"> -->
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">Add Stock Transfer to Pharmacy</h3>
                    </div>
                    <!-- Success message -->
                    <div class="col-lg-12 card-background" style="background-color:#fff";>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <!-- <strong>Whoops!</strong> There were some problems with your input.<br><br> -->
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('stockTransfer') }}" method="POST" enctype="multipart/form-data" id="subForm" onsubmit="return validateForm()">
                         @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Pharmacy From*</label>
                                        @if(Auth::check() && Auth::user()->user_type_id == 96)
                                           @php
                                            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
                                            $mappedpharma = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
                                           @endphp
                                           
                                               <select class="form-control" name="pharmacy_from" id="pharmacy_from" required>
                                                   <option value="">Select Pharmacy</option>
                                                   @foreach ($pharmacies as $pharmacy)
                                                       @if(in_array($pharmacy->id, $mappedpharma))
                                                           <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
                                                       @endif
                                                   @endforeach
                                               </select>
                                          
                                       @else
                                            @if(session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all")
                                               <select class="form-control" name="pharmacy_from" id="pharmacy_from" required readonly>
                                                   <option value="{{ session('pharmacy_id') }}">{{ session('pharmacy_name') }}</option>
                                               </select>
                                           @else
                                               <select class="form-control" name="pharmacy_from" id="pharmacy_from" required>
                                                   <option value="">Select Pharmacy</option>
                                                   @foreach ($pharmacies as $pharmacy)
                                                       <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
                                                   @endforeach
                                               </select>
                                            @endif
                                       @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Pharmacy To*</label>
                                        <select class="form-control" required name="pharmacy_to" id="pharmacy_to">
                                        <option value="" selected disabled>--Select Pharmacy--</option>
                                        @foreach ($pharmacies as $id => $pharmacy)
                                            <option value="{{ $pharmacy->id }}" {{ old('pharmacy_to') == $pharmacy->id ? 'selected' : '' }}>
                                                {{ $pharmacy->pharmacy_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Transfer Date*</label>
                                        <input type="date" class="form-control" required name="transfer_date"
                                            id="transfer_date" placeholder="Date" value="{{ old('transfer_date') }}">
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
                                                        <th>Medicine Name</th>
                                                        <th>Batch No</th>
                                                        <th>Transfer Quantity</th>
                                                        <th>Actions</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id="productRowTemplate" style="display: none">
                                                        <td>
                                                          <select class="form-control medicine-name" name="medicine_id[]" onchange="fetchBatchDetailsForRow()">

                                                                <option value="">Please select medicine</option>
                                                                
                                                                @foreach ($medicines as $id => $medicine)
                                                                <option value="{{ $medicine->id }}">{{ $medicine->medicine_name }}</option>
                                                                @endforeach

                                                            </select>
                                                        </td>
                                                        <td class="medicine-batch-no">
                                                            <select class="form-control batch_no" id="batch_no"  name="batch_no[]">
                                                            <option value="">--Batch--</option>
                                                    
                                                        </select>
                                                        <input type="hidden" name="current_stock" value="">
                                                    </td>
                                                        <td class="medicine-quantity"><input type="number" min="1"
                                                                class="form-control" value="" name="quantity[]"  placeholder = "Transfer Quantity" oninput="if (this.value < 1) this.value = 1;"></td>
                                                        <td><button type="button" onclick="removeFn(this)"
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
                            <div class="row">
                                <div class="col-md-6">
                                   <!-- Left Div - terms_condition -->
                                   <div id="terms_condition" class="custom-margin">
                                      <div class="form-group">
                                         <label class="form-label">Notes:</label>
                                         <textarea class="form-control" name="notes" placeholder="Notes">{{ old('notes') }}</textarea>

                                      </div>
                                      <div class="form-group">
                                      <label class="form-label">Reference File (Supported Formats: .pdf, .doc, .docx. Max size: 2 MB)</label>
                                    <input type="file" class="form-control" accept=".pdf, .doc, .docx" name="reference_file">
                                    
                                    <div class="name">
                                        @if ($errors->has('reference_file'))
                                            <span class="text-danger errbk">{{ $errors->first('reference_file') }}</span>
                                        @endif
                                    </div>
                                      </div>
                                   </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <center>
                                    <button type="submit" class="btn btn-raised btn-primary" id="subFm" >
                                        <i class="fa fa-check-square-o"></i> Add</button>
                                    <button type="reset" class="btn btn-raised btn-success">
                                        <i class="fa fa-refresh"></i> Reset</button>
                                    <a class="btn btn-danger" href="{{ route('branch-transfer.index') }}"> <i class="fa fa-times"></i>
                                        Cancel</a>
                                </center>
                            </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    <!-- </div> -->
@endsection
@section('js')
@endsection
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>


<script>
    //qty negative value restriction
    $(document).ready(function() {
        
    $('input[name="quantity[]"]').on('input', function() {
        var inputValue = $(this).val();
        if (inputValue < 0) {
            $(this).val(1); 
        }
    });
});

function removeFn(parm) {
    var currentRow = $(parm).closest('tr');
    currentRow.remove();
}


$(document).ready(function() {

    $('#pharmacy_from').change(function() {
        var selectedValue = $(this).val();
        $('#pharmacy_to option').show(); 
        if (selectedValue) {
            $('#pharmacy_to option[value="' + selectedValue + '"]').hide();
        }
        $('#pharmacy_to').val('');
    });
});
//set current date in transfer date
$(document).ready(function() {
    var currentDate = new Date().toISOString().slice(0,10);
    $('#transfer_date').val(currentDate);
});

//get stock 
$(document).ready(function() {
$(document).on('change', '.batch_no', function() {
    var current_stock = $(this).find(':selected').data('current_stock'); 
    $('.current_stock').val(current_stock);
    //console.log(current_stock); 
     $(this).closest('tr').find('input[name="quantity[]"]').attr("max", current_stock );
    //current_stock
});
$(document).on('input', 'input[name="quantity[]"]', function() {
    var x = $(this).closest('tr').find('.batch_no option:selected').data('current_stock');
    //alert(x)
    var y  = $(this).val();
    
    if(y > x) {
                swal("Error", "Transfer Quantity cannot be greater than Current Stock", "error");
        $(this).val('');
        
    }
});


    
    // var batch = document.getElementById('batch_noS');
  
    // batch.addEventListener('change', function(e) {
    //     console.log(e.target.value);
    // });


   
    function fetchBatchDetailsForRow(row) {

        var medicineId = row.find('.medicine-name').val();
        var pharmacyId = $('#pharmacy_from').val();
        console.log(pharmacyId);

        if (!medicineId || !pharmacyId) {
            return;
        }

        $.ajax({
            url: '/getBatchDetails',
            type: 'GET',
            data: {
                'medicine_id': medicineId,
                'pharmacy_id': pharmacyId,
                '_token': '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log(response);
                //alert("test")
                var batchSelect = row.find('.batch_no');
                batchSelect.empty();
                batchSelect.append('<option value="">--Select Batch--</option>');
                var x = 0;
                if (response && response.length > 0) {
                    $.each(response, function(index, item) {
                        batchSelect.append('<option class="selected-'+ item.stock_id +'" value="' + item.stock_id + '" data-custom2="'+ item.purchase_unit_id +'" data-current_stock="'+ item.current_stock +'" data-custom3="'+ item.purchase_rate +'" data-custom4="'+ item.sale_rate +'">' + item.batch_no + ' (MFD: ' + item.mfd + ', EXP: ' + item.expd + ', Stock: ' + item.current_stock + ', Sale Rate: '+ item.sale_rate + ')</option>');
                       
                    });
                } else {
                    console.log('No batch details found.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    }
$(document).on('change', '.medicine-name, #pharmacy_from', function() {
    var row = $(this).closest('tr');
    fetchBatchDetailsForRow(row);
});

$('.medicine-name').each(function() {
    var row = $(this).closest('tr');
    fetchBatchDetailsForRow(row);
});
        $(document).ready(function() {
        addDefaultRow();
    });
    
    $("#addProductBtn").click(function(event) {
    event.preventDefault();
    addDefaultRow();
});

function addDefaultRow() {
    var newRow = $("#productRowTemplate").clone().removeAttr("style");
    newRow.find('select').addClass('medicine-select');
    newRow.find('input').val('').prop('readonly', false);
    newRow.find('input span').remove();
    $("#productTable tbody").append(newRow);
    fetchBatchDetailsForRow(newRow);
}
    function validateProductRow() {
    var productName = $("#productName").val();
    var quantity = $("#quantity").val();

    if (productName.trim() === '') {
        alert('Please enter a product name');
        return false;
    }

    if (quantity.trim() === '') {
        alert('Please enter a quantity');
        return false;
    }

    return true;
}

  
       
});
$(document).on('change', '.medicine-select', function() {
    var row = $(this).closest('tr');
    row.find('input[name="mfd[]"]').remove();
    row.find('input[name="exp[]"]').remove();
    row.find('input[name="purchase_unit_id[]"]').remove();
    row.find('input[name="selected_batch_no[]"]').remove();
    row.find('input[name="purchase_rate[]"]').remove();
    row.find('input[name="sale_rate[]"]').remove();

    var selectedOption = $(this).find('option:selected');
        var optionText = selectedOption.text();
        var batchNumberMatch = optionText.match(/([A-Za-z0-9]+)\s*\(/);
        var batchNumber = batchNumberMatch ? batchNumberMatch[1].trim() : null;
        console.log(batchNumber);
        var match = optionText.match(/\(MFD: (\d{4}-\d{2}-\d{2}), EXP: (\d{4}-\d{2}-\d{2}), Stock: (\d+)\)/);
        var mfd = null;
        var exp = null;
        var stock = null;

        var custom2 = selectedOption.data('custom2');
        var custom3 = selectedOption.data('custom3');
        var custom4 = selectedOption.data('custom4');
        
        if (match) {
            mfd = match[1]; 
            exp = match[2]; 
            stock = match[3]; 
        } 
        if(mfd) {
            row.append('<input type="hidden" value="'+ mfd +'" name="mfd[]">');
        }
        if(exp) {
            row.append('<input type="hidden" value="'+ exp +'" name="exp[]">');
        }
       if(stock) {
            row.find('input[name="quantity[]"]').attr('max', stock);
       }
       if(custom2){
        row.append('<input type="hidden" value="'+ custom2 +'" name="purchase_unit_id[]">');
       }
       if(batchNumber){
        row.append('<input type="hidden" value="'+ batchNumber +'" name="selected_batch_no[]">');
       }
       if(custom3){
        row.append('<input type="hidden" value="'+ custom3 +'" name="purchase_rate[]">');
       }
       if(custom4){
        row.append('<input type="hidden" value="'+ custom4 +'" name="sale_rate[]">');
       }
      

        });
        

</script>
<script>
function validateForm() {
    var isValid = true;

    // Loop through each dynamic row
    $('#productRowTemplate').siblings().each(function(index) {
        var medicineId = $(this).find('.medicine-name').val();
        var batchNo = $(this).find('.batch_no').val();
        var quantity = $(this).find('.medicine-quantity input').val();

        // Flag to track if error message has been displayed for the current row
        var errorDisplayed = false;

        // Check if any of the fields in the current row is empty
        if (medicineId === '' || batchNo === '' || quantity === '') {
            isValid = false;
            // Check if error message has already been displayed for this row
            if (!errorDisplayed) {
                // Display error message and set flag to true
Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: 'Please fill out all fields in row ' + (index + 1)
});
                errorDisplayed = true;
            }
        }
    });

    return isValid;
}
</script>
<script>
$(document).ready(function() {
    $('#subForm').submit(function() {
        return validateForm();
    });
});
</script>
