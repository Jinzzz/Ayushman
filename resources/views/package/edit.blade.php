@extends('layouts.app') @section('content') <div class="container">
  <div class="row" style="min-height: 70vh;">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h3 class="mb-0 card-title">Edit Salary Package</h3>
        </div>
        <div class="col-lg-12" style="background-color: #fff;"> @if ($errors->any()) <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input. <br>
            <br>
            <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
          </div> @endif <form action="{{ route('packages.update', ['id' => $id]) }}" method="POST" enctype="multipart/form-data"> @csrf @method('PUT') <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Package Name</label>
                  <input type="text" class="form-control" name="package_name" value="{{ $packages->package_name }}" placeholder="Staff Name">
                </div>
              </div>
            </div>
            <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Salary Head Name</label>
                    <select class="form-control" name="salary_head_id" id="salary_head_id">
                        <option value="" disabled selected>Choose Salary Head Type</option>
                        @foreach($heads as $head)
                            <option value="{{ $head->id }}" {{ $head->salary_head_name == $packages->salary_head_name ? 'selected' : '' }}>
                                {{ $head->salary_head_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Salary Head Type</label>
                    <input type="text" class="form-control" name="salary_head_type_id" id="salary_head_type" value="{{ $packages->salary_head_type }}" readonly>
                    <input type="hidden" name="selected_salary_head_type_id" id="selected_salary_head_type_id" value="{{ $packages->selected_salary_head_type_id ?? '' }}">
                </div>
            </div>
        </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Package Amount Type</label>
                  <select class="form-control" name="package_amount_type" id="package_amount_type">
                    <option value="" disabled>Choose Amount Type</option>
                    <option value="Amount" {{ $packages->package_amount_type === 'Amount' ? 'selected' : '' }}>Amount</option>
                    <option value="Percentage" {{ $packages->package_amount_type === 'Percentage' ? 'selected' : '' }}>Percentage</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label branch" id="branchLabel">Package Amount Value*</label>
                  <input type="number" class="form-control" name="package_amount_value" id="package_amount_value" value="{{ $packages->package_amount_value }}" placeholder="Package Amount Value">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="custom-switch">
                    <input type="checkbox" id="is_active" name="status" onchange="toggleStatus(this)" class="custom-switch-input" @if($packages->status) checked @endif> <span id="statusLabel" class="custom-switch-indicator"></span>
                    <span id="statusText" class="custom-switch-description"> @if($packages->status) Active @else Inactive @endif </span>
                  </label>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="form-label">Remarks</label>
                  <textarea class="form-control" name="remark" placeholder="Reason For Leave">{{ $packages->remark ?? old('remark') }}</textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <center>
                    <button type="submit" class="btn btn-raised btn-primary">
                      <i class="fa fa-check-square-o"></i> Update </button>
                    <button type="reset" class="btn btn-raised btn-success"> Reset </button>
                    <a class="btn btn-danger" href="{{ route('packages.index') }}">Cancel</a>
                  </center>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div> @endsection @section('js') <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
  function toggleStatus(checkbox) {
    if (checkbox.checked) {
      $("#statusText").text('Active');
      $("input[name=status]").val(1); // Set the value to 1 when checked (Active)
    } else {
      $("#statusText").text('Inactive');
      $("input[name=status]").val(0); // Set the value to 0 when unchecked (Inactive)
    }
  }
</script>
<script>
$(document).ready(function() {
    // Trigger change event on salary_head_id dropdown on page load
    $('#salary_head_id').trigger('change');

    // Handle change event on salary_head_id dropdown
    $('#salary_head_id').change(function() {
        var selectedHeadId = $(this).val();

        // Make an Ajax request to get corresponding salary_head_type
        $.ajax({
            type: 'GET',
            url: '/getSalaryHeadType/' + selectedHeadId,
            success: function(data) {
                // Update the value of the input field with the received salary_head_type
                $('#salary_head_type').val(data.salary_head_type);
                // Update the hidden input field with the selected salary_head_type_id
                $('#selected_salary_head_type_id').val(data.salaryHeadTypeId);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching salary head type:', error);
            }
        });
    });
});
</script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    var packageAmountType = document.getElementById("package_amount_type");
    var packageAmountValue = document.getElementById("package_amount_value");
    packageAmountValue.addEventListener("input", function() {
      console.log("Input event triggered");
      console.log("Type:", packageAmountType.value);
      console.log("Value:", packageAmountValue.value);
      if (packageAmountType.value === "Percentage" && parseFloat(packageAmountValue.value) > 100) {
        console.log("Showing SweetAlert2 error");
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Percentage cannot be greater than 100%',
          confirmButtonText: 'OK'
        });
        // Optionally, reset the value or take other actions as needed
        // Replace with the actual values
        packageAmountType.value = "Percentage";
        packageAmountValue.value = ""; // Test with a value greater than 100
      }
    });
  });
</script> @endsection