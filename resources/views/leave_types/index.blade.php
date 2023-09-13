@extends('layouts.app')

@section('content')
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
        @if ($message = Session::get('exists'))
        <div class="alert alert-danger">
            <p>{{$message}}</p>
        </div>
        @endif
        <div class="card-header">
            <h3 class="card-title">{{$pageTitle}}</h3>
        </div>
        <div class="card-body">
            <a href="{{ route('leave.type.create') }}" class="btn btn-block btn-info">
                <i class="fa fa-plus"></i>
                Add {{$pageTitle}}
            </a>



            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th class="wd-15p">SL.NO</th>
                            <th class="wd-20p">Leave Types</th>
                            <th class="wd-15p">Status</th>
                            <th class="wd-15p">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach($leave_types as $leave_type)
                        <tr id="qualificationRow_{{ $leave_types->leave_type_id }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ $leave_types->name}}</td>
                            <td>
                                <form action="{{ route('leave.type.changeStatus', $leave_types->leave_type_id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm @if($leave_types->is_active == 0) btn-danger @else btn-success @endif">
                                        @if($leave_types->is_active == 0)
                                        InActive
                                        @else
                                        Active
                                        @endif
                                    </button>
                                </form>
                            </td>

                            <td>
                                <a class="btn btn-primary" href="{{ route('leave.type.edit', $leave_types->leave_type_id) }}">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit
                                </a>

                                <form style="display: inline-block" action="{{ route('leave.type.destroy', $leave_types->leave_type_id) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" onclick="return confirm('Do you want to delete it?');" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i>Delete</button>
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
    function deleteQualification(qualificationId) {
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
                        url: "{{ route('qualifications.destroy', '') }}/" + qualificationId,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            // Handle the success response, e.g., remove the row from the table
                            if (response == '1') {
                                $("#qualificationRow_" + qualificationId).remove();
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