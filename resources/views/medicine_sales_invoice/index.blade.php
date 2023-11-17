@extends('layouts.app')
@section('content')
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
        <a href="{{ route('medicine.sales.invoices.create') }}" class="btn btn-block btn-info">
            <i class="fa fa-plus"></i>
            Create {{$pageTitle}}
        </a>
        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th class="wd-15p">SL.NO</th>
                        <th class="wd-15p">Branch</th>
                        <th class="wd-15p">Invoice No</th>
                        <th class="wd-15p">Invoice Date</th>
                        <!-- <th class="wd-15p">Total Amount</th> -->
                        <!-- <th class="wd-15p">Discount Amount</th> -->
                        <th class="wd-15p">Paid Amount</th>
                        <th class="wd-15p">Sales person</th>
                        <th class="wd-15p">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i = 0;
                    @endphp
                    @foreach($medicineSalesInvoice as $invoice)
                    <tr id="dataRow_{{ $invoice->sales_invoice_id }}">
                        <td>{{ ++$i }}</td>
                        <td>{{ $invoice->branch->branch_name }}</td>
                        <td>{{ $invoice->sales_invoice_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</td>
                        <!-- <td>{{ $invoice->total_amount }}</td> -->
                        <!-- <td>{{ $invoice->discount_amount }}</td> -->
                        <td>{{ $invoice->payable_amount }}</td>
                        <td>{{ $invoice->staff->staff_name }}</td>
                        <td>
                            <a class="btn btn-primary btn-sm edit-custom" href="{{ route('medicine.sales.invoices.edit', $invoice->sales_invoice_id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a>
                            <a class="btn btn-primary btn-sm edit-custom" href="{{ route('medicine.sales.invoices.print', $invoice->sales_invoice_id) }}"><i class="fa fa-print" aria-hidden="true"></i>
                            Print </a>

                            <a class="btn btn-secondary btn-sm" href="{{ route('medicine.sales.invoices.show', $invoice->sales_invoice_id) }}">
                                <i class="fa fa-eye" aria-hidden="true"></i> View</a>
                            <form style="display: inline-block" action="" method="post">
                                @csrf
                                @method('delete')
                                <button type="button" onclick="deleteData({{ $invoice->sales_invoice_id }})" class="btn-danger btn-sm">
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
                        url: "{{ route('medicine.sales.invoices.destroy', '') }}/" + dataId,
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
                            alert('An error occurred while deleting the data.');
                        },
                    });
                } else {
                    return;
                }
            });
    }
</script>