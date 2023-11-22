@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">Edit Holiday</h3>
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

                        <form action="{{ route('holidays.update', ['id' => $holiday->id]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Holiday Name</label>
                                        <input type="text" class="form-control" name="holiday_name" maxlength="100"
                                            value="{{ $holiday->holiday_name }}" placeholder="Holiday Name">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">From Date</label>
                                        <input type="date" class="form-control" name="from_date" id="from_date" value="{{ $holiday->from_date }}">
                                        <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">To Date</label>
                                        <input type="date" class="form-control" name="to_date" id="to_date" value="{{ $holiday->to_date }}">
                                        <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Leave Type</label>
                                        <select class="form-control" name="leave_type" id="leave_type">
                                            <option value="" disabled selected>Choose Leave Type</option>
                                            @foreach($leave_types as $lt)
                                                <option value="{{ $lt->leave_type_id }}" {{ $lt->leave_type_id == $holiday->leave_type ? 'selected' : '' }}>
                                                    {{ $lt->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Year</label>
                                    <input type="number" class="form-control" name="year" id="year" value="{{ $holiday->year }}" placeholder="Holiday Year" >
                                    <p class="error-message" style="color: red; display: none;">Only numbers are allowed.</p>
                                </div>
                            </div> 
                            </div>
                            </br>

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
                                        <a class="btn btn-danger" href="{{ route('holidays.index') }}">Cancel</a>
                                    </center>
                                </div>
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



