@extends('layouts.app')

@section('content')

        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">Edit Leave Request</h3>
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

                        <form action="{{ route('employee.update', ['id' => $leave_request->id]) }}" method="POST"
                            enctype="multipart/form-data">
                            <input type="hidden" class="form-control" readonly name="staff_id" 
                                            value="{{ $leave_request->staff_id }}">
                            <input type="hidden" class="form-control" name="db_total_days" 
                                            value="{{ $leave_request->days }}">         
                            @csrf
                            @method('PUT')
                            <div class="row">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Branch Name</label>
                                        <input type="text" class="form-control" readonly name="branch_name" maxlength="100"
                                            value="{{ $leave_request->branch_name }}" placeholder="Branch Name">
                                    </div>
                                </div>
                                </div> 
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Staff Name</label>
                                        <input type="text" class="form-control" readonly name="staff_name" maxlength="100"
                                            value="{{ $leave_request->staff_name }}" placeholder="Staff Name">
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Leave Type</label>
                                    <select class="form-control" name="leave_type" id="leave_type">
                                    <option value="" disabled selected>Choose Leave Type</option>
                                    @foreach($leave_types as $lt)
                                        <option value="{{ $lt->leave_type_id }}" {{ $lt->leave_type_id == $leave_request->leave_type ? 'selected' : '' }}>
                                            {{ $lt->name }}
                                        </option>
                                    @endforeach
                                </select>

                                </div>
                                </div>
                                </div>

                                <div class="row"  id="totalDaysContainer">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Total Days</label>
                                        <input type="text" class="form-control" value="{{$total_leaves }}" name="total_days" id="total_days" placeholder="No Of Days" readonly>
                                        <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"> From Date</label>
                                    <input type="date" class="form-control" name="from_date" id="from_date" value="{{ $leave_request->from_date }}" placeholder="Emergency Contact">
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Start Day</label>
                                    <select class="form-control" name="start_day" id="start_day">
                                <option value="Full Day" {{ $leave_request->start_day == 'Full Day' ? 'selected' : '' }}>
                                    Full Day
                                </option>
                                <option value="First Half" {{ $leave_request->start_day == 'First Half' ? 'selected' : '' }}>
                                First Half 
                                </option>
                                <option value="Second Half" {{ $leave_request->start_day == 'Second Half' ? 'selected' : '' }}>
                                Second Half 
                                </option>
                                <!-- Options will be dynamically populated using AJAX -->
                            </select>
                                </div>
                            </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">To Date</label>
                                    <input type="date" class="form-control" name="to_date" id="to_date" value="{{ $leave_request->to_date }}">
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">End Day</label>
                                    <select class="form-control" name="end_day" id="end_day">
                                <option value="Full Day" {{ $leave_request->end_day == 'Full Day' ? 'selected' : '' }}>
                                    Full Day
                                </option>
                                <option value="First Half" {{ $leave_request->end_day == 'First Half' ? 'selected' : '' }}>
                                First Half
                                </option>
                                <option value="Second Half" {{ $leave_request->end_day == 'Second Half' ? 'selected' : '' }}>
                                Second Half
                                </option>
                                <!-- Options will be dynamically populated using AJAX -->
                            </select>
                                </div>
                            </div>
                        </div>

                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">No Of Days</label>
                                    <input type="text" class="form-control" value="{{ $leave_request->days }}" name="days" id="no_of_days" placeholder="No Of Days" readonly>
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div>


                            </div>
                            <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Reason</label>
                                   <textarea class="form-control" name="reason" placeholder="Reason For Leave">{{ old('reason', $leave_request->reason) }}</textarea>


                                </div>
                            </div>
                        </div>

                            <!-- ... Add other leave-related fields here -->

                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-raised btn-primary">
                                            <i class="fa fa-check-square-o"></i> Update
                                        </button>
                                        <button type="reset" class="btn btn-raised btn-success">
                                            Reset
                                        </button>
                                        <a class="btn btn-danger" href="{{ route('employee.index') }}">Cancel</a>
                                    </center>
                                </div>
                            </div>
                        </form>
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
       // Handle change event on From Date and To Date inputs
       $('#from_date, #to_date, #start_day, #end_day').change(function () {
        var fromDate = new Date($('#from_date').val());
        var toDate = new Date($('#to_date').val());
        var startDay = $('#start_day').val();
        var endDay = $('#end_day').val();

        // Check if both dates are valid
        if (!isNaN(fromDate.getTime()) && !isNaN(toDate.getTime())) {
            // Check if the selected from_date and to_date are the same
            var sameDate = fromDate.toISOString().split('T')[0] === toDate.toISOString().split('T')[0];

            // Show or hide the End Day dropdown based on the sameDate condition
            $('#end_day').closest('.form-group').toggle(!sameDate);

            // Calculate the difference in days based on start and end days
            var differenceInDays = calculateDifferenceInDays(fromDate, toDate, startDay, endDay);

            // Update the No Of Days input
            $('#no_of_days').val(differenceInDays);
        }
    });

        // Handle change event on staff select
        $('#staff_id').change(function () {
            var staffId = $(this).val();

            // Make an AJAX request to get the total leaves for the selected staff
            $.ajax({
                type: 'GET',
                url: '{{ route("get-total-leaves", ["staffId" => ":staffId"]) }}'.replace(':staffId', staffId),
                success: function (data) {
                    // Update the total days input with received data
                    $('#total_days').val(data.total_leaves);
                },
                error: function (error) {
                    console.error(error);
                }
            });
        });

        // Function to calculate the difference in days
        function calculateDifferenceInDays(startDate, endDate, startDay, endDay) {
            // Calculate the difference in days and include the last day
            var differenceInTime = endDate.getTime() - startDate.getTime();
            var differenceInDays = Math.floor(differenceInTime / (1000 * 3600 * 24)) + 1;

            // Adjust the difference based on start and end days
            if(startDay === 'First Half' && endDay === 'First Half' || endDay === 'Second Half'){
                differenceInDays -= 0.5;
            }if(startDay === 'Second Half' && endDay === 'First Half' || endDay === 'Second Half'){
                differenceInDays -= 0.5;
            }
            else if(startDay === 'First Half' || startDay === 'Second Half') {
                differenceInDays -= 0.5; // If both halves are selected, subtract half a day
            } else if (endDay === 'First Half' || endDay === 'Second Half') {
                differenceInDays -= 0.5; // If only one half is selected, subtract half a day
            }

            return differenceInDays;
        }
    });

</script>



