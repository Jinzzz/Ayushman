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
                  <th class="wd-20p">Supplier</th>
                  <th class="wd-15p">Return Date</th>
                  <th class="wd-15p">Branch</th>
                  <th class="wd-15p">Reason</th>
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
                  <td>{{ $return->supplier->supplier_name }}</td>
                  <td>{{ \Carbon\Carbon::parse($return->return_date)->format('d-m-Y') }}</td>
                  <td>{{ @$return->branch->branch_name }}</td>
                  <td>{{ $return->reason }}</td>
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
                       url: "{{ route('externaldoctors.destroy', '') }}/" + dataId,
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
                           alert('An error occurred while deleting the external doctor.');
                       },
                   });
               } else {
                   return;
               }
           });
   }
   
      // Change status 
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
                        url: "{{ route('externaldoctors.changeStatus', '') }}/" + dataId,
                        type: "patch",
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response == '1') {
                                var cell = $('#dataRow_' + dataId).find('td:eq(4)');

                                if (cell.find('.btn-success').length) {
                                    cell.html('<button type="button" onclick="changeStatus(' + dataId + ')" class="btn btn-sm btn-danger">Inactive</button>');
                                } else {
                                    cell.html('<button type="button" onclick="changeStatus(' + dataId + ')" class="btn btn-sm btn-success">Active</button>');
                                }

                                flashMessage('s', 'Status changed successfully');
                            } else {
                                flashMessage('e', 'An error occurred! Please try again later.');
                            }
                        },
                        error: function() {
                            alert('An error occurred while changing the branch status.');
                        },
                    });
                }
            });
    }
   </script>