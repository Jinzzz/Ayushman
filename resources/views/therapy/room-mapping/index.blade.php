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
            
            <form action="{{ route('therapy-map.room.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Select Therapy*</label>
                                <select class="form-control" required name="therapy_id" id="therapy_id">
                                    <option value="">Select Therapy</option>
                                    @foreach( $therapyList as $therapyLists)
                                    <option value="{{ $therapyLists->id }}">{{ $therapyLists->therapy_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Select Therapy Room*</label>
                                <select class="form-control" required name="therapy_room_id[]" id="therapy_room_id" multiple style="width: 100%;">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-square-o"></i> Submit
                        </button>&nbsp;&nbsp;
                        <a class="btn btn-primary" href="{{ route('therapymapping.index') }}">
                            <i class="fa fa-times" aria-hidden="true"></i> Reset
                        </a>
                        <a class="btn btn-primary" href="{{ route('therapy.index') }}">
                            Therapies
                        </a>
                    </div>
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
                           
                            <th class="wd-15p">Therapy Room</th>
                            <th class="wd-15p">Therapy</th>
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
                            <td>{{ optional($room->therapyRoom)->room_name ?? 'N/A' }}</td>
                            <td>{{ @$room->therapy['therapy_name'] }}</td>
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

$('#therapy_id').change(function() {
    var therapyId = $(this).val();

    $.ajax({
        url: '/check-therapyroom-availability',
        type: 'GET',
        data: {
            therapy_id: therapyId,
        },
        success: function(response) {
            console.log(response);
            updateRoomOptions(response.rooms);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});

function updateRoomOptions(rooms) {
    var roomSelect = $('#therapy_room_id');
    roomSelect.empty();
    roomSelect.append('<option value="">Select Room</option>');
    rooms.forEach(function(room) {
        roomSelect.append('<option value="' + room.id + '">' + room.room_name + '</option>');
    });
}
    

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
                        url: "{{ route('therapy.room.destroy', '') }}/" + dataId,
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
                            alert('An error occurred while deleting the data.');
                        },
                    });
                } else {
                    return;
                }
            });
    }
</script>
@endsection