@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Search Purchae Invoice</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('medicinePurchaseReturn.index') }}" method="GET">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="staff-code">Purchase Return Number</label>
                            <input type="text" id="purchase_return_no" name="purchase_return_no" class="form-control" value="{{ request('purchase_return_no') }}" placeholder="Purchase Return Number">
                        </div>

                         <div class="col-md-3">
                            <label for="contact-number">Return Date</label>
                            <input type="date" id="return_date" name="return_date" class="form-control" value="{{ request('return_date') }}">
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
                                <a class="btn btn-primary" href="{{ route('medicinePurchaseReturn.index') }}"><i class="fa fa-times" aria-hidden="true"></i> Reset</a>
                          
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
      <h3 class="card-title">Medicine Purchase Return</h3>
   </div>
   <div class="card-body">
      <a href="{{ route('medicinePurchaseReturn.create') }}" class="btn btn-block btn-info">
      <i class="fa fa-plus"></i>
      Create Medicine Purchase Return
      </a>
      <div class="table-responsive">
         <table id="example" class="table table-striped table-bordered text-nowrap w-100">
            <thead>
               <tr>
                  <th class="wd-15p">SL.NO</th>
                  <th class="wd-15p">Purchase Return No</th>
                  <th class="wd-15p">Invoice ID</th>
                  <th class="wd-20p">Supplier</th>
                  <th class="wd-20p">Pharmacy</th>
                  <th class="wd-15p">Return Date</th>
                  <th class="wd-15p">Sub Total</th>  
                  <th class="wd-15p">Action</th>
               </tr>
            </thead>
            <tbody>
               @php
               $i = 0;
               @endphp
               @foreach($purchaseReturn as $return)
               <tr id="dataRow_{{ $return->purchase_return_id  }}">
                  <td>{{ ++$i }}</td>
                  <td>{{ $return->purchase_return_no }}</td>
                  <td>{{ @$return->PurchaseInvoice['purchase_invoice_no'] }}</td>
                  <td>{{ $return->supplier_name }}</td>
                  <td>{{ $return->pharmacy_name }}</td>
                  <td>{{ \Carbon\Carbon::parse($return->return_date)->format('d-m-Y') }}</td>
                  <td>{{ $return->sub_total }}</td>     
                  <td>
                     <a class="btn btn-primary btn-sm edit-custom"
                        href="{{ route('medicinePurchaseReturn.edit', $return->purchase_return_id ) }}"><i
                        class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a>
                     <a class="btn btn-secondary btn-sm" href="{{ route('medicinePurchaseReturn.show', $return->purchase_return_id ) }}">
                     <i class="fa fa-eye" aria-hidden="true"></i> View</a>
                     <form style="display: inline-block"
                        action="{{ route('externaldoctors.destroy', $return->purchase_return_id ) }}" method="post">
                        @csrf
                        @method('delete')
                        <button type="button" onclick="deleteData({{ $return->purchase_return_id }})"class="btn-danger btn-sm">
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
                url: "{{ route('medicinePurchaseReturn.destroy', '') }}/" + dataId,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response == '1') {
                        $("#dataRow_" + dataId).remove();
                        swal("Success", "Invoice Return deleted successfully", "success");
                    } else {
                        flashMessage('error', 'An error occurred! Please try again later.');
                    }
                },
                error: function() {
                    flashMessage('error', 'An error occurred while deleting the invoice.');
                },
            });
        } else {
            return;
        }
    });
}
</script>
