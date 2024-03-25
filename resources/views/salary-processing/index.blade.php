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
                    <h3 class="card-title">Salary Processing</h3>
                </div>
                <div class="card-body">
                    <a href="{{route('create.salary-processing')}}" class="btn btn-block btn-info">
                        <i class="fa fa-plus"></i>
                        Add Salary Processing
                    </a>
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Salary<br>Month</th>
                                    <th class="wd-15p">Staff</th>
                                    <th class="wd-15p">Processed <br> Date</th>
                                    <th class="wd-15p">Branch</th>
                                    <th class="wd-15p">Net Earnings</th>
                                    <th class="wd-20p">Status</th>
                                    <th class="wd-15p">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($processDatas as $key => $processData)
                                    <tr id="dataRow_{{ @$processData->id }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{@$processData->salary_month}}</td>
                                        <td>{{@$processData->staff->staff_username}}</td>
                                        <td>{{@$processData->processed_date}}</td>
                                        <td>{{@$processData->branch->branch_name}}</td>
                                        <td>{{@$processData->net_earning}}</td>
                                        <td> <button type="button" style="width: 115px;" @if(@$processData->processing_status==1) onclick="changeStatus({{ $processData->id }})" @endif class="btn btn-sm @if(@$processData->processing_status==1) btn-danger @else btn-success @endif">
                                    @if(@$processData->processing_status==1)Pending @else Paid @endif
                                </button></td>
                                        
                                        
                                        <td>
                                            <a class="btn btn-primary" href="{{ route('salary_processing.view', ['id' => @$processData->id]) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> View </a>
                                            {{--<a class="btn btn-danger" href=""><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Delete </a>--}}
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
    <script>
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
                        url: "{{ route('salary_processing.changeStatus', '') }}/" + dataId,
                        type: "patch",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response == '1') {
                                var cell = $('#dataRow_' + dataId).find('td:eq(6)');

                                if (cell.find('.btn-success').length) {
                                    cell.html('<button type="button" style="width: 70px;"  onclick="changeStatus(' + dataId + ')" class="btn btn-sm btn-danger">Pending</button>');
                                } else {
                                    cell.html('<button type="button" style="width: 70px;"  onclick="changeStatus(' + dataId + ')" class="btn btn-sm btn-success">Paid</button>');
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
                   // alert('under modification');
                }
            });
    }
    </script>
@endsection
