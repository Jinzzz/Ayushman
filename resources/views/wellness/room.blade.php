@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
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
            @if ($message = Session::get('exists'))
            <div class="alert alert-danger">
                <p>{{$message}}</p>
            </div>
            @endif
            @if ($errors->any())
            <div class="alert alert-danger">
                <!-- <strong>Whoops!</strong> There were some problems with your input.<br><br> -->
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="card-header">
                <h3 class="card-title"><strong>{{$pageTitle}}</strong></h3>
            </div>
            <form action="{{ route('wellness.room.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label"> Select Branch*</label>
                                <select class="form-control" required name="branch_id" id="branch_id">
                                    <option value="">Select Branch</option>
                                    @foreach( $branches as $branch_id => $branch_name)
                                    <option value="{{ $branch_id }}">{{ $branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label"> Select Wellness*</label>
                                <select class="form-control" required name="wellness_id" id="wellness_id">
                                    <option value="">Select Wellness</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Select Therapy Room*</label>
                                <select class="multi-select to-hidden" required name="therapy_room_id[]" id="therapy_room_id" multiple style="width: 100%;">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-square-o"></i> Submit
                        </button>&nbsp;&nbsp;
                        <a class="btn btn-primary" href="{{ route('wellness.room.assign') }}">
                            <i class="fa fa-times" aria-hidden="true"></i> Reset
                        </a>
                        <a class="btn btn-primary" href="{{ route('wellness.index') }}">
                            Wellnesses
                        </a>
                    </div>
                </div>
        </div>
        </form>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Assigned Rooms</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th class="wd-20p">SL.NO</th>
                            <th class="wd-15p">Branch</th>
                            <th class="wd-15p">Therapy Room</th>
                            <th class="wd-15p">Wellness</th>
                            <th class="wd-15p">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach($assignedRooms as $room)
                        <tr id="dataRow_{{ $room->id }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ $room->branch->branch_name ?? 'N/A' }}</td>
                            <td>{{ optional($room->therapyRoom)->room_name ?? 'N/A' }}</td>
                            <td>{{ optional($room->wellness)->wellness_name ?? 'N/A' }}</td>
                            <td>
                                <button type="button" onclick="deleteData({{ $room->id }})" class="btn btn-danger">
                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                </button>
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
<!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $('#branch_id').on('change', function() {
        var selected_branch_id = $(this).val();
        $.ajax({
            url: "{{ route('get.branch.room.wellness', '') }}/" + selected_branch_id,
            method: "get",
            data: {
                _token: "{{ csrf_token() }}",
            },
            success: function(data) {
                $(".to-hidden").css("display", "none");

                $('#wellness_id').empty().append('<option value="">Select Wellness</option>');

                // Populate the dropdown with wellness options
                $.each(data.wellnesses, function(key, value) {
                    $('#wellness_id').append('<option value="' + key + '">' + value + '</option>');
                });

                // Clear existing options and append new ones
                $('#therapy_room_id').empty().append('<option disabled value="">Select Therapy Room</option>');

                // Add "Select All" option
                // $('#therapy_room_id').append('<option value="all">Select All</option>');

                // Append a new option for each room
                $.each(data.rooms, function(key, value) {
                    $('#therapy_room_id').append('<option value="' + key + '">' + value + '</option>');
                });

                // Initialize or reinitialize the Select2 plugin on the multi-select dropdown
                $('#therapy_room_id').select2({
                    // Add options if needed
                });

                // Handle "Select All" option
                $('#therapy_room_id').on('change', function() {
                    if ($(this).val() === 'all') {
                        // Select all options
                        $('#therapy_room_id').find('option').prop('selected', true);
                        $(this).trigger('change');
                    }
                });

            },
            error: function() {
                console.log('Error fetching data.');
            }
        });

    });

    // delete data 
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
                        url: "{{ route('wellness.room.destroy', '') }}/" + dataId,
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
                            alert('An error occurred while deleting the qualification.');
                        },
                    });
                } else {
                    return;
                }
            });
    }
</script>
@endsection