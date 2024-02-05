@extends('layouts.app') @section('content') <div class="container">
  <div class="row" style="min-height: 70vh;">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h3 class="mb-0 card-title">Create Therapy Stock</h3>
        </div>
        <div class="col-lg-12" style="background-color: #fff;"> @if ($errors->any()) <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input. <br>
            <br>
            <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
          </div> @endif 
          <form action="{{ route('therapy-stock-transfers.store') }}" id="addFm" method="POST" enctype="multipart/form-data">
             @csrf 
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label branch" id="branchLabel">Therapy*</label>
                  <select class="form-control" name="therapy_id" id="therapy_id">
                    <option value="" disabled selected>Choose Therapy</option> @foreach($therapys as $therapy) <option value="{{ $therapy->id }}" {{ old('therapy_id') == $therapy->id ? 'selected' : '' }}>
                      {{ $therapy->therapy_name }}
                    </option> @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label branch" id="branchLabel">Medicine*</label>
                  <select class="form-control" name="medicine_id" id="medicine_id">
                    <option value="" disabled selected>Choose Medicine</option> @foreach($medicines as $medicine) <option value="{{ $medicine->id }}" {{ old('medicine_id') == $medicine->id ? 'selected' : '' }}>
                      {{ $medicine->medicine_name }}
                    </option> @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Batch No*</label>
                  <select class="form-control" name="batch_id" id="batch_no">
                    <option value="" selected disabled>Choose Batch No</option>
                    <!-- Batch numbers will be dynamically added here -->
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Current Stock *</label>
                  <input type="text" class="form-control" required name="current_stock" id="current_stock" placeholder="Current Stock" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Transfer Quantity*</label>
                  <input type="number" class="form-control" required pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="transfer_quantity" id="transfer_quantity" placeholder="Transfer Quantity">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Transfer Date*</label>
                  <input type="date" class="form-control" required name="transfer_date" id="transfer_date" placeholder="Transfer Date">
                </div>
              </div>
            </div>
            <div class="form-group">
              <center>
                <button type="submit" id="submitForm" class="btn btn-raised btn-primary">
                  <i class="fa fa-check-square-o"></i> Add </button>
                <a class="btn btn-success" href="{{ route('therapy-stock-transfers.create') }}">Reset</a>
                <a class="btn btn-danger" href="{{ route('therapy-stock-transfers.index') }}">Cancel</a>
              </center>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div> @endsection
<!-- Other HTML content... --> @section('js') <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


<script>
  // Fetch generic name, batch numbers, and current stock based on selected medicine
  $(document).ready(function() {
    // Function to fetch and update batch numbers
    function updateBatchNumbers() {
        var medicineId = $('#medicine_id').val();

        // Make an AJAX request to get batch numbers
        $.ajax({
            url: '/get-medicine-batch/' + medicineId,
            type: 'GET',
            success: function(data) {
                // Update the options of the 'batch_no' dropdown
                var batchNoDropdown = $('#batch_no');
                batchNoDropdown.html('<option value="">Choose Batch No</option>');
                $.each(data.batch_numbers, function(index, batchNumber) {
                    batchNoDropdown.append($('<option>', {
                        value: batchNumber,
                        text: batchNumber
                    }));
                });

                // After updating batch numbers, fetch and update current stock for the first batch
                var firstBatchNumber = data.batch_numbers.length > 0 ? data.batch_numbers[0] : '';
                $('#batch_no').val(firstBatchNumber); // Select the first batch
                updateCurrentStock();
            },
            error: function(error) {
                console.error('Error fetching batch numbers:', error);
            }
        });
    }

        // Update the condition in your 'submitForm' click event
        $(document).on('keyup', '#transfer_quantity', function () {
    // Check if the form is valid according to your validation rules
    if (validator.form()) {
        // Retrieve transfer quantity and current stock values
        var transferQuantity = parseInt($(this).val()); // Get the value from the current input field
        var currentStock = parseInt($('#current_stock').val());

        // Check if transfer quantity is greater than current stock
        if (transferQuantity > currentStock) {
            // Show SweetAlert message for the error case
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Transfer quantity cannot be greater than the current stock!',
            });

            // Clear the transfer quantity field
            $(this).val('');
        }
    }
});

    // Function to fetch and update current stock
    function updateCurrentStock() {
        var medicineId = $('#medicine_id').val();
        var batchNo = $('#batch_no').val();

        // Make an AJAX request to get current stock
        $.ajax({
            url: '/get-current-medicine-stock/' + medicineId + '/' + batchNo,
            type: 'GET',
            success: function(data) {
                // Update the value of the 'current_stock' input field
                $('#current_stock').val(data.current_stock);
            },
            error: function(error) {
                console.error('Error fetching current stock:', error);
            }
        });
    }

    // Trigger the functions on page load
    updateBatchNumbers();

    // Attach the event listeners to 'medicine_id' and 'batch_no' changes
    $('#medicine_id').on('change', function() {
        updateBatchNumbers();
    });

    $('#batch_no').on('change', function() {
        updateCurrentStock();
    });
});

  // Get the current date in the format YYYY-MM-DD
  function getCurrentDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }
  // Set the current date as the default value
  document.getElementById('transfer_date').value = getCurrentDate();
</script>
<script type="text/javascript">
  var validator; // Declare validator outside $(document).ready()
  $(document).ready(function() {
    validator = $("#addFm").validate({
      rules: {
        medicine_id: "required",
        therapy_id: "required",
        batch_id: "required",
        transfer_quantity: "required",
        transfer_date: "required",
        created_by: "required",
      },
      messages: {
        medicine_id: "Please select medicine.",
        therapy_id: "Please select Therapy.",
        batch_id: "Please select batch.",
        transfer_quantity: "Please enter total quantity.",
        transfer_date: "Please enter transfer date.",
        created_by: "Please enter created by.",
      },
      submitHandler: function(form) {
        // Your form submission logic here
        form.submit();
      },
    });
    $(document).on('click', '#submitForm', function() {
      if (validator.form()) {
        $('#addFm').submit();
      } else {
        flashMessage('w', 'Please fill all mandatory fields');
      }
    });

    function flashMessage(type, message) {
      // Implement or replace this function based on your needs
      console.log(type, message);
    }
  });
</script>
 @endsection