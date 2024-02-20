@extends('layouts.app')
@section('content')
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
    <div class="container">
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger">
                            <p></p>
                        </div>
                    @endif
                    <div class="card-header">
                        <div class="col-md-6">
                            <h3 class="mb-0 card-title">Medicine Initial Stock Update</h3>
                        </div>
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
                        <form action="{{ route('updatestockmedicine') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Pharmacy*</label>
                                        <select class="form-control" required name="pharmacy_id" id="pharmacy_id" @if(session()->has('pharmacy_id')) disabled @endif>
                                            <option value="">Choose Pharmacy</option>
                                            @foreach ($pharmacies as $pharmacy)
                                                <option value="{{ $pharmacy->id }}" @if(session()->has('pharmacy_id') && session('pharmacy_id') == $pharmacy->id) selected @endif>
                                                    {{ $pharmacy->pharmacy_name }}
                                                </option>
                                            @endforeach
                                        </select>
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
                                                        <th>MFD</th>
                                                        <th>EXP</th>
                                                        <th>Stock</th>
                                                        <th>Purchase<br>rate</th>
                                                        <th>Sale<br>Rate</th>
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
                                                            <input type="text" class="form-control" required name="batch_no[]" id="batch_no">
                                                    </td>
                                                        <td class="medicine-stock-mfd equal-width-td">
                                                            <input type="date" class="form-control" required name="mfd[]" id="mfd"></td>
                                                        <td class="medicine-stock-exp equal-width-td">
                                                            <input type="date" class="form-control" required name="expd[]" id="expd"  placeholder="Batch Number"></td>
                                                        <td class="medicine-stock equal-width-td">
                                                            <input type="number" class="form-control" min="0" required name="new_stock[]" placeholder="New Stock"></td>
                                                        <td class="medicine-purchase-rate equal-width-td">
                                                            <input type="text" class="form-control" pattern="[0-9]+(\.[0-9]+)?" required name="purchase_rate[]" placeholder="Purchase Rate"></td>
                                                        <td class="medicine-sale-rate equal-width-td">
                                                            <input type="text" class="form-control" pattern="[0-9]+(\.[0-9]+)?" required name="sale_rate[]" placeholder="Sale Rate"></td>
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
                                    <button type="submit" class="btn btn-raised btn-primary">
                                        <i class="fa fa-check-square-o"></i> Update</button>
                                    <button type="reset" class="btn btn-raised btn-success">
                                        Reset</button>
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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/34.0.1/classic/ckeditor.js"></script>


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
            $("#productTable tbody").append(newRow);
            fetchBatchDetailsForRow(newRow);
        });
    });

    </script>
<script>
     $(document).ready(function() {
        $("#addProductBtn").click();
    });
</script>
