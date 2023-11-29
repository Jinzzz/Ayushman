@extends('layouts.app') @section('content') <div class="container">
  <div class="row" style="min-height: 70vh;">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h3 class="mb-0 card-title">Create Salary Package</h3>
        </div>
        <div class="col-lg-12" style="background-color: #fff;"> @if ($errors->any()) <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input. <br>
            <br>
            <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
          </div> @endif <form action="{{ route('packages.store') }}" method="POST" enctype="multipart/form-data"> @csrf <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label branch" id="branchLabel">Package Name*</label>
                  <input type="text" class="form-control" name="package_name" id="package_name" value="{{ old('package_name') }}" placeholder="Package Name">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label branch" id="branchLabel">Company Name*</label>
                  <input type="text" class="form-control" name="company_name" id="company_name" value="Ayushman" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <div class="form-label">Status</div>
                  <label class="custom-switch">
                    <input type="hidden" name="status" value="0">
                    <!-- Default value for Inactive -->
                    <input type="checkbox" id="statusSwitch" name="status" onchange="toggleStatus(this)" class="custom-switch-input" checked>
                    <span id="statusLabel" class="custom-switch-indicator"></span>
                    <span id="statusText" class="custom-switch-description">Active</span>
                  </label>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Salary Head</label>
                  <select class="form-control" name="salary_head_id" id="salary_head_id">
                    <option value="" disabled selected>Choose Salary Head</option> @foreach($heads as $head) <option value="{{ $head->id }}">{{ $head->salary_head_name }}</option> @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row" id="salaryHeadTypeRow" style="display: none;">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Salary Head Type</label>
                  <input type="text" class="form-control" name="salary_head_type_id" id="salary_head_type" readonly>
                  <input type="hidden" name="salary_head_type_id" id="salary_head_type_id" value="">

                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Package Amount Type</label>
                  <select class="form-control" name="package_amount_type" id="package_amount_type">
                    <option value="" disabled selected>Choose Amount Type</option>
                    <option value="Amount">Amount</option>
                    <option value="Percentage">Percentage</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label branch" id="branchLabel">Package Amount Value*</label>
                  <input type="number" class="form-control" name="package_amount_value" id="package_amount_value"  pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="{{ old('package_amount_value') }}" placeholder="Package Amount Value">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="form-label">Remark</label>
                  <textarea class="form-control" name="remark" placeholder="Remark">{{ old('remark') }}</textarea>
                </div>
              </div>
            </div>
        </div>
        </br>
        </br>
        <div class="form-group">
          <center>
            <button type="submit" class="btn btn-raised btn-primary">
              <i class="fa fa-check-square-o"></i> Add </button>
            <button type="reset" class="btn btn-raised btn-success"> Reset </button>
            <a class="btn btn-danger" href="{{ route('packages.index') }}">Cancel</a>
          </center>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div> @endsection @section('js') <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/latest/jquery.validate.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function toggleStatus(checkbox) {
    if (checkbox.checked) {
      $("#statusText").text('Active');
      $("input[name=status]").val(0); // Set the value to 0 when checked (Active)
    } else {
      $("#statusText").text('Inactive');
      $("input[name=status]").val(1); // Set the value to 1 when unchecked (Inactive)
    }
  }
</script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    var packageAmountType = document.getElementById("package_amount_type");
    var packageAmountValue = document.getElementById("package_amount_value");
    packageAmountValue.addEventListener("input", function() {
      if (packageAmountType.value === "Percentage" && parseFloat(packageAmountValue.value) > 100) {
        // Use SweetAlert2 to show a custom modal
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Percentage cannot be greater than 100%',
          confirmButtonText: 'OK'
        });
        // Optionally, reset the value or take other actions as needed
        packageAmountValue.value = "";
      }
    });
  });
</script>
<!-- Your existing JavaScript code -->
<!-- Your JavaScript code -->
<!-- ... Your other script imports ... -->

<script>
  document.addEventListener("DOMContentLoaded", function() {
    var salaryHeadIdDropdown = document.getElementById('salary_head_id');
    var salaryHeadTypeRow = document.getElementById('salaryHeadTypeRow');
    var salaryHeadTypeInput = document.getElementById('salary_head_type');
    var salaryHeadTypeIdInput = document.getElementById('salary_head_type_id'); // Add this line

    function updateSalaryHeadType() {
      var selectedHeadId = salaryHeadIdDropdown.value;
      if (selectedHeadId) {
        // Make an Ajax request to get the corresponding salary_head_type
        $.ajax({
          type: 'GET',
          url: '/getSalaryHeadType/' + selectedHeadId,
          success: function(data) {
            console.log('Data from server:', data);
            salaryHeadTypeInput.value = data.salary_head_type;
            salaryHeadTypeIdInput.value = data.salaryHeadTypeId; // Set the value of the hidden input
            console.log('Selected ID:', data.id);
            console.log('Salary Head Type ID:', data.salaryHeadTypeId); // Accessing the new value
            salaryHeadTypeRow.style.display = 'block';
          },
          error: function(xhr, status, error) {
            console.error('Error fetching salary head type:', error);
          }
        });
      } else {
        salaryHeadTypeInput.value = '';
        salaryHeadTypeIdInput.value = ''; // Reset the value when no option is selected
        salaryHeadTypeRow.style.display = 'none';
      }
    }

    // Initial update on page load
    updateSalaryHeadType();

    // Add an event listener to the salary_head_id dropdown
    salaryHeadIdDropdown.addEventListener('change', function() {
      updateSalaryHeadType();
    });
  });
</script>
