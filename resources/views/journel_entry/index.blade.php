@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
        @if ($message = Session::get('s'))
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
                <h3 class="card-title">{{$pageTitle}} Search</h3>
            </div>
            <form action="{{ route('journel.entry.index') }}" method="GET">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="pat-code">Branch:</label>
                            <select class="form-control" name="branch_id" id="branch_id">
                                <option value="">Choose Branch</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->branch_id }}" {{ $branch->branch_id == $branch_id ? ' selected' : '' }}>{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="pat-code">Journel Entry Type:</label>
                            <select class="form-control" name="journel_entry_type_id" id="journel_entry_type_id">
                                <option value="">Choose Journel Entry Type</option>
                                @foreach($journel_entry_types as $journel_entry_type)
                                <option value="{{ $journel_entry_type->journal_entry_type_id }}" {{ $journel_entry_type->journal_entry_type_id == $journel_entry_type_id ? ' selected' : '' }}>{{ $journel_entry_type->journal_entry_type_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="pat-name">Journel Number:</label>
                            <input type="text" id="journel_number" name="journel_number" class="form-control" value="{{ request('journel_number') }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="pat-code">From Date:</label>
                            <input type="date" id="from_date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="pat-name">To Date:</label>
                            <input type="date" id="to_date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-4 d-flex justify-content-center align-items-end">
                            <div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button> &nbsp;
                                <a class="btn btn-primary" href="{{ route('journel.entry.index') }}"><i class="fa fa-times" aria-hidden="true"></i> Reset</a>
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
                <h3 class="card-title">List {{$pageTitle}}</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('journel.entry.create') }}" class="btn btn-block btn-info">
                    <i class="fa fa-plus"></i>
                    Create {{$pageTitle}}
                </a>

                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th class="wd-15p">SL.NO</th>
                                <th class="wd-15p">Branch Name</th>
                                <th class="wd-15p">Journel Entry Type</th>
                                <th class="wd-15p">Journel Number</th>
                                <th class="wd-15p">Journel Date</th>
                                <th class="wd-15p">Total Amout</th>
                                <th class="wd-15p">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i = 0;
                            @endphp
                            @foreach($journel_entries as $journel_entry)
                            <tr id="dataRow_{{ $journel_entry->journel_entry_id }}">
                                <td>{{ ++$i }}</td>
                                <td>{{ $journel_entry->branch->branch_name  }}</td>
                                <td>{{ $journel_entry->journel_entry_type->journal_entry_type_name  }}</td>
                                <td>{{ $journel_entry->journel_number  }}</td>
                                <td>{{ date('d-m-y', strtotime($journel_entry->journel_date)) }}</td>
                                <td>{{ $journel_entry->total_debit  }}</td>
                                <td>
                                    <a class="btn btn-primary btn-sm edit-custom" href="{{ route('journel.entry.edit', $journel_entry->journal_entry_id ) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a>
                                    <a class="btn btn-secondary btn-sm" href="{{ route('journel.entry.show',$journel_entry->journal_entry_id ) }}">
                                        <i class="fa fa-eye" aria-hidden="true"></i> View </a>
                                    <button type="button" onclick="deleteData({{ $journel_entry->journal_entry_id }})" class="btn btn-danger">
                                        <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                    </button>
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
                        url: "{{ route('journel.entry.destroy', '') }}/" + dataId,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            console.log(response);
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