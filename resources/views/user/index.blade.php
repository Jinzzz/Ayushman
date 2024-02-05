@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">User Search</h3>
    </div>
    <form action="{{ route('user.index') }}" method="GET">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">User Type:</label>
                    <select class="form-control" name="user_type_id" id="user_type_id">
                        <option value="">Choose User Type</option>
                        @foreach($userTypes as $id => $userType)
                        <option value="{{ $id }}" {{ $id == $user_type_id ? 'selected' : ''}}>{{ $userType }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Username:</label>
                    <input type="text" id="username" name="username" class="form-control" value="{{ request('username') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email:</label>
                    <input type="text" id="email" name="email" class="form-control" value="{{ request('email') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button> &nbsp;
                        <a class="btn btn-primary" href="{{ route('user.index') }}"><i class="fa fa-times" aria-hidden="true"></i> Reset</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="row">
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
            <h3 class="card-title">List Users</h3>
        </div>
        <div class="card-body">
            <a href="{{ route('user.create') }}" class="btn btn-block btn-info">
                <i class="fa fa-plus"></i>
                Create User
            </a>
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th class="wd-15p">SL.NO</th>
                            <th class="wd-15p">Username</th>
                            <th class="wd-15p">User Email</th>
                            <th class="wd-15p">User Type</th>
                            <th class="wd-15p">Staff</th>
                            {{-- <th class="wd-15p">Last Login Time</th> --}}
                            <th class="wd-15p">Status</th>
                            <th class="wd-15p">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach($users as $user)
                        <tr id="dataRow_{{ $user->user_id }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->userType->master_value??''}}</td>
                            <td>{{ $user->Staff->staff_name??''}}</td>
                            {{-- <td>{{ $user->last_login_time }}</td> --}}
                            <td>
                                <button type="button" style="width: 70px;" onclick="changeStatus({{$user->user_id}})" class="btn btn-sm @if($user->is_active == 0) btn-danger @else btn-success @endif">
                                    @if($user->is_active == 0)
                                    Inactive
                                    @else
                                    Active
                                    @endif
                                </button>
                            </td>

                            <td>
                                <a class="btn btn-primary btn-sm edit-custom" href="{{ route('user.edit', $user->user_id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a>
                                {{-- <a class="btn btn-secondary btn-sm" href="{{ route('user.show', $doctor->id) }}">
                                <i class="fa fa-eye" aria-hidden="true"></i> View</a> --}}
                                <form style="display: inline-block" action="{{ route('user.destroy', $user->user_id) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button type="button" onclick="deleteData({{ $user->user_id}})" class="btn-danger btn-sm">
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
                        url: "{{ route('user.destroy', '') }}/" + dataId,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            // Handle the success response, e.g., remove the row from the table
                            if (response == '1') {
                                $("#dataRow_" + dataId).remove();
                                flashMessage('s', 'Data deleted successfully');
                            } else {
                                flashMessage('e', 'An error occured! Please try again later.');
                            }
                        },
                        error: function() {
                            alert('An error occurred while deleting the user.');
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
                        url: "{{ route('user.changeStatus', '') }}/" + dataId,
                        type: "patch",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response == '1') {
                                var cell = $('#dataRow_' + dataId).find('td:eq(5)');

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
                            alert('An error occurred while changing the user status.');
                        },
                    });
                }
            });
    }
</script>