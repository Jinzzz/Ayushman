@extends('layouts.app')
@section('content')
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
    </style> --}}
    <div class="container">
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
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Pharmacy From*</label>
                                        <select class="form-control" required name="pharmacy_from" id="pharmacy_from">
                                            <option value="">--Select Pharmacy--</option>
                                            @foreach ($pharmacies as $id => $pharmacy)
                                            <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Pharmacy To*</label>
                                        <select class="form-control" required name="pharmacy_to" id="pharmacy_to">
                                            <option value="">--Select Pharmacy--</option>
                                            @foreach ($pharmacies as $id => $pharmacy)
                                            <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
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
                                                            <select class="form-control medicine-name" name="medicine_id[]"
                                                                dis>
                                                                <option value="">Please select medicine</option>
                                                                
                                                                @foreach ($medicines as $id => $medicine)
                                                                <option value="{{ $medicine->id }}">{{ $medicine->medicine_name }}</option>
                                                                @endforeach

                                                            </select>
                                                        </td>
                                                        <td class="medicine-batch-no">
                                                            <select class="form-control batch_no" required name="batch_no[]">
                                                            <option value="">--Batch--</option>
                                                        </select>
                                                    </td>
                                                        <td class="medicine-quantity"><input type="number" min="1"
                                                                class="form-control" value="" name="quantity[]"></td>
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
                                         <textarea class="form-control" name="notes" placeholder="Notes"></textarea>
                                      </div>
                                      <div class="form-group">
                                         <label class="form-label">Reference File:</label>
                                         <input type="file" class="form-control" name="reference_file">
                                      </div>
                                   </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <center>
                                    <button type="submit" class="btn btn-raised btn-primary">
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
    </div>
@endsection
@section('js')
@endsection
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<!--<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>-->
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

//exclude pharmacy from dropdown
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
    function fetchBatchDetailsForRow(row) {
        var medicineId = row.find('.medicine-name').val();
        var pharmacyId = $('#pharmacy_from').val();

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
                var batchSelect = row.find('.batch_no');
                batchSelect.empty();
                batchSelect.append('<option value="">--Select Batch--</option>');
                
                if (response && response.length > 0) {
                    $.each(response, function(index, item) {
                        batchSelect.append('<option value="' + item.stock_id + '">' + item.batch_no + ' (MFD: ' + item.mfd + ', EXP: ' + item.expd + ', Stock: ' + item.current_stock + ')</option>');
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

    $("#addProductBtn").click(function(event) {
        event.preventDefault();
        var newRow = $("#productRowTemplate").clone().removeAttr("style");
        newRow.find('select').addClass('medicine-select');
        newRow.find('input').val('').prop('readonly', false);
        newRow.find('input span').remove();
        $("#productTable tbody").append(newRow);
        fetchBatchDetailsForRow(newRow);
    });
});
</script>
