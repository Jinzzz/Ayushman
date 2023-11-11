@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            @if ($messages = Session::get('error'))
            <div class="alert alert-danger">
                <ul>
                    @foreach (json_decode($messages, true) as $field => $errorMessages)
                    @foreach ($errorMessages as $errorMessage)
                    <li>{{$errorMessage}}</li>
                    @endforeach
                    @endforeach
                </ul>
            </div>
            @endif
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{$message}}</p>
            </div>
            @endif
            @if ($message = Session::get('failed'))
            <div class="alert alert-danger">
                <p>{{$message}}</p>
            </div>
            @endif
            <div class="card-header">
                <h3 class="card-title"><strong>Add {{$pageTitle}}</strong></h3>
            </div>
            <form action="{{ route('timeslot.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="hidden_id" name="hidden_id" value="{{ isset($edit_timeslot->id) ? $edit_timeslot->id : '' }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="slot_name"><b>Slot Name*</b></label>
                                <input type="text" id="slot_name" value="{{ isset($edit_timeslot->slot_name) ? $edit_timeslot->slot_name : '' }}" required name="slot_name" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-label">Status</div>
                                <label class="custom-switch">
                                    <input type="hidden" name="is_active" value="0"> <!-- Hidden field for false value -->
                                    <input type="checkbox" id="is_active" name="is_active" value="1" onchange="toggleStatus(this)" class="custom-switch-input" {{ isset($edit_timeslot->is_active) && $edit_timeslot->is_active == 1 ? 'checked' : '' }}>
                                    <span id="statusLabel" class="custom-switch-indicator"></span>
                                    <span id="statusText" class="custom-switch-description">
                                        {{ isset($edit_timeslot->is_active) && $edit_timeslot->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="time_from"><b>Time From*</b></label>
                                <input type="time" id="time_from" value="{{ isset($edit_timeslot->time_from) ? $edit_timeslot->time_from : '' }}" required name="time_from" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="time_to"><b>Time To*</b></label>
                                <input type="time" id="time_to" value="{{ isset($edit_timeslot->time_to) ? $edit_timeslot->time_to : '' }}" required name="time_to" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <div>
                            <button type="submit" class="btn btn-raised btn-primary">
                                <i class="fa fa-check-square-o"></i>{{ isset($edit_timeslot->id) ? 'Update' : 'Add' }}
                            </button>
                            <a class="btn btn-danger" href="{{ route('timeslot.index') }}">
                                <i class="fa fa-times" aria-hidden="true"></i> {{ isset($edit_timeslot->id) ? 'Cancel' : 'Reset' }}
                            </a>

                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">List Timeslots</h3>
            </div>
            <div class="card-body">


                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th class="wd-20p">SL.NO</th>
                                <th class="wd-15p">Slot Name</th>
                                <th class="wd-15p">Time Slot</th>
                                <th class="wd-15p">Status</th>
                                <th class="wd-15p">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i = 0;
                            @endphp
                            @foreach($timeslots as $timeslot)
                            <tr id="dataRow_{{ $timeslot->id }}">
                                <td>{{ ++$i }}</td>
                                <td>{{ $timeslot->slot_name }}</td>
                                <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $timeslot->time_from)->format('g:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $timeslot->time_to)->format('g:i A') }}</td>
                                <td>
                                    <button type="button" onclick="changeStatus({{ $timeslot->id }})" class="btn btn-sm @if($timeslot->is_active == 0) btn-danger @else btn-success @endif">
                                        @if($timeslot->is_active == 0)
                                        InActive
                                        @else
                                        Active
                                        @endif
                                    </button>
                                </td>
                                <td>
                                    <!-- <a class="btn btn-primary btn-sm edit-custom" href="{{ route('timeslot.edit', $timeslot->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a> -->

                                    <form style="display: inline-block" action="{{ route('timeslot.destroy', $timeslot->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="button" onclick="deleteData({{ $timeslot->id }})" class="btn-danger btn-sm">
                                            <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                        </button>
                                    </form>
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
                        url: "{{ url('timeslot', '') }}/" + dataId,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            // Handle the success response, e.g., remove the row from the table
                            if (response == '1') {
                                $("#dataRow_" + dataId).remove();
                                i = 0;
                                $("#example tbody tr").each(function() {
                                    i++;
                                    $(this).find("td:first").text(i);
                                });
                                flashMessage('s', 'Data deleted successfully');
                            } else {
                                flashMessage('e', 'An error occured! Please try again later.');
                            }
                        },
                        error: function() {
                            alert('An error occurred while deleting the timeslot.');
                        },
                    });
                } else {
                    return;
                }
            });
    }

    // Change status 
    function changeStatus(dataId) {
        swal({
                title: "Change Status?",
                text: "Are you sure you want to change the status?",
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
                        url: "{{ route('timeslot.changeStatus', '') }}/" + dataId,
                        type: "patch",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response == '1') {
                                var cell = $('#dataRow_' + dataId).find('td:eq(3)');
                                var formStatusCheckbox = $('#is_active');
                                var checkEdit = $('#hidden_id').val();
                                var statusTextElement = $('#statusText');

                                // Update status in the table
                                if (cell.find('.btn-success').length) {
                                    cell.html('<button type="button" onclick="changeStatus(' + dataId + ')" class="btn btn-sm btn-danger">Inactive</button>');
                                } else {
                                    cell.html('<button type="button" onclick="changeStatus(' + dataId + ')" class="btn btn-sm btn-success">Active</button>');
                                }

                                // Update status in the form only if the dataId matches hidden_id
                                if (checkEdit == dataId) {
                                    formStatusCheckbox.prop('checked', !formStatusCheckbox.prop('checked'));
                                    formStatusCheckbox.val(formStatusCheckbox.prop('checked') ? '1' : '0');

                                    // Update statusText in the form
                                    statusTextElement.text(formStatusCheckbox.prop('checked') ? 'Active' : 'Inactive');
                                }

                                flashMessage('s', 'Status changed successfully');
                            } else {
                                flashMessage('e', 'An error occurred! Please try again later.');
                            }
                        },
                        error: function() {
                            alert('An error occurred while changing the qualification status.');
                        },
                    });
                }
            });
    }

    function toggleStatus(checkbox) {
        if (checkbox.checked) {
            $("#statusText").text('Active');
            $("input[name=is_active]").val(1); // Set the value to 1 when checked
        } else {
            $("#statusText").text('Inactive');
            $("input[name=is_active]").val(0); // Set the value to 0 when unchecked
        }
    }
</script>