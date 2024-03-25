@extends('layouts.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
  body {
    margin-top: 40px;
  }

  .stepwizard-step p {
    margin-top: 10px;
  }

  .app-content {
    min-height: unset !important;
  }

  .stepwizard-row {
    display: table-row;
  }

  .stepwizard {
    display: table;
    width: 100%;
    position: relative;
  }

  .custom-control-label {
    position: relative !important;
  }

  .card {
    background-color: #fff !important;
    padding: 10px;
  }

  .stepwizard-step button[disabled] {
    opacity: 1 !important;
    filter: alpha(opacity=100) !important;
  }

  .stepwizard-row:before {
    top: 14px;
    bottom: 0;
    position: absolute;
    content: " ";
    width: 100%;
    height: 1px;
    background-color: #ccc;
    z-order: 0;
  }

  .stepwizard-step {
    display: table-cell;
    text-align: center;
    position: relative;
  }

  .btn-circle {
    width: 30px;
    height: 30px;
    text-align: center;
    padding: 6px 0;
    font-size: 12px;
    line-height: 1.428571429;
    border-radius: 15px;
  }

  .custom-control-label::before {
    top: 0;
  }

  .custom-control-label::after {
    top: 0;
  }

  .error-border {
    border: 2px solid red;
  }

  .error-message {
    color: red;
    font-size: 12px;
  }


  .swal-overlay {
    z-index: 9999 !important;
  }
</style>
<div class="container">
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
  <div class="stepwizard">
    <div class="stepwizard-row setup-panel">
      <div class="stepwizard-step">
        <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
        <p>Personal Info</p>
      </div>
      <div class="stepwizard-step">
        <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
        <p>Salary Info</p>
      </div>
      <div class="stepwizard-step">
        <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
        <p>Available Leave</p>
      </div>
    </div>
  </div>
  <form action="{{ route('staffs.store') }}" id="addFm" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row setup-content" id="step-1">
      <div class="card">
        <div class="col-md-12">
          <div class="col-md-12">
            <h3>Personal Info</h3>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Staff Type*</label>
                  <select class="form-control" name="staff_type" id="staff_type">
                    <option value="">Select Staff Type</option>
                    @foreach($stafftype as $masterId => $masterValue) <option value="{{ $masterId }}" {{ old('staff_type') == $masterId ? 'selected' : '' }}>
                      {{ $masterValue }}
                    </option> @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6" id="consultation_fees_field" style="display: none;">
                <div class="form-group">
                  <label class="form-label">Consultation Fees*</label>
                  <input type="text" class="form-control" name="consultation_fees" id="consultation_fees" placeholder="Consultation Fees">
                  <span class="required-message" style="display: none; color: red;">Consultation Fees is required</span>
                </div>
              </div>

              <div class="col-md-6" id="pharmacySelectBox" style="display: none;">
                <div class="form-group">
                  <label class="form-label">Pharmacy*</label>
                  <select class="form-control" name="pharmacy[]" multiple id="pharmacy_id" required>
                    <option value="" selected disabled>Select Pharmacy</option>
                    @foreach ($pharmacies as $pharmacy)
                    <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Employment Type*</label>
                  <select class="form-control" name="employment_type" id="employment_type ">
                    <option value="">Select Employment Type</option>
                    @foreach($employmentType as $masterId => $masterValue) <option value="{{ $masterId }}" {{ old('employment_type') == $masterId ? 'selected' : '' }}>
                      {{ $masterValue }}
                    </option> @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Staff Name*</label>
                  <input type="text" class="form-control" name="staff_name" maxlength="100" value="{{ old('staff_name') }}" placeholder="Staff Name">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="patient_gender" class="form-label">Gender*</label>
                  <select class="form-control" name="gender" id="gender">
                    <option value="">Choose Gender</option>
                    @foreach($gender as $id => $gender) <option value="{{ $id }}" {{ old('gender') == $id ? 'selected' : '' }}>
                      {{ $gender }}
                    </option> @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label branch" id="branchLabel">Branch*</label>
                  <select class="form-control" name="branch_id" id="branch_field">
                    <option value="">Choose Branch</option>
                    @foreach($branchs as $branch) <option value="{{ $branch->branch_id }}" {{ old('branch_id') == $branch->branch_id ? 'selected' : '' }}>
                      {{ $branch->branch_name }}
                    </option> @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Date Of Birth*</label>
                  <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" placeholder="Date Of Birth">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Email*</label>
                  <input type="email" class="form-control" name="staff_email" onblur="checkemail()" id="staff_email_id" value="{{ old('staff_email') }}" placeholder="Staff Email">

                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Contact Number*</label>
                  <input type="number"  class="form-control" name="staff_contact_number" id="numericInput"  value="{{ old('staff_contact_number') }}" placeholder="Contact Number" pattern="[0-9]+" title="Please enter digits only" inputmode="numeric">

                  <p class="error-message" style="color: green; display: none;">Please enter digits only.</p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Address*</label>
                  <textarea class="form-control" name="staff_address" placeholder="Address">{{ old('staff_address') }}</textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Qualification*</label>
                  <input type="text" class="form-control" name="staff_qualification" maxlength="100" value="{{ old('staff_qualification') }}" placeholder="Qualification" oninput="this.value = this.value.replace(/[0-9]/g, '');">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Specialization*</label>
                  <input type="text" class="form-control" name="staff_specialization" maxlength="255" value="{{ old('staff_specialization') }}" placeholder="Specialization" oninput="this.value = this.value.replace(/[0-9]/g, '');">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Work Experience(In Years)*</label>
                  <input type="text" class="form-control" name="staff_work_experience" maxlength="5" value="{{ old('staff_work_experience') }}" placeholder="Work Experience" pattern="[0-9]+" title="Please enter digits only">
                  <p class="error-message" style="color: green; display: none;">Please enter digits only.</p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Commission Type*</label>
                  <select class="form-control" name="staff_commission_type" id="staff_commission_type" onchange="updateCommissionPlaceholder()">
                    <option value="">Select Commission Type</option>
                    <option value="percentage" {{ old('staff_commission_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                    <option value="fixed" {{ old('staff_commission_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                  </select>

                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Staff Commission*</label>
                  <input type="text" class="form-control" name="staff_commission"  id="numericInput1" id="staff_commission" value="{{ old('staff_commission') }}" placeholder="Staff Commission" oninput="validateCommission(this);">
                  <span id="commission-error" style="color: red;"></span>
                </div>
              </div>
            </div>
            <div class="row">

              <div class="col-md-6">
                <div class="form-group">
                  <div class="form-label"></div>
                  <label class="form-label">Access Card Number*</label>
                  <input type="text" class="form-control" name="access_card_number" onkeyup="checkaccesscardnumber()" id="access_card_number_id" value="{{ old('access_card_number') }}" id="access_card_number" placeholder="Access Card Number">
                  </label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <div class="form-label"></div>
                  <label class="form-label">Date Of Join*</label>
                  <input type="date" class="form-control" name="date_of_join" value="{{ old('date_of_join') ?: date('Y-m-d') }}" placeholder="Date Of Join">
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Status</label>
                <label class="custom-switch">
                  <input type="hidden" name="is_active" value="0">
                  <!-- Hidden field for false value -->
                  <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" checked>
                  <span id="statusLabel" class="custom-switch-indicator"></span>
                  <span id="statusText" class="custom-switch-description">Active</span>
                </label>
              </div>
            </div>
            <!-- Add other fields for personal info section -->
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Has Login</label>
                <label class="custom-control custom-checkbox" style="padding-left:0;">
                  <input type="checkbox" class="custom-control-input" name="is_login" id="is_login" {{ old('is_login') ? 'checked' : '' }} onchange="toggleLoginFields()">
                  <span class="custom-control-label" style="padding-left:22px;">Has Login</span>
                </label>
              </div>
            </div>
          </div>
          <button class="btn btn-primary nextBtn btn-lg pull-right" type="button">Next</button>
          <div id="login_fields" style="display: none;">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Staff Username*</label>
                  <input type="text" class="form-control" name="staff_username" onblur="checkusername()" id="staff_username_id" value="{{ old('staff_username') }}" placeholder="Staff Username">
                </div>
                <label class="form-label" style="color:#0d97c6;">Note: Password will be generated and shared to registered E-mail.</label>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Staff Discount Percentage</label>
                  <input type="text" class="form-control" name="discount_percentage" onkeyup="checkDiscountPercentage()" id="discount_percentage" value="{{ old('discount_percentage') }}" placeholder="Staff Discount Percentage">
                </div>
              </div>
              <!-- Add other login fields here -->
            </div>
          </div>
        </div>
      </div>
      <!-- ... (previous code) ... -->
      <!-- ... (remaining code) ... -->
    </div>
</div>
</div>
</div>
<div class="row setup-content abc" id="step-2" style="padding: 10px;background-color:#fff;margin-bottom:10px">
  <div class="col-xs-12" style="margin-left: 330px;">
    <div class="col-md-12">
      <h3>Salary Info</h3>
      <div class="row" id="salaryContainer">
        <div class="col-md-3">
          <div class="form-group">
            <label class="form-label">Salary Head*</label>
            <select class="form-control salary_head" name="salary_head_id[]" id="salary_head_id">
              <option value="" disabled selected>Choose Salary Head</option>
              @foreach($heads as $head)
              <option value="{{ $head->id }}">{{ $head->salary_head_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="form-label">Salary Head Type*</label>
            <input type="text" class="form-control salary_head_types" name="salary_head_type_id[]" id="salary_head_type" readonly style="background: #dfdddd!important;">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label class="form-label">Salary Amount*</label>
            <input type="number" class="form-control" name="amount[]" value="{{ old('amount.0') }}" placeholder="Salary Amount">

          </div>
        </div>
        <div class="col-md-3" data-initial-row id="salaryContainer">
          <div class="form-group">
            <label class="form-label">Actions</label>
            <button type="button" class="btn btn-danger remove-row" disabled>Remove</button>
          </div>
        </div>
      </div>


      <div class="row" style="margin-top: 20px;">
        <div class="col-md-6">
          <button type="button" class="btn btn-primary" id="addProductBtn">Add Salary Row</button>
        </div>

        <div class="col-md-6" style="display: flex; justify-content:center;">
          <button class="btn btn-primary nextBtn btn-lg pull-right" type="button">Next</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- Step 3: Available Leaves -->
<div class="row setup-content" id="step-3" style="margin-left: 320px;">
  <div class="col-md-12">
    <h3>Available Leaves</h3>
    <div class="row">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Is Applicable</th>
            <th scope="col">Leave Type</th>
            <th scope="col">Credit Period</th>
            <th scope="col">Credit Limit</th>
          </tr>
        </thead>
        <tbody>
          @for ($i = 0; $i < count($leave_types); $i++) <tr>
            <td>
              <div class="custom-checkbox">
                <input type="checkbox" class="custom-control-input custom-control-input-lg" name="leave_type[]" id="customCheck{{ $i + 1 }}" value="{{ $leave_types[$i]->leave_type_id }}">
                <input type="hidden" name="leave_type_id[]" value="{{ $leave_types[$i]->leave_type_id }}">
                <label class="custom-control-label" for="customCheck{{ $i + 1 }}"></label>
              </div>
            </td>
            <td>{{ $leave_types[$i]->name }}</td>
            <td>
              <select class="form-control" name="credit_period[]" id="credit_period_0_{{ $leave_types[$i]->leave_type_id }}" disabled>
                <option value="" disabled selected>Select Credit Period</option>
                <option value="Monthly">Monthly</option>
                <option value="Yearly">Yearly</option>
              </select>
            </td>
            <td>
              <div class="form-group">
                <input class="form-control" type="number" id="credit_limit_0" name="credit_limit[]" placeholder="Enter Credit Limit" disabled>
              </div>
            </td>
            </tr>
            @endfor
        </tbody>
      </table>
    </div>
    <div class="text-center">
      <button class="btn btn-success btn-lg" type="button" id="finalSubmit">Submit</button>
    </div>
    <br><br>
  </div>
</div>
</form>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<link rel="stylesheet" type="text/css" href="path/to/sweetalert.css">
<script src="path/to/sweetalert.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">




<script>
  $(document).ready(function() {
    var navListItems = $('div.setup-panel div a'),
      allWells = $('.setup-content'),
      allNextBtn = $('.nextBtn');

    allWells.hide();

    navListItems.click(function(e) {
      e.preventDefault();
      var $target = $($(this).attr('href')),
        $item = $(this);

      if (!$item.hasClass('disabled')) {
        navListItems.removeClass('btn-primary').addClass('btn-default');
        $item.addClass('btn-primary');
        allWells.hide();
        $target.show();
        $target.find('input:eq(0)').focus();
      }
    });

    allNextBtn.click(function() {
      var curStep = $(this).closest(".setup-content"),
        curStepBtn = curStep.attr("id"),
        nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
        curInputs = curStep.find("input[type='text'],input[type='url']"),
        isValid = true;

      $(".form-group").removeClass("has-error");
      for (var i = 0; i < curInputs.length; i++) {
        if (!curInputs[i].validity.valid) {
          isValid = false;
          $(curInputs[i]).closest(".form-group").addClass("has-error");
        }
      }

      if (isValid)
        nextStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-primary').trigger('click');
  });

  // Function to toggle the visibility of the Username, Password, and Confirm Password fields based on the "Is Login" checkbox
  function toggleLoginFields() {
    var isLoginCheckbox = document.getElementById('is_login');
    var loginFields = document.getElementById('login_fields');
    if (isLoginCheckbox.checked) {
      loginFields.style.display = 'block'; // Show the login fields
    } else {
      loginFields.style.display = 'none'; // Hide the login fields
    }
  }

  // Attach click event to the remove button using event delegation
  $(document).ready(function() {
    // Function to handle the "Remove" button click
    $(document).on('click', '.remove-row', function() {
      // Find the parent row
      var parentRow = $(this).closest('.row');

      // Check if it's the initial row; if not, remove it
      if (!parentRow.hasClass('initial-row')) {
        parentRow.remove();
      }
    });

    $("#finalSubmit").click(function() {
      // Remove existing hidden fields to avoid duplication
      $('#addFm [name^="salary_head_id"]').remove();
      $('#addFm [name^="salary_head_type_id"]').remove();
      $('#addFm [name^="amount"]').remove();

      // Create an array to store data for dynamically added rows
      var dataArray = [];

      // Iterate over each row with the id starting with 'salaryContainer'
      $('[id^="salaryContainer"]').each(function(index, element) {
        var row = $(element);

        // Extract values from the current row and push them to the dataArray
        var salary_head_id = row.find('.salary_head').val();
        var salary_head_type_id = row.find('.salary_head_types').val();
        var amount = row.find('[name="amount[]"]').val();

        // Only append hidden fields when valid data is available
        if (salary_head_id && salary_head_type_id && amount) {
          dataArray.push({
            'salary_head_id': salary_head_id,
            'salary_head_type_id': salary_head_type_id,
            'amount': amount,
          });
        }

        // Add more fields as needed
      });

      // Append the dataArray to the form using a traditional for loop
      for (var i = 0; i < dataArray.length; i++) {
        $('<input>').attr({
          type: 'hidden',
          name: 'salary_head_id[]',
          value: dataArray[i].salary_head_id
        }).appendTo('#addFm');

        $('<input>').attr({
          type: 'hidden',
          name: 'salary_head_type_id[]',
          value: dataArray[i].salary_head_type_id
        }).appendTo('#addFm');

        $('<input>').attr({
          type: 'hidden',
          name: 'amount[]',
          value: dataArray[i].amount
        }).appendTo('#addFm');

        // Add more fields as needed
      }

      //validation
      const validationErr = validateLeaves()
      if(validationErr){
        // alert("Please fill all checked fields");
        return;
      }
      
      // Submit the form
      $('#addFm').submit();
    });

    function validateLeaves(params) {
      let err = false
      const checkboxes = document.querySelectorAll('input[name="leave_type[]"]');
      checkboxes.forEach(function(checkbox, index) {
        console.log(checkbox,"checkboxcheckboxcheckbox");
        if(checkbox.checked){
          var row = checkbox.closest('tr');
          // Find credit period and credit limit fields in the same row
          var creditPeriodField = row.querySelector('select[name="credit_period[]"]');
          var creditLimitField = row.querySelector('input[name="credit_limit[]"]');
          if(!creditPeriodField.value || !creditLimitField.value){
            err = true
          }
        }
      })
      return err
    }




    $("#addProductBtn").click(function(event) {

      event.preventDefault();
      var uniqueID = Date.now();
      var newIndex = $('.row[id^="salaryContainer"]').length + 1;
      var newRow = $('<div class="row" id="salaryContainer' + newIndex + '">' +
        '<div class="col-md-3">' +
        '<div class="form-group">' +
        '<label class="form-label">Salary Head*</label>' +
        '<select class="form-control salary_head" name="salary_head_id[]">' +
        '<option value=""disabled selected>Choose Salary Head</option>' +
        '@foreach($heads as $head)' +
        '<option value="{{ $head->id }}">{{ $head->salary_head_name }}</option>' +
        '@endforeach' +
        '</select>' +
        '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
        '<div class="form-group">' +
        '<label class="form-label">Salary Head Type*</label>' +
        '<input type="text" class="form-control salary_head_types" name="salary_head_type_id[]" id="salary_head_type" readonly>' +
        '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
        '<div class="form-group">' +
        '<label class="form-label">Salary Amount*</label>' +
        '<input type="number" class="form-control" name="amount[]" placeholder="Salary Amount">' +
        '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
        '<div class="form-group">' +
        '<label class="form-label">Actions</label>' +
        '<button type="button" class="btn btn-danger remove-row">Remove</button>' +
        '</div>' +
        '</div>' +
        '</div>');
      // Append the new row to the table
      $("#salaryContainer").after(newRow);
      $("#step-2").find(".abc").append(newRow);
    });

  });

  $(document).ready(function() {
    // Existing code...

    // Delegate the change event to a static parent element
    $(document).on('change', '.salary_head', function() {
      var selectedHeadId = $(this).val();
      var salaryHeadTypeInput = $(this).closest('.row').find('.form-group input[type="text"]');

      // Make an Ajax request to fetch the corresponding salary head type
      $.ajax({
        url: '/getSalaryHeadType/' + selectedHeadId,
        type: 'GET',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.salary_head_type) {
            // Update the input field with the fetched salary head type
            salaryHeadTypeInput.val(response.salary_head_type);
          } else {
            // Handle errors or clear the input field if needed
            salaryHeadTypeInput.val('');
          }
        },
        error: function(xhr, status, error) {
          // Handle errors here
          console.error(xhr.responseText);
        }
      });
    });

    // Existing code...
  })
</script>

<script>
  function checkemail() {
    var emailValue = $('#staff_email_id').val();
    // alert(emailValue);
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
      type: 'POST',
      url: '/checkUniqueEmail',
      headers: {
        'X-CSRF-TOKEN': csrfToken
      },
      data: {
        email: emailValue
      },
      success: function(response) {
        if (response) {
          if (!response.status) {
            Swal.fire({
              icon: 'warning',
              title: 'Email Already Exists',
              text: 'The email address already exists.'
            });
            $('#staff_email_id').val("");
          } else {
            Swal.fire({
              icon: 'success',
              title: 'Email is Unique',
              text: 'The email address is available.'
            });
          }
        } else {
          console.error('Invalid response format');
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while checking email uniqueness. Please try again later.'
          });
        }
      },
    });
  }
</script>

<script>
  $(document).ready(function() {
    $('#staff_username_id').on('keydown', function(e) {
      // Prevent the entry of spaces
      if (e.key === ' ') {
        e.preventDefault();
      }
    });
    $('#staff_username_id').on('input', function() {
      var usernameValue = $(this).val();
      $(this).val(usernameValue.toLowerCase());
    });
  });

  function checkusername() {
    var usernameValue = $('#staff_username_id').val().trim(); // Trim leading and trailing spaces
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    usernameValue = usernameValue.toLowerCase(); // Convert to lowercase

    // Update the input field with the trimmed and lowercase username
    $('#staff_username_id').val(usernameValue);

    if (usernameValue === "") {
      return;
    }

    $.ajax({
      type: 'POST',
      url: '/checkUniqueUsername',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      data: {
        username: usernameValue
      },
      success: function(response) {
        if (response) {
          if (!response.status) {
            Swal.fire({
              icon: 'warning',
              title: 'Username Already Exists',
              text: 'The username already exists.'
            });
            $('#staff_username_id').val("");
          } else {
            Swal.fire({
              icon: 'success',
              title: 'Username is Unique',
              text: 'The username is available.'
            });
          }
        } else {
          console.error('Invalid response format');
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while checking username uniqueness. Please try again later.'
          });
        }
      },
    });
  }
