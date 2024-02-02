@extends('layouts.app')
@section('content')
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
  <form action="{{ route('staffs.update', ['staff_id' => $staffs->staff_id]) }}" method="POST" enctype="multipart/form-data"> 
    @csrf 
    @method('PUT')
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
    @foreach($stafftype as $masterId => $masterValue)
        <option value="{{ $masterId }}" {{ optional($staffs)->staff_type == $masterId ? 'selected' : '' }}>
            {{ $masterValue }}
        </option>
    @endforeach
</select>

                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Employment Type*</label>
                  <select class="form-control" name="employment_type" id="employment_type">
                      <option value="">Select Employment Type</option> @foreach($employmentType as $masterId => $masterValue) <option value="{{ $masterId }}" {{ $staffs->employment_type == $masterId ? 'selected' : '' }}>
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
                  <input type="text" class="form-control" name="staff_name" maxlength="100" value="{{ $staffs->staff_name }}" placeholder="Staff Name">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="patient_gender" class="form-label">Gender*</label>
                  <select class="form-control" name="gender" id="gender">
                      <option value="">Choose Gender</option> @foreach($gender as $id => $genderValue) <option value="{{ $id }}" {{ $staffs->gender == $id ? 'selected' : '' }}>
                        {{ $genderValue }}
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
                      <option value="">Choose Branch</option> @foreach($branchs as $branch) <option value="{{ $branch->branch_id }}" {{ $staffs->branch_id == $branch->branch_id ? 'selected' : '' }}>
                        {{ $branch->branch_name }}
                      </option> @endforeach
                    </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Date Of Birth*</label>
                  <input type="date" class="form-control" name="date_of_birth" value="{{ $staffs->date_of_birth }}" placeholder="Date Of Birth">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Email*</label>
                  <input type="email" class="form-control" name="staff_email" value="{{ $staffs->staff_email }}" placeholder="Staff Email">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Contact Number*</label>
                  <input type="text" class="form-control" name="staff_contact_number" value="{{ $staffs->staff_contact_number }}" placeholder="Contact Number" pattern="[0-9]+" title="Please enter digits only" oninput="validateContact(this)">
                  <p class="error-message" style="color: green; display: none;">Please enter digits only.</p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Address*</label>
                  <textarea class="form-control" name="staff_address" placeholder="Address">{{ $staffs->staff_address }}</textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Qualification*</label>
                  <input type="text" class="form-control" name="staff_qualification" maxlength="100" value="{{ $staffs->staff_qualification }}" placeholder="Qualification" oninput="this.value = this.value.replace(/[0-9]/g, '');" >
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Specialization*</label>
                  <input type="text" class="form-control" name="staff_specialization" maxlength="255" value="{{ $staffs->staff_specialization }}" placeholder="Specialization" oninput="this.value = this.value.replace(/[0-9]/g, '');" >
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Work Experience*</label>
                  <input type="text" class="form-control" name="staff_work_experience" maxlength="5" value="{{ $staffs->staff_work_experience }}" placeholder="Work Experience" pattern="[0-9]+" title="Please enter digits only" >
                  <p class="error-message" style="color: green; display: none;">Please enter digits only.</p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Commission Type*</label>
                  <select class="form-control" name="staff_commission_type" id="staff_commission_type">
                      <option value="">Select Commission Type</option>
                      <option value="percentage" {{ $staffs->staff_commission_type == 'percentage' ? 'selected' : '' }}>Percentage</option>
                      <option value="fixed" {{ $staffs->staff_commission_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                    </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Staff Commission*</label>
                  <input type="text" class="form-control" name="staff_commission" id="staff_commission" value="{{ $staffs->staff_commission }}"  placeholder="Staff Commission" oninput="validateCommission(this);" >
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <div class="form-label"></div>
                  <label class="form-label">Access Card Number*</label>
                  <input type="text" class="form-control" name="access_card_number" value="{{ $staffs->access_card_number }}" id="access_card_number" placeholder="Access Card Number" >
                  </label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <div class="form-label"></div>
                  <label class="form-label">Date Of Join*</label>
                  <input type="date" class="form-control" name="date_of_join" value="{{ $staffs->date_of_join }}" placeholder="Date Of Join" >
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
                  <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" checked >
                  <span id="statusLabel" class="custom-switch-indicator"></span>
                  <span id="statusText" class="custom-switch-description">Active</span>
                </label>
              </div>
            </div>
            <!-- Add other fields for personal info section -->
            <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">Has Login*</label>
                <label class="custom-control custom-checkbox" style="padding-left: 0;">
                    <input type="checkbox" class="custom-control-input" name="is_login" id="is_login" onchange="toggleLoginFields()" {{ $staffs->is_login == 1 ? 'checked' : '' }}>
                    <span class="custom-control-label" style="padding-left: 22px;">Has Login</span>
                </label>
            </div>
        </div>
          </div>
          <button class="btn btn-primary nextBtn btn-lg pull-right" type="button">Next</button>
          <div class="col-md-6" id="usernameField" style="{{ $staffs->is_login == 1 ? '' : 'display: none;' }}">
          <div class="form-group">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" name="staff_username" id="staff_username" value="{{ old('staff_username', $staffs->staff_username) }}">
          </div>
          <div class="form-group">
          <label class="form-label">Staff Discount Percentage</label>
                <input type="text" class="form-control" name="discount_percentage" onkeyup="checkDiscountPercentage()" id="discount_percentage" value="{{  $staffs->discount_percentage }}" placeholder="Staff Discount Percentage">
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
      <div class="container">
        @foreach($salaryData as $index => $salary)
          <div class="row salary-row" id="salaryContainer{{ $index }}">
            <div class="col-md-3">
              <div class="form-group">
                <label class="form-label">Salary Head*</label>
                <select class="form-control salary_head" name="salary_head_id[]" id="salary_head_id{{ $index }}" required>
                <option value="" disabled selected>Choose Salary Head</option>
                @foreach($heads as $head)
                  <option value="{{ $head->id }}" {{ $head->id == $salary->salary_head ? 'selected' : '' }}>
                    {{ $head->salary_head_name }}
                  </option>
                @endforeach
              </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="form-label">Salary Head Type*</label>
                <input type="text" class="form-control salary_head_types" name="salary_head_type_id[]" id="salary_head_type{{ $index }}" value="{{ $salary->salary_head_type }}" required>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="form-label">Salary Amount*</label>
                <input type="number" class="form-control" name="amount[]" value="{{ $salary->amount }}" placeholder="Salary Amount" required>
              </div>
            </div>
            <div class="col-md-3" data-initial-row id="salaryContainer{{ $index }}">
              <div class="form-group">
                <label class="form-label">Actions</label>
                <button type="button" class="btn btn-danger remove-row" {{ $index == 0 ? 'disabled' : '' }}>Remove</button>
              </div>
            </div>
          </div>
        @endforeach
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
    @for ($i = 0; $i < count($leave_types); $i++)
        <tr>
            <td>
                <div class="custom-checkbox">
                    <input type="checkbox" class="custom-control-input custom-control-input-lg" name="leave_type[]" id="customCheck{{ $i + 1 }}" value="{{ $leave_types[$i]->leave_type_id }}" 
                    @if($selectedLeaveTypes->where('leave_type', $leave_types[$i]->leave_type_id)->isNotEmpty()) checked @endif
                    >
                    <input type="hidden" name="leave_type_id[]" value="{{ $leave_types[$i]->leave_type_id }}">
                    <label class="custom-control-label" for="customCheck{{ $i + 1 }}"></label>
                </div>
            </td>
            <td>{{ $leave_types[$i]->name }}</td>
            <td>
                <select class="form-control" name="credit_period[]" id="credit_period_{{ $leave_types[$i]->leave_type_id }}">
                    <option value="" disabled selected>Select Credit Period</option>
                    <option value="Monthly" @if($selectedLeaveTypes->where('leave_type', $leave_types[$i]->leave_type_id)->isNotEmpty() && $selectedLeaveTypes->where('leave_type', $leave_types[$i]->leave_type_id)->first()->credit_period == 'Monthly') selected @endif>Monthly</option>
                    <option value="Yearly" @if($selectedLeaveTypes->where('leave_type', $leave_types[$i]->leave_type_id)->isNotEmpty() && $selectedLeaveTypes->where('leave_type', $leave_types[$i]->leave_type_id)->first()->credit_period == 'Yearly') selected @endif>Yearly</option>
                </select>
            </td>
            <td>
                <div class="form-group">
                    <input type="number" id="credit_limit" name="credit_limit[]" placeholder="Enter Credit Limit" 
                    @if($selectedLeaveTypes->where('leave_type', $leave_types[$i]->leave_type_id)->isNotEmpty()) value="{{ $selectedLeaveTypes->where('leave_type', $leave_types[$i]->leave_type_id)->first()->credit_limit }}" @endif
                    >
                </div>
            </td>
        </tr>
    @endfor
</tbody>

      </table>
    </div>
    <div class="text-center">
      <button class="btn btn-success btn-lg"  type="submit" id="finalSubmit">Update</button>
    </div>
    <br><br>
  </div>
</div>
</form>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">0
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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

  
// });
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

    // Submit the form
    $('#addFm').submit();
});
  });

  $(document).ready(function () {
    $("#addProductBtn").click(function (event) {
        event.preventDefault();
        try {
            var newIndex = $('.row[id^="salaryContainer"]').length + 1;
            var newRow = $('<div class="row" id="salaryContainer' + newIndex + '">' +
                '<div class="col-md-3">' +
                '<div class="form-group">' +
                '<label class="form-label">Salary Head*</label>' +
                '<select class="form-control salary_head" name="salary_head_id[]">' +
                '<option value="" disabled selected>Choose Salary Head</option>' +
                '@foreach($heads as $head)' +
                '<option value="{{ $head->id }}">{{ $head->salary_head_name }}</option>' +
                '@endforeach' +
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-3">' +
                '<div class="form-group">' +
                '<label class="form-label">Salary Head Type*</label>' +
                '<input type="text" class="form-control salary_head_types" name="salary_head_type_id[]" id="salary_head_type">' +
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
                '<button type="button" class="btn btn-danger remove-row" ' + (newIndex == 1 ? 'disabled' : '') + '>Remove</button>' +
                '</div>' +
                '</div>' +
                '</div>');

            // Append the new row to the container
            $("#step-2 .container").append(newRow);

        } catch (error) {
            console.error("Error adding row:", error);
        }
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


    // Ensure the usernameField is hidden initially if is_login is not 1
    document.addEventListener("DOMContentLoaded", function() {
        toggleLoginFields();
    });

    function toggleLoginFields() {
        var isLoginCheckbox = document.getElementById('is_login');
        var usernameField = document.getElementById('usernameField');

        if (isLoginCheckbox.checked || isLoginCheckbox.defaultChecked) {
            usernameField.style.display = 'block';
        } else {
            usernameField.style.display = 'none';
        }
    }

        $(document).ready(function () {
            // Listen for checkbox changes
            $('.custom-checkbox input[type="checkbox"]').change(function () {
                var row = $(this).closest('tr');
                var index = row.index();

                if (!this.checked) {
                    // If unchecked, clear the corresponding fields
                    resetFields(row);
                } else {
                    // If checked back, restore the database values
                    restoreFields(row, index);
                }
            });

            function resetFields(row) {
                // Update the fields with empty values
                row.find('select[name="credit_period[]"]').val('');
                row.find('input[name="credit_limit[]"]').val('');
            }


        });

    </script>
    

