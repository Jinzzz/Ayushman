@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
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
               <h3 class="mb-0 card-title">Medicine Stock Updation</h3>
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
               <form action="{{ route('update.medicine.stocks') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                  <div class="row">
                  <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Pharmacy*</label>
                            <select class="form-control" required name="branch_id" id="branch_id">
                                <option value="">Choose Branch</option>
                                @foreach($pharmacies as  $pharmacy)
                                <option value="{{ $pharmacy->id  }}">{{ $pharmacy->pharmacy_name }}</option>
                                @endforeach
                            </select>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Medicine *</label>
                            <select class="form-control" required name="medicine" id="medicine">
                                <option value="">Choose Medicine</option>
                                @foreach($medicines as $id => $value)
                                <option value="{{ $id }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Generic Name *</label>
                           <input type="text" class="form-control" required name="generic_name" id="generic_name"  placeholder="Generic Name" >
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Batch No</label>
                            <select class="form-control"  name="batch_no" id="batch_no">
                                <option value="">Choose Batch No</option>
                                <!-- Batch numbers will be dynamically added here -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Current Stock *</label>
                            <input type="text" class="form-control" required name="current_stock" id="current_stock" placeholder="Current Stock" readonly>
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">New Stock *</label>
                           <input type="number" class="form-control" min="0" required name="new_stock" placeholder="New Stock">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Remarks *</label>
                           <input type="text" class="form-control"  required name="remarks"  placeholder="Remarks">
                        </div>
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
                  <!-- ... -->
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
<script>
    // Fetch generic name, batch numbers, and current stock based on selected medicine
    document.getElementById('medicine').addEventListener('change', function () {
        var medicineId = this.value;

        // Make an AJAX request to get the generic name
        $.ajax({
            url: '/get-generic-name/' + medicineId,
            type: 'GET',
            success: function (data) {
                // Update the value of the 'generic_name' input field
                document.getElementById('generic_name').value = data.generic_name;
            },
            error: function (error) {
                console.error('Error fetching generic name:', error);
            }
        });

        // Make an AJAX request to get batch numbers
        $.ajax({
            url: '/get-batch-numbers/' + medicineId,
            type: 'GET',
            success: function (data) {
                // Update the options of the 'batch_no' dropdown
                var batchNoDropdown = document.getElementById('batch_no');
                batchNoDropdown.innerHTML = '<option value="">Choose Batch No</option>';
                data.batch_numbers.forEach(function (batchNumber) {
                    var option = document.createElement('option');
                    option.value = batchNumber;
                    option.text = batchNumber;
                    batchNoDropdown.appendChild(option);
                });
            },
            error: function (error) {
                console.error('Error fetching batch numbers:', error);
            }
        });
    });

    // Make an AJAX request to get current stock based on selected batch number
    document.getElementById('batch_no').addEventListener('change', function () {
        var medicineId = document.getElementById('medicine').value;
        var batchNo = this.value;

        // Make an AJAX request to get current stock
        $.ajax({
            url: '/get-current-stock/' + medicineId + '/' + batchNo,
            type: 'GET',
            success: function (data) {
                // Update the value of the 'current_stock' input field
                document.getElementById('current_stock').value = data.current_stock;
            },
            error: function (error) {
                console.error('Error fetching current stock:', error);
            }
        });
    });
</script>
@endsection
