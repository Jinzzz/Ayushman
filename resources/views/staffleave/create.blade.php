@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Create Leave Request</h3>
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
                    <form action="{{ route('staffleave.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label branch" id="branchLabel">Staff Branch*</label>
                                    <select class="form-control" name="branch_id" id="branch_id">
                                    <option value="">Choose Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->branch_id }}" {{ old('branch_id') == $branch->branch_id ? 'selected' : '' }}>
                                            {{ $branch->branch_name }}
                                        </option>
                                    @endforeach
                                </select>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Staff Name</label>
                                    <select class="form-control" name="staff_id" id="staff_id">
                                        <option value="">Select a Branch...</option>
                                        <!-- Options will be dynamically populated using AJAX -->
                                    </select>
                                    <p class="no-staff-message" style="color: red; display: none;">No staff members in the selected branch.</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"> From Date</label>
                                    <input type="date" class="form-control" name="from_date" id="from_date" value="{{ old('from_date') }}" placeholder="Emergency Contact" >
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Start Day</label>
                                    <select class="form-control" name="start_day" id="start_day">
                                    <option value="Full Day" {{ old('start_day') == 'Full Day' ? 'selected' : '' }}>Full Day</option>
                                    <option value="Half Day" {{ old('start_day') == 'Half Day' ? 'selected' : '' }}>Half Day</option>
                                    <!-- Options will be dynamically populated using AJAX -->
                                </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">To Date</label>
                                    <input type="date" class="form-control" name="to_date" id="to_date"  value="{{ old('to_date') }}">
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">End Day</label>
                                    <select class="form-control" name="end_day" id="end_day">
                                    <option value="Full Day" {{ old('end_day') == 'Full Day' ? 'selected' : '' }}>Full Day</option>
                                    <option value="Half Day" {{ old('end_day') == 'Half Day' ? 'selected' : '' }}>Half Day</option>
                                    <!-- Options will be dynamically populated using AJAX -->
                                </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">No Of Days</label>
                                    <input type="text" class="form-control" value="{{ old('days') }}" name="days" id="no_of_days" placeholder="No Of Days" readonly>
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div>
                
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Leave Type</label>
                                    <select class="form-control" name="leave_type" id="leave_type">
                                    <option value="" disabled selected>Choose Leave Type</option>
                                    @foreach($leave_types as $leave_type)
                                        <option value="{{ $leave_type->leave_type_id }}" {{ old('leave_type') == $leave_type->leave_type_id ? 'selected' : '' }}>
                                            {{ $leave_type->name }}
                                        </option>
                                    @endforeach
                                </select>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Reason</label>
                                    <textarea class="form-control" name="reason" placeholder="Reason For Leave">{{ old('reason') }}</textarea>

                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <center>
                                <button type="submit" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Add
                                </button>
                                <button type="reset" class="btn btn-raised btn-success">
                                    Reset
                                </button>
                                <a class="btn btn-danger" href="{{ route('staffleave.index') }}">Cancel</a>
                            </center>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script type="text/javascript">
  $(document).ready(function () {
        // Handle change event on branch select
        $('#branch_id').change(function () {
            var branchId = $(this).val();

            // Clear the current options in the staff select
            $('#staff_id').empty().append('<option value="">Select a Branch...</option>');
            $('.no-staff-message').hide();

            // Make an AJAX request to get the staff names for the selected branch
            $.ajax({
                type: 'GET',
                url: '{{ route("get-staff-names", ["branchId" => ":branchId"]) }}'.replace(':branchId', branchId),
                success: function (data) {
                    // Populate the staff select with received data
                    $('#staff_id').empty();
                    if (Object.keys(data).length > 0) {
                        $.each(data, function (key, value) {
                            // Create an option element
                            var option = $('<option>', {
                                value: key,
                                text: value
                            });

                            // Append the option to the staff select
                            $('#staff_id').append(option);
                        });
                    } else {
                        $('.no-staff-message').show();
                    }
                },
                error: function (error) {
                    console.error(error);
                }
            });
        });
    });

    $(document).ready(function () {
        // Handle change event on From Date and To Date inputs
        $('#from_date, #to_date').change(function () {
            var fromDate = new Date($('#from_date').val());
            var toDate = new Date($('#to_date').val());

            // Check if both dates are valid
            if (!isNaN(fromDate.getTime()) && !isNaN(toDate.getTime())) {
                // Calculate the difference in days and include the last day
                var differenceInTime = toDate.getTime() - fromDate.getTime();
                var differenceInDays = Math.floor(differenceInTime / (1000 * 3600 * 24)) + 1;

                // Update the No Of Days input
                $('#no_of_days').val(differenceInDays);
            }
        });
    });
</script>



