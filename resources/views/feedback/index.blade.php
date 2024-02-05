@extends('layouts.app')
@section('content')
    <style>
        .fa-eye:before {
            color: #fff !important;
        }
    </style>
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Feedbacks</h3>
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
                    <h3 class="card-title">List Feedbacks</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Patient</th>
                                    <th class="wd-15p">Ratings</th>
                                    <th class="wd-15p">Average Rating</th>
                                    <th class="wd-15p">Feedback</th>
                                    <th class="wd-20p">Status</th>
                                    <th class="wd-15p">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($feedData as $feedDatas)
                                    <tr id="dataRow_{{ $feedDatas->id }}">
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $feedDatas->patient['patient_name'] }}</td>
                                        <td>
                                            Consultant : {{ $feedDatas->consultancy_rating }} / 5
                                            <br>
                                            Visit : {{ $feedDatas->visit_rating }} / 5 
                                            <br>
                                            Service : {{ $feedDatas->service_rating }} / 5 
                                            <br>
                                            Pharmacy : {{ $feedDatas->pharmacy_rating }} / 5 
                                            <br>
                                            Appointment : {{ $feedDatas->appointment_rating }} / 5

                                        </td>
                                        <td>{{ $feedDatas->average_rating }} / 5</td>
                                        <td>{!! wordwrap($feedDatas->feedback, 30, "<br>") !!}</td>
                                        <td>
                                            <button type="button" style="width: 70px;"
                                                onclick="changeStatus({{ $feedDatas->id }})"
                                                class="btn btn-sm @if ($feedDatas->is_active == 0) btn-danger @else btn-success @endif">
                                                @if ($feedDatas->is_active == 0)
                                                    Inactive
                                                @else
                                                    Active
                                                @endif
                                            </button>
                                        </td>
                                        <td>
                                            <form style="display: inline-block"
                                                action="{{ route('feedback.destroy', $feedDatas->id) }}"
                                                method="post">
                                                @csrf
                                                @method('delete')
                                                <button type="button" onclick="deleteData({{ $feedDatas->id }})"
                                                    class="btn-danger btn-sm">
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
                        url: "{{ route('feedback.destroy', '') }}/" + dataId,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            handleDeleteResponse(response, dataId);
                        },
                        error: function() {
                            alert('An error occurred while deleting the branch.');
                        },
                    });
                }
            });
    }

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
                        url: "{{ route('feedback.changeStatus', '') }}/" + dataId,
                        type: "GET",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response == '1') {
                                var cell = $('#dataRow_' + dataId).find('td:eq(5)');

                                if (cell.find('.btn-success').length) {
                                    cell.html(
                                        '<button type="button" style="width: 70px;"  onclick="changeStatus(' +
                                        dataId +
                                        ')" class="btn btn-sm btn-danger">Inactive</button>');
                                } else {
                                    cell.html(
                                        '<button type="button" style="width: 70px;"  onclick="changeStatus(' +
                                        dataId + ')" class="btn btn-sm btn-success">Active</button>'
                                        );
                                }
                              
                                $.growl.notice({
                                    message: "Status updated"
                                });
                            } else {
                                $.growl.error({
                                    title: "Oops!",
                                    message: "Something went wrong"
                                    });
                            }
                        },
                        error: function() {
                            $.growl.error({
                                title: "Oops!",
                                message: "Something went wrong"
                            });
                        },
                    });
                }
            });
    }


    function handleDeleteResponse(response, dataId) {
        console.log(response.success);
        if (response.success) {
            swal("Success", response.message, "success");
            $("#dataRow_" + dataId).remove();
        } else {
            swal("Error", "An error occurred! Please try again later.", "error");
        }
    }

    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 2000);
</script>
