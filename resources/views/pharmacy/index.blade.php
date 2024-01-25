@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Search Pharmacy</h3>
            </div>
            <form action="{{ route('pharmacy.index') }}" method="GET">
                <div class="card-body">
                    <div class="row mb-3">

      
                        <div class="col-md-3">
                            <label for="supplier-name" class="form-label">Pharmacy Name:</label>
                            <input type="text" id="pharmacy-name" name="pharmacy_name" class="form-control" value="{{ request('pharmacy_name') }}">
                        </div>
     
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>&nbsp;
                            <a class="btn btn-primary" href="{{ route('pharmacy.index') }}"><i class="fa fa-times" aria-hidden="true"></i> Reset</a>
                        </div>
                    </div>
                </div>
        </div>
        </form>
    </div>
    <div class="card">

        @if ($message = Session::get('success'))
        <div class="alert alert-success"  id="successAlert">
            <p>{{$message}}</p>
        </div>
        @endif
        @if ($message = Session::get('error'))
        <div class="alert alert-danger" id="errorAlert">
            <p>{{$message}}</p>
        </div>
        @endif
        <div class="card-header">
            <h3 class="card-title">List Pharmacy</h3>
        </div>
        <div class="card-body">
            <a href="{{ route('pharmacy.create') }}" class="btn btn-block btn-info">
                <i class="fa fa-plus"></i>
                Create Pharmacy
            </a>
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th class="wd-15p">SL.NO</th>
                            <th class="wd-15p">Pharmacy Name</th>
                            <th class="wd-15p">Branch Name</th>
                            <th class="wd-15p">Status</th>
                            <th class="wd-15p">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach($pharmacies as $pharmacy)
                        <tr id="dataRow_{{$pharmacy->id}}">
                            <td>{{ ++$i }}</td>
                            <td>{{ $pharmacy->pharmacy_name }}</td>
                            <td>{{ $pharmacy->branch_name }}</td>
                            <td>
                                <button type="button" style="width: 70px;" onclick="changeStatus({{ $pharmacy->id }})" class="btn btn-sm @if($pharmacy->status == 0) btn-danger @else btn-success @endif">
                                    @if($pharmacy->status == 0)
                                    Inactive
                                    @else
                                    Active
                                    @endif
                                </button>
                            </td>

                            <td>
                            <a class="btn btn-primary btn-sm edit-custom" href="{{ route('pharmacy.edit', $pharmacy->id) }}">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit
                            </a>
          
                                <button type="button" onclick="deleteData({{ $pharmacy->id }})" class="btn btn-danger">
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    setTimeout(function() {
        $('#successAlert').remove();
    }, 3000);
    setTimeout(function() {
        $('#errorAlert').remove();
    }, 3000);
</script>
<script>
function deleteData(dataId) {
    // Display SweetAlert confirmation dialog
    Swal.fire({
        title: "Delete selected data?",
        text: "Are you sure you want to delete this data?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: true,
        closeOnCancel: true
    }).then(function(result) {
        if (result.isConfirmed) {

            $.ajax({
                url: "{{ route('pharmacy.destroy', '') }}/" + dataId,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response == '1') {
                        $("#dataRow_" + dataId).remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'Data deleted successfully',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        showErrorMessage('An error occurred! Please try again later.');
                    }
                },
                error: function() {
                    showErrorMessage('An error occurred while deleting the staff.');
                },
            });
        } else {
            return;
        }
    });
}
</script>

