@extends('layouts.app')
@section('content')
<style>
select.medsearch { display: none !important; } span.current {
     font-size: 10px!important; } .table td {padding: 5px 3px;} .pricecard .form-group label {font-size: 12px;margin:0;} .pricecard .form-group span, .pricecard .form-group input {width: auto;padding: 0;border: unset;line-height: 1;height:auto;} .pricecard .form-group input:focus {outline: unset !important;border: none !important;} .pricecard .form-group {display: flex;align-items: center;gap: 16px;margin: 5px 0;}.pricecard .col-md-4 {margin-left: auto;}.dropdown-select {background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 100%);background-repeat: repeat-x;filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#40FFFFFF', endColorstr='#00FFFFFF', GradientType=0);background-color: #fff;border-radius: 6px;box-sizing: border-box;cursor: pointer;display: block;float: left;font-size: 14px;font-weight: normal;outline: none;padding-left: 18px;padding-right: 30px;position: relative;text-align: left !important;transition: all 0.2s ease-in-out;-webkit-user-select: none;-moz-user-select: none; -ms-user-select: none;user-select: none;white-space: nowrap; width: auto;}.dropdown-select:focus {background-color: #fff;}.dropdown-select:hover {background-color: #fff;}.dropdown-select:active,.dropdown-select.open {background-color: #fff !important;border-color: #bbb;box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) inset;}.dropdown-select:after {height: 0; width: 0; border-left: 4px solid transparent; border-right: 4px solid transparent; border-top: 4px solid #777;-webkit-transform: origin(50% 20%); transform: origin(50% 20%); transition: all 0.125s ease-in-out; content: ''; display: block; margin-top: -2px; pointer-events: none; position: absolute; right: 10px; top: 50%;}.dropdown-select.open:after { -webkit-transform: rotate(-180deg); transform: rotate(-180deg);}.dropdown-select.open .list { -webkit-transform: scale(1); transform: scale(1); opacity: 1; pointer-events: auto;}.dropdown-select.open .option {cursor: pointer;}.dropdown-select.wide {width: 100%;}.dropdown-select.wide .list {left: 0 !important;right: 0 !important;}.dropdown-select .list {box-sizing: border-box; transition: all 0.15s cubic-bezier(0.25, 0, 0.25, 1.75), opacity 0.1s linear; -webkit-transform: scale(0.75); transform: scale(0.75); -webkit-transform-origin: 50% 0; transform-origin: 50% 0; box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.09); background-color: #fff; border-radius: 6px; margin-top: 4px; padding: 3px 0; opacity: 0; overflow: hidden; pointer-events: none; position: absolute; top: 100%; left: 0; z-index: 999; max-height: 250px; overflow: auto; border: 1px solid #ddd;}.dropdown-select .list:hover .option:not(:hover) { background-color: transparent !important;}.dropdown-select .dd-search{overflow:hidden;display:flex;align-items:center;justify-content:center;margin:5px 0;}.dropdown-select .dd-searchbox{width:90%;padding:0.5rem;border:1px solid #999;border-color:#999;border-radius:4px;outline:none;line-height: 1;}.dropdown-select .dd-searchbox:focus{border-color:#12CBC4;}.dropdown-select .list ul { padding: 0;}.dropdown-select .option {cursor: default; font-weight: 400; line-height: 2; outline: none; padding-left: 10px; padding-right: 25px; text-align: left; transition: all 0.2s; list-style: none; font-size: 10px;}.dropdown-select .option:hover,.dropdown-select .option:focus {background-color: #f6f6f6 !important;}.dropdown-select .option.selected { font-weight: 600; color: #12cbc4;}.dropdown-select .option.selected:focus {background: #f6f6f6;}.dropdown-select a {color: #aaa; text-decoration: none; transition: all 0.2s ease-in-out;}.dropdown-select a:hover {
     color: #666;}
 </style>
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
                                        <div class="table-responsive" style="min-height: 200px;">
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
                                                            <select class="form-control medicine-name medsearch" name="medicine_id[]"
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
        create_custom_dropdowns();
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
                        batchSelect.append('<option value="' + item.stock_id + '">' + item.batch_no + ' (MFD: ' + item.mfd + ', EXP: ' + item.expd + ', Stock: ' + item.current_stock + ', Sale Rate: '+ item.sale_rate + ')</option>');
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

