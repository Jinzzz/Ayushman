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
               <form action="{{ route('updatestockmedicine') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                  <div class="row">
                  <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Branch *</label>
                            <select class="form-control" required name="branch_id" id="branch_id">
                                <option value="">Choose Branch</option>
                                @foreach($branchs as $branch)
                    <option value="{{ $branch->branch_id  }}" {{ $branch->branch_id  == $medicines->branch_id ? 'selected' : '' }}>
                      {{ $branch->branch_name }}
                    </option>
                    @endforeach
                            </select>
                        </div>
                     </div>
                     <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Medicine *</label>
                        <select class="form-control" required name="medicine" id="medicine" readonly disabled>
                            <option value="">Choose Medicine</option>
                            @foreach($meds as $med)
                                <option value="{{ $med->id }}" {{ $med->id == $medicines->id ? 'selected' : '' }}>
                                    {{ $med->medicine_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>


                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Generic Name *</label>
                           <input type="text" class="form-control" required name="generic_name" id="generic_name"  placeholder="Generic Name" value="{{$medicines->generic_name}}" readonly>
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
                           <label class="form-label">Remarks</label>
                           <textarea class="form-control" name="remarks">{{ $medicines->remark }}</textarea>
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
<script src="https://cdn.ckeditor.com/ckeditor5/34.0.1/classic/ckeditor.js"></script>


<!-- Add the correct path to the CKEditor script -->
<script src="https://cdn.ckeditor.com/ckeditor5/34.0.1/classic/ckeditor.js"></script>

<script>
    $(document).ready(function () {
        // Fetch batch numbers based on the initially selected medicine at page load
        fetchBatchNumbers();

        // Fetch current stock based on the initially selected batch number at page load
        fetchCurrentStock();

        // Function to fetch batch numbers based on the selected medicine
        function fetchBatchNumbers() {
            var medicineId = $('#medicine').val();

            // Make an AJAX request to fetch batch numbers based on the selected medicine
            $.ajax({
                url: '/getBatchNumbers', // Replace with the actual route to fetch batch numbers
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'medicine_id': medicineId
                },
                success: function (data) {
                    // Clear existing options
                    $('#batch_no').empty();

                    // Add new batch numbers based on the response
                    $.each(data.batchNumbers, function (key, value) {
                        $('#batch_no').append('<option value="' + value + '">' + value + '</option>');
                    });
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }

        // Function to fetch current stock based on the selected batch number
        function fetchCurrentStock() {
            var medicineId = $('#medicine').val();
            var batchNo = $('#batch_no').val();

            // Make an AJAX request to get current stock
            $.ajax({
                url: '/get-current-stock/' + medicineId + '/' + batchNo,

                type: 'GET',
                success: function (data) {
                    // Update the value of the 'current_stock' input field
                    $('#current_stock').val(data.current_stock);
                },
                error: function (error) {
                    console.error('Error fetching current stock:', error);
                }
            });
        }

        // Make an AJAX request to get current stock based on selected batch number
        $('#batch_no').on('change', function () {
            fetchCurrentStock();
        });
    });
</script>

