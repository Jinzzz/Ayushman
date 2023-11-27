@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Holiday-Department Linking</h3>
            </div>
            <form action="{{ route('holidays.storelink', ['holidaymapping_id' => $holiday->id]) }}" method="POST">
                    @csrf
            <input type="hidden" id="holiday_name" name="holiday_id" class="form-control" value="{{ $holiday->id }}">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="holiday_name" class="form-label" style="color: red;">Holiday Name*</label>
                        <input type="text" id="holiday_name" name="holiday_id" class="form-control" value="{{ $holiday->holiday_name }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                    <label for="from_date" class="form-label" style="color: red;">From Date*</label>
                        <input type="date" id="from_date" name="from_date" class="form-control" value="{{ $holiday->from_date }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="to_date" class="form-label red-label" style="color: red;">To Date*</label>
                        <input type="date" id="to_date" name="to_date" class="form-control" value="{{ $holiday->to_date }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="year" class="form-label red-label" style="color: red;">Year*</label>
                        <input type="number" id="year" name="year" class="form-control" value="{{ $holiday->year }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="department" class="form-label red-label" style="color: red;" placeholder="Select Staff Type">Department*</label>
                    <select class="multi-select" name="department[]" multiple style="width: 100%;">
                    <option value="" disabled selected>Select a Department</option>
                    @foreach($staff_types as $staff_type)
                    <option value="{{ $staff_type->id }}">{{ $staff_type->master_value }}</option>
                    @endforeach
            </select>
                </div>
            </div>

                <div class="col-md-3 d-flex align-items-end">
                        <div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-add" aria-hidden="true"></i> Save</button>&nbsp;
                            <a class="btn btn-primary" href="{{ route('holidays.index') }}"><i class="fa fa-times" aria-hidden="true"></i> Reset</a>
                        </div>
                    </div>
            </div>

        </div>
    </div>
    </form>
</div>

    <div class="card">
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{$message}}</p>
        </div>
        @endif
        @if ($message = Session::get('error'))
        <div class="alert alert-danger">
            <p>{{$message}}</p>
        </div>
        @endif
        <div class="card-header">
            <h3 class="card-title">List Holidays</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th class="wd-15p">SL.NO</th>
                            <th class="wd-10p">Holiday Name</th>
                            <th class="wd-10p">Departments</th>
                            <th class="wd-15p">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach ($departments as $department)
                        <tr id="dataRow_{{ $department->id }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ $department->holiday_name }}</td>
                            <td>{{ $department->department_name }}</td>
                            <td>
                            <form style="display: inline-block" action="{{ route('holidaysmapping.destroy', $department->id) }}" method="post">
                            @csrf
                            @method('DELETE')
                                    <button type="button" onclick="deleteData({{ $department->id }})" class="btn-danger btn-sm">
                                        <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                    </button>
                                </form>
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
<script>
function deleteData(dataId) {
    swal({
        title: "Delete selected data?",
        text: "Are you sure you want to delete this data",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {
            $.ajax({
                url: "{{ route('holidaysmapping.destroy', '') }}/" + dataId,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.success == true) {
                        $("#dataRow_" + dataId).remove();
                        // Display a success flash message
                        flashMessage('success', 'Department deleted successfully');
                    } else {
                        // Display an error flash message
                        flashMessage('error', 'An error occurred! Please try again later.');
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    // Display an error flash message
                    flashMessage('error', 'An error occurred while deleting the Department.');
                    console.error("Error: ", textStatus, errorThrown);
                },
            });
        }
    });
}

function flashMessage(type, message) {
    // Use Laravel's session to store flash messages
    @if (session('flash_message'))
        @foreach(session('flash_message') as $type => $message)
            toastr.{{ $type }}("{{ $message }}");
        @endforeach
    @endif

    // Add the new flash message
    toastr[type](message);
}

</script>