<script>

    function create_custom_dropdowns() {
     $('select.medsearch').each(function (i, select) {
         if (!$(this).next().hasClass('dropdown-select')) {
             $(this).after('<div class="dropdown-select wide ' + ($(this).attr('class') || '') + '" tabindex="0"><span class="current"></span><div class="list"><ul></ul></div></div>');
             var dropdown = $(this).next();
             var options = $(select).find('option');
             var selected = $(this).find('option:selected');
             dropdown.find('.current').html(selected.data('display-text') || selected.text());
             options.each(function (j, o) {
                 var display = $(o).data('display-text') || '';
                 dropdown.find('ul').append('<li class="option ' + ($(o).is(':selected') ? 'selected' : '') + '" data-value="' + $(o).val() + '" data-display-text="' + display + '">' + $(o).text() + '</li>');
             });
         }
         $(this).next().find('ul').before('<div class="dd-search"><input id="" autocomplete="off" onkeyup="filter(this)" class="dd-searchbox txtSearchValue" type="text"></div>');
     });
 
     
 }
 
 // Event listeners
 
 // Open/close
 $(document).on('click', '.dropdown-select', function (event) {
     if($(event.target).hasClass('dd-searchbox')){
         return;
     }
     $('.dropdown-select').not($(this)).removeClass('open');
     $(this).toggleClass('open');
     if ($(this).hasClass('open')) {
         $(this).find('.option').attr('tabindex', 0);
         $(this).find('.selected').focus();
     } else {
         $(this).find('.option').removeAttr('tabindex');
         $(this).focus();
     }
 });
 
 // Close when clicking outside
 $(document).on('click', function (event) {
     if ($(event.target).closest('.dropdown-select').length === 0) {
         $('.dropdown-select').removeClass('open');
         $('.dropdown-select .option').removeAttr('tabindex');
     }
     event.stopPropagation();
 });
 
 function filter(i){
     var valThis = $(i).val();
     $(i).closest('.dropdown-select').find('ul > li').each(function(){
      var text = $(this).text();
         (text.toLowerCase().indexOf(valThis.toLowerCase()) > -1) ? $(this).show() : $(this).hide();         
    });
 };
 // Search
 
 // Option click
 $(document).on('click', '.dropdown-select .option', function (event) {
     $(this).closest('.list').find('.selected').removeClass('selected');
     $(this).addClass('selected');
     var text = $(this).data('display-text') || $(this).text();
     $(this).closest('.dropdown-select').find('.current').text(text);
     $(this).closest('.dropdown-select').prev('select').val($(this).data('value')).trigger('change');
 });
 
 // Keyboard events
 $(document).on('keydown', '.dropdown-select', function (event) {
     var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
     // Space or Enter
     //if (event.keyCode == 32 || event.keyCode == 13) {
     if (event.keyCode == 13) {
         if ($(this).hasClass('open')) {
             focused_option.trigger('click');
         } else {
             $(this).trigger('click');
         }
         return false;
         // Down
     } else if (event.keyCode == 40) {
         if (!$(this).hasClass('open')) {
             $(this).trigger('click');
         } else {
             focused_option.next().focus();
         }
         return false;
         // Up
     } else if (event.keyCode == 38) {
         if (!$(this).hasClass('open')) {
             $(this).trigger('click');
         } else {
             var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
             focused_option.prev().focus();
         }
         return false;
         // Esc
     } else if (event.keyCode == 27) {
         if ($(this).hasClass('open')) {
             $(this).trigger('click');
         }
         return false;
     }
 });
 </script>
