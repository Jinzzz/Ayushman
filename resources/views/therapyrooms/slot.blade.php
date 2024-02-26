@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><strong>Assigning Timeslots</strong></h3>
            </div>
            <form action="{{ route('room.slot.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"> Week Day *</label>
                                <select class="form-control" required name="week_day" id="week_day">
                                    <option value="">Select Week Day</option>
                                    @foreach( $weekdays as $masterId => $masterValue)
                                    <option value="{{ $masterId }}">{{ $masterValue }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"> Time Slot *</label>
                                <select class="form-control" required name="time_slot" id="slot">
                                    <option value="">Select Time Slot</option>
                                    @foreach( $slots as $slot)
                                    <option value="{{ $slot->id }}">{{ \Carbon\Carbon::createFromFormat('H:i:s', $slot->time_from)->format('g:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $slot->time_to)->format('g:i A') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="hidden" class="form-control" required name="therapy_room_id" value="{{$id}}">
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-square-o"></i> Submit
                        </button>&nbsp;&nbsp;
                        <a class="btn btn-primary" href="{{ route('slot_assigning.index',$id) }}">
                            <i class="fa fa-times" aria-hidden="true"></i> Reset
                        </a>
                        <a class="btn btn-primary" href="{{ route('therapyrooms.index') }}">
                             Therapy Rooms
                        </a>
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
        @if ($message = Session::get('exists'))
        <div class="alert alert-danger">
            <p>{{$message}}</p>
        </div>
        @endif
        <div class="card-header">
            <h3 class="card-title">Assigned Timeslots</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th class="wd-20p">SL.NO</th>
                            <th class="wd-15p">Week Day</th>
                            <th class="wd-15p">Time Slot</th>
                            <th class="wd-15p">Status</th>
                            <th class="wd-15p">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach($timeslots as $slot)
                        <tr id="dataRow_{{ $slot->id }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ @$slot->weekDay['master_value']}}</td>
                            
                            <td>
                                @if(isset($slot->slot['time_from'])&& !empty($slot->slot['time_from']))
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', @$slot->slot['time_from'])->format('g:i A') }} @endif -
                                @if(isset($slot->slot['time_to']) && !empty($slot->slot['time_to']))
                                 {{ \Carbon\Carbon::createFromFormat('H:i:s', @$slot->slot['time_to'])->format('g:i A') }}
                                 @endif
                                </td>
                            <td>
                                <button type="button" style="width: 70px;"  onclick="changeStatus({{ $slot->id }})" class="btn btn-sm @if($slot->is_active == 0) btn-danger @else btn-success @endif">
                                    @if($slot->is_active == 0)
                                    InActive
                                    @else
                                    Active
                                    @endif
                                </button>
                            </td>
                            <td>
                                <button type="button" onclick="deleteData({{ $slot->id }})" class="btn btn-danger">
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
                        url: "{{ route('room.slot.destroy', '') }}/" + dataId,
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
                        url: "{{ route('room.slot.changeStatus', '') }}/" + dataId,
                        type: "patch",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response == '1') {
                                var cell = $('#dataRow_' + dataId).find('td:eq(3)');

                                if (cell.find('.btn-success').length) {
                                    cell.html('<button type="button" style="width: 70px;"  onclick="changeStatus(' + dataId + ')" class="btn btn-sm btn-danger">Inactive</button>');
                                } else {
                                    cell.html('<button type="button" style="width: 70px;"  onclick="changeStatus(' + dataId + ')" class="btn btn-sm btn-success">Active</button>');
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
</script>