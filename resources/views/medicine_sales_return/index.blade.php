@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Search Purchae Invoice</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('medicine.sales.return.index') }}" method="GET">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="staff-code">Sales Return Number</label>
                            <input type="text" id="sales_return_no" name="sales_return_no" class="form-control" value="{{ request('sales_return_no') }}" placeholder="Sales Return Number">
                        </div>
                         <div class="col-md-3">
                            <label for="staff-name">Return Date</label>
                            <input type="date" id="return_date" name="return_date" class="form-control" value="{{ request('return_date') }}" >
                        </div>

                        <div class="col-md-3">
                        <label for="contact-number">Pharmacy</label>
                            <select class="form-control" name="pharmacy_id" id="pharmacy_id">
                                <option value="" {{ !request('id') ? 'selected' : '' }}>Choose Pharmacy</option>
                                @foreach($pharmacies as  $data)
                                    <option value="{{ $data->id }}"{{ old('id') == $data->id ? 'selected' : '' }}>
                                        {{ $data->pharmacy_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                   </div>
                   <div class="row mb-3">
                                 
                        <div class="col-md-12 d-flex align-items-end">
                           
                                <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button> &nbsp; &nbsp;
                                <a class="btn btn-primary" href="{{ route('medicine.sales.return.index') }}"><i class="fa fa-times" aria-hidden="true"></i> Reset</a>
                          
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
        <h3 class="card-title">{{$pageTitle}}</h3>
    </div>
    <div class="card-body">
        <a href="{{ route('medicine.sales.return.create') }}" class="btn btn-block btn-info">
            <i class="fa fa-plus"></i>
            Create {{$pageTitle}}
        </a>
        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th class="wd-15p">SL.NO</th>
                        <th class="wd-15p">Return No</th>
                        <th class="wd-15p">Return Date</th>
                        <th class="wd-15p">Pharmacy</th>
                        <th class="wd-15p">Sales person</th>
                        <th class="wd-15p">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i = 0;
                    @endphp
                    @foreach($purchaseReturn as $invoice)
                    <tr id="dataRow_{{ $invoice->sales_return_id  }}">
                        <td>{{ ++$i }}</td>
                        <td>{{ $invoice->sales_return_no }}</td>
                        <td>{{ \Carbon\Carbon::parse($invoice->return_date)->format('d-m-Y') }}</td>
                        <td>{{ $invoice->pharmacy_name }}</td>
                        <td>{{ $invoice->staff->staff_name }}</td>
                        
                        <!-- <td>{{ $invoice->discount_amount }}</td> -->
                        <td>
                            <!-- <a class="btn btn-primary btn-sm edit-custom" href="{{ route('medicine.sales.return.edit', $invoice->sales_return_id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a> -->
                            <a class="btn btn-primary btn-sm edit-custom" href="{{ route('medicine.sales.return.print', $invoice->sales_return_id) }}"><i class="fa fa-print" aria-hidden="true"></i>
                                Print </a>
                            <a class="btn btn-secondary btn-sm" href="{{ route('medicine.sales.return.show', $invoice->sales_return_id) }}">
                                <i class="fa fa-eye" aria-hidden="true"></i> View</a>
                            <form style="display: inline-block" action="" method="post">
                                @csrf
                                @method('delete')
                                <button type="button" onclick="deleteData({{ $invoice->sales_return_id  }})" class="btn-danger btn-sm">
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>

function showSuccessMessage(message) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: message,
        });
    }

    function showErrorMessage(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
        });
    }
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
                        url: "{{ route('medicine.sales.return.destroy', '') }}/" + dataId,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            // Handle the success response, e.g., remove the row from the table
                            if (response == '1') {
                                $("#dataRow_" + dataId).remove();
                                showSuccessMessage('Data deleted successfully');
                            } else {
                                showErrorMessage('An error occurred! Please try again later.'); 
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