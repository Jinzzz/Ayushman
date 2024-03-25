@extends('layouts.app')
@section('content')
@php
use App\Models\Mst_Staff;
@endphp
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Search Purchase Invoice</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('medicinePurchaseInvoice.index') }}" method="GET">
                    <div class="row mb-3">
                         <div class="col-md-3">
                            <label for="staff-name">Invoice Date</label>
                            <input type="date" id="staff-name" name="invoice_date" class="form-control" value="{{ request('invoice_date') }}" >
                        </div>
                         <div class="col-md-3">
                            <label for="contact-number">Due Date</label>
                            <input type="date" id="due_date" name="due_date" class="form-control" value="{{ request('due_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="contact-number">Pharmacy</label>
                           @if(Auth::check() && Auth::user()->user_type_id == 96)
                           @php
                            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
                            $mappedpharma = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
                           @endphp
                            <select class="form-control" name="pharmacy_id" id="pharmacy_id">
                                <option value="" {{ !request('id') ? 'selected' : '' }}>Choose Pharmacy</option>
                                @foreach ($pharmacies as $pharmacy)
                                       @if(in_array($pharmacy->id, $mappedpharma))
                                           <option value="{{ $pharmacy->id }}" {{request()->input('pharmacy_id') == $pharmacy->id ? 'selected':''}}>{{ $pharmacy->pharmacy_name }}</option>
                                       @endif
                                @endforeach
                            </select>
                        @else
                        <select class="form-control" name="pharmacy_id" id="pharmacy_id">
                                <option value="" {{ !request('id') ? 'selected' : '' }}>Choose Pharmacy</option>
                                @foreach($pharmacies as  $data)
                                    <option value="{{ $data->id }}"{{ old('id') == $data->id ? 'selected' : '' }}>
                                        {{ $data->pharmacy_name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        </div>
                            <div class="col-md-3">
                            <label for="contact-number">Select Supplier</label>
                            <select class="form-control" name="supplier_id" id="supplier_id">
                                 <option value="disabled selected"> Select Supplier </option> 
                                @foreach($suppliers as  $data)
                                    <option value="{{ $data->supplier_id  }}" }}>{{ $data->supplier_name }} </option>
                                @endforeach
                            </select>
                        </div>



                   </div>
                   <div class="row mb-3">
                                 
                        <div class="col-md-12 d-flex align-items-end">
                           
                                <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button> &nbsp; &nbsp;
                                <a class="btn btn-primary" href="{{ route('medicinePurchaseInvoice.index') }}"><i class="fa fa-times" aria-hidden="true"></i> Reset</a>
                          
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
      <h3 class="card-title">Medicine Purchase Invoice</h3>
   </div>
   <div class="card-body">
      <a href="{{ route('medicinePurchaseInvoice.create') }}" class="btn btn-block btn-info">
      <i class="fa fa-plus"></i>
      Create Medicine Purchase Invoice
      </a>
      <div class="table-responsive">
         <table id="example" class="table table-striped table-bordered text-nowrap w-100">
            <thead>
               <tr>
                  <th class="wd-15p">SL.NO</th>
                  <th class="wd-15p">Invoice No</th>
                  <th class="wd-20p">Supplier</th>
                  <th class="wd-20p">Pharmacy</th>
                  <th class="wd-15p">Invoice Date</th>
                  <th class="wd-15p">Due Date</th>
               
                  {{-- <th class="wd-15p">Reason</th> --}}
                  <th class="wd-15p">Total</th>
                   <th class="wd-15p">Paid Amount</th>
                 
                  <th class="wd-15p">Action</th>
               </tr>
            </thead>
            <tbody>
               @php
               $i = 0;
               @endphp
               @foreach($purchaseInvoice as $invoice)
               <tr id="dataRow_{{ $invoice->purchase_invoice_id }}">
                  <td>{{ ++$i }}</td>
                  <td>{{ $invoice->purchase_invoice_no }}</td>
                  <td>{{ @$invoice->supplier->supplier_name }}</td>
                  <td>{{ @$invoice->pharmacy_name }}</td>
                  <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</td>
                  <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') }}</td>

                  <td>{{ $invoice->total_amount }}</td>
                     <td>{{ isset($invoice->paid_amount) ? $invoice->paid_amount : '0.00' }}</td>

                      <td>
                        <a class="btn btn-secondary btn-sm" href="{{ route('medicinePurchaseInvoice.view',$invoice->purchase_invoice_id) }}">
                        <i class="fa fa-eye" aria-hidden="true"></i> View </a><br><br>
                     <form style="display: inline-block"
                        action="{{ route('medicinePurchaseInvoice.destroy', $invoice->purchase_invoice_id ) }}" method="post">
                        @csrf
                        @method('delete')
                        <button type="button" onclick="deleteData({{ $invoice->purchase_invoice_id }})"class="btn-danger btn-sm">
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
function flashMessage(type, message) {
    alert(type + ': ' + message);
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
                url: "{{ route('medicinePurchaseInvoice.destroy', '') }}/" + dataId,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response == '1') {
                        $("#dataRow_" + dataId).remove();
                        swal("Success", "Invoice deleted successfully", "success");
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