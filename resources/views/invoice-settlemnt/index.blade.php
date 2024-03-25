@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Consultation Billing</h3>
            </div>

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
            <form action="{{ route('invoice-settlemnt.index') }}" method="GET">
         <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="pat-code">Patient Name:</label>
                
                <select class="form-control mt-2" name="patient_name" id="patient_name">
                    <option value="" selected disabled>Select Patient</option>
                    @foreach($patientLists as $patientList)
                        <option value="{{ $patientList->patient_name }}">
                            {{ $patientList->patient_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="pat-code">Booking Date:</label>
                <input type="date" id="booking_date" name="booking_date" class="form-control" value="{{ old('booking_date')}}">
            </div>
            <div class="col-md-4">
                <label for="pat-code">Booking Type:</label>
                <select class="form-control" name="booking_type_id" id="booking_type_id">
                                    <option value=""selected disabled>Select Booking Type</option>
                                    @foreach($Bookingtypes as  $Bookingtype)
                                    <option value="{{ $Bookingtype->id }}">
                                        {{ $Bookingtype->master_value }}
                                    </option>
                                    @endforeach
                                </select>
            </div>
        </div>
        <div class="col-md-4 d-flex align-items-center">
            <div>
                <button type="submit" class="btn btn-secondary"><i class="fa fa-filter" aria-hidden="true"></i> Search </button>
                <a class="btn btn-secondary ml-2" href="{{ route('invoice-settlemnt.index') }}"><i class="fa fa-times" aria-hidden="true"></i>Reset</a>
            </div>
        </div>
    </div>
</form>

        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">List Bookings</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th class="wd-15p">SL.NO</th>
                                <th class="wd-15p">Booking Reference Number</th>
                                <th class="wd-20p">Patient Name</th>
                                <th class="wd-15p">Doctor Name</th>
                                <th class="wd-15p">Booking Date</th>
                                <th class="wd-15p">Time Slot</th>
                                <th class="wd-15p">Booking Type</th>
                                <th class="wd-15p">Branch</th>
                                <th class="wd-15p">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i = 0;
                            @endphp
                            @foreach($patients as $patient)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $patient->booking_reference_number}}

                                </td>
                                <td>{{ $patient->patient_name}}</td>
                                <td>{{ $patient->staff_name}}</td>
                                <td>{{ $patient->booking_date}}</td>
                                <td>{{ date('h:i A', strtotime($patient->time_from)) }} - {{ date('h:i A', strtotime($patient->time_to)) }}  </td>
                                <td>{{ $patient->booking_type_value}}</td>
                                <td>{{ $patient->branch_name}}</td>
                                <td>
                                
                                <a class="btn btn-secondary" href="{{ route('invoice-settlemnt.create', $patient->id) }}">
                                    <i class="fa fa-eye" aria-hidden="true"></i> Generate Invoice
                                </a>
            
                                                
                                            </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    // Fetch the input field and patient list
    const patientSelect = document.getElementById('patient_name');
    const options = Array.from(patientSelect.options);

    // Add event listener to the input field for search functionality
    patientSelect.addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();

        // Loop through patient options and show/hide based on search value
        options.forEach(option => {
            const patientName = option.text.toLowerCase();
            if (patientName.includes(searchValue) || searchValue === '') {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
    });
</script>


@endsection