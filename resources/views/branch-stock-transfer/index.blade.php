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
                    <h3 class="card-title">Stock Transfer to Pharmacies</h3>
                </div>
                <div class="card-body">
                    <a href="{{route('create.branch-stock-transfer')}}" class="btn btn-block btn-info">
                        <i class="fa fa-plus"></i>
                        Add Stock Transfer to Pharmacy
                    </a>
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Transfer Code</th>
                                    <th class="wd-15p">Transfer<br>Date</th>
                                    <th class="wd-15p">Pharmacy From</th>
                                    <th class="wd-15p">Pharmacy To</th>
                                    
                                    <th class="wd-15p">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($processDatas as $key => $processData)
                                    <tr id="">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{$processData->transfer_code }}</td>
                                        <td>{{ $processData->transfer_date }}</td>
                                        <td>{{ $processData->pharmacy['pharmacy_name'] }}</td>
                                        <td>{{ $processData->pharmacys['pharmacy_name'] }}</td>
                                        <td>
                                            <a style="margin-right:10px;" class="btn btn-primary" href="{{ route('stock-transfer-history.show', ['id' => $processData->id]) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> View </a> 
                                            <form style="display: inline-block"
                                        action="{{ route('medicine.destroy', $processData->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="button"
                                            onclick="deleteData({{ $processData->id }})"class="btn-danger btn-sm">
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
@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
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
                url: "{{ route('stock-transfer.destroy', '') }}/" + dataId,
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
                        }, 1000);
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