</script>

<script>
  function checkaccesscardnumber() {
    var accesscardnumberValue = $('#access_card_number_id').val();
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    console.log('Accesscardnumber Value:', accesscardnumberValue);
    $.ajax({
      type: 'POST',
      url: '/checkUniqueAccessCardNumber',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      data: {
        accesscardnumber: accesscardnumberValue
      },
      success: function(response) {
        if (response) {
          // Assuming response is a boolean indicating existence
          if (!response.status) {
            Swal.fire({
              icon: 'warning',
              title: 'Accesscard Number Already Exists',
              text: 'The Accesscard Number already exists.'
            });
            $('#staff_username_id').val("");
          } else {
            Swal.fire({
              icon: 'success',
              title: 'Accesscard Number is Unique',
              text: 'The Accesscard Number is available.'
            });
          }
        } else {
          console.error('Invalid response format');
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while checking accesscardnumber uniqueness. Please try again later.'
          });
        }
      },
    });
  }
</script>
<script>
  function updateCommissionPlaceholder() {
    var commissionType = document.getElementById('staff_commission_type').value;
    var placeholderText = '';
    var commissionInput = document.getElementById('staff_commission');

    if (commissionType === 'percentage') {
      placeholderText = 'Enter Percentage';
      // Additional validation for percentage not greater than 100
      validatePercentage(commissionInput.value);
    } else if (commissionType === 'fixed') {
      placeholderText = 'Enter Fixed Amount';
      // Clear previous error message
      document.getElementById('commission-error').innerHTML = '';
    } else {
      placeholderText = 'Staff Commission';
      // Clear previous error message
      document.getElementById('commission-error').innerHTML = '';
    }

    commissionInput.setAttribute('placeholder', placeholderText);
  }

  function validatePercentage(value) {
    var errorMessage = '';
    if (value && (isNaN(value) || parseFloat(value) > 100)) {
      Swal.fire({
        icon: 'error',
        title: 'Invalid Percentage',
        text: 'Percentage should be between 0 and 100.'
      });
      $('#staff_commission').val("");
    }

    document.getElementById('commission-error').innerHTML = errorMessage;
  }

  // Call the function on page load
  document.addEventListener('DOMContentLoaded', function() {
    updateCommissionPlaceholder();
  });

  function validateCommission(input) {
    var commissionType = document.getElementById('staff_commission_type').value;

    if (commissionType === 'percentage') {
      validatePercentage(input.value);
    } else {
      // Clear previous error message
      document.getElementById('commission-error').innerHTML = '';
    }
  }
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var checkboxes = document.querySelectorAll('input[name="leave_type[]"]');

    checkboxes.forEach(function(checkbox, index) {
      checkbox.addEventListener('change', function() {
      
        // Find the closest row to the checkbox
        var row = this.closest('tr');

        // Find credit period and credit limit fields in the same row
        var creditPeriodField = row.querySelector('select[name="credit_period[]"]');
        var creditLimitField = row.querySelector('input[name="credit_limit[]"]');
        var requiredMessage = row.querySelector('.required-message');

        creditPeriodField.addEventListener('change', function() {
          creditPeriodField.classList.remove('error-border');
        })

        creditLimitField.addEventListener('input', function() {
          creditLimitField.classList.remove('error-border');
          if (requiredMessage && creditPeriodField.value && creditLimitField.value) {
            row.removeChild(requiredMessage);
          }

        })

        if (creditPeriodField && creditLimitField) {
          if (this.checked) {
            // Checkbox is checked, remove red border
            creditPeriodField.classList.add('error-border');
            creditLimitField.classList.add('error-border');

            if (!requiredMessage) {
              requiredMessage = document.createElement('div');
              requiredMessage.classList.add('required-message');
              requiredMessage.textContent = 'Fields are required';
              // Set the background color to red
              requiredMessage.style.color = 'red';
              row.appendChild(requiredMessage);
            }

            // Make other fields mandatory
            creditPeriodField.removeAttribute('disabled');
            creditLimitField.removeAttribute('disabled');
          } else {
            // Checkbox is unchecked, add red border and show required message
            creditPeriodField.classList.remove('error-border');
            creditLimitField.classList.remove('error-border');

            // Remove required message
            if (requiredMessage) {
              requiredMessage.remove();
            }

            // Make other fields non-mandatory
            creditPeriodField.setAttribute('disabled', 'true');
            creditLimitField.setAttribute('disabled', 'true');
          }
        } else {
          console.error('Elements not found for checkbox:', this);
        }
      });
    });
  });

  function checkDiscountPercentage() {
    var discountInput = document.getElementById('discount_percentage');
    var enteredValue = parseFloat(discountInput.value);

    if (isNaN(enteredValue) || enteredValue < 0 || enteredValue > 100) {
      swal({
        title: 'Invalid Percentage',
        text: 'Please enter a valid discount percentage between 0 and 100.',
        icon: 'error',
        button: 'OK',
      });

      discountInput.value = '';
    }
  }

  $(document).ready(function() {
    $('#staff_type').change(function() {
      var selectedStaffType = $(this).val();
      if (selectedStaffType == 96) {
        $('#pharmacySelectBox').show();
      } else {
        $('#pharmacySelectBox').hide();
      }
    });


    $('#staff_type').change(function() {
      var selectedStaffType = $(this).val();
      if (selectedStaffType == 20) {
        $('#consultation_fees_field').show();
      } else {
        $('#consultation_fees_field').hide();
      }
    });
  });
  document.addEventListener("DOMContentLoaded", function() {
    var staffTypeSelect = document.getElementById("staff_type");
    var consultationFeesField = document.getElementById("consultation_fees_field");
    var consultationFeesInput = document.getElementById("consultation_fees");
    var requiredMessage = document.querySelector(".required-message");

    staffTypeSelect.addEventListener("change", function() {
      var selectedOption = staffTypeSelect.options[staffTypeSelect.selectedIndex].value;

      if (selectedOption === "20") {
        consultationFeesField.style.display = "block";
        consultationFeesInput.setAttribute("required", true);
      } else {
        consultationFeesField.style.display = "none";
        consultationFeesInput.removeAttribute("required");
        requiredMessage.style.display = "none";
      }

      if (consultationFeesInput.validity.valueMissing) {
        requiredMessage.style.display = "block";
      } else {
        requiredMessage.style.display = "none";
      }
    });

    consultationFeesInput.addEventListener("input", function() {
      if (consultationFeesInput.validity.valueMissing) {
        requiredMessage.style.display = "block";
      } else {
        requiredMessage.style.display = "none";
      }
    });

  });

  $(document).ready(function() {

    var today = new Date().toISOString().split('T')[0];

    // Set the max attribute of the date input to today
    document.getElementById("date_of_birth").setAttribute("max", today);

  });

  function toggleStatus(checkbox) {
    var statusText = document.getElementById('statusText');
    if (checkbox.checked) {
      statusText.textContent = 'Active';
    } else {
      statusText.textContent = 'Inactive';
    }
  }
  $(document).ready(function(){
    document.getElementById('numericInput').addEventListener('input', function(event) {
        let inputValue = event.target.value;
        inputValue = inputValue.replace(/[^0-9.]/g, '');
        inputValue = inputValue.replace(/(\..*)\./g, '$1');
        event.target.value = inputValue;
    });
})

$(document).ready(function(){
    document.getElementById('numericInput1').addEventListener('input', function(event) {
        let inputValue = event.target.value;
        inputValue = inputValue.replace(/[^0-9.]/g, '');
        inputValue = inputValue.replace(/(\..*)\./g, '$1');
        event.target.value = inputValue;
    });
})
</script>