@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Consultation Billing</h3>
            </div>
            <form id="searchForm" action="{{ route('patientname.search', ['id' => $firstPatientId]) }}" method="POST">
            @csrf

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="employee_name" class="form-label">Patient Name</label>
                        <select class="form-control" name="patient_id" id="patient_id">
                            <option value="" disabled selected>Choose Patient</option>
                            @foreach($patientNames as $patientId => $patientName)
                                <option value="{{ $patientId }}">
                                    {{ $patientName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- <div class="col-md-4">
                        <label for="birthday" class="form-label">Booking date</label>
                        <input class="form-control" type="date" id="booking_date" name="booking_date">
                    </div> -->
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                    </div>
                </div>
            </div>

        </form>
        </div>
    </div>
</div>

<div class="card">
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>
    @endif
    <div class="card-header">
        <h3 class="card-title">List Consultation Billing</h3>
    </div>
    <div class="card-body">
        <!-- <a href="{{ route('availableleaves.create') }}" class="btn btn-block btn-info">
            <i class="fa fa-plus"></i> Add Employee Leave
        </a> -->
        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th class="wd-25p">SL.NO</th>
                        <th class="wd-25p">Booking ID</th>
                        <th class="wd-25p">Booking Reference Number</th>
                        <th class="wd-25p">Patient Name</th>
                        <th class="wd-25p">Booking Date</th>
                        <th class="wd-25p">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 0;
                     @endphp
                     @foreach($datas as $data)
                    <tr id="dataRow_{{$data->consultation_id }}">
                        <td>{{ ++$i }}</td>
                        <td>{{ $data->consultation_id }}</td>
                        <td>{{ $data->booking_reference_number }}</td>
                        <td>{{ $data->patient_name }}</td>
                        <td>{{ $data->booking_date }}</td>
                        <td><center>
                            <a class="btn btn-secondary btn-sm" href="{{ route('consultation_billing.create', $data->consultation_id) }}">
                                <i class="fa fa-eye" aria-hidden="true"></i> Generate Invoice
                            </a>
                        </center>
                    </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
        <!-- TABLE WRAPPER -->
    </div>
    <!-- SECTION WRAPPER -->
</div>
</div>
</div>
<!-- ROW-1 CLOSED -->
@endsection
<!-- Add these in the head section of your HTML document -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<!-- Add this script at the end of your index.blade.php file -->
<script>
    $(document).ready(function () {
        // Intercept the form submission
        $('#searchForm').submit(function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Get the form data
            var formData = $(this).serialize();

            // Make an AJAX request
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                success: function (response) {
                    // Update the table content with the response
                    $('#example').replaceWith(response);
                },
                error: function (error) {
                    // Handle the error
                    console.log(error);
                }
            });
        });
    });
    setTimeout(function() {
                $('#success-alert').fadeOut('slow');
            }, 3000);
</script>
