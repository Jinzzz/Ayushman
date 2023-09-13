@extends('layouts.app')
@section('content')
<div class="container">
<div class="row" style="min-height: 70vh;">
<div class="col-md-12">
   <div class="card">
      <div class="card-header">
         <h3 class="mb-0 card-title">Edit Branch</h3>
      </div>
      <!--<div class="card-body">-->
      <!--   @if ($message = Session::get('status'))-->
      <!--   <div class="alert alert-success">-->
      <!--      <p></p>-->
      <!--   </div>-->
      <!--   @endif-->
      <!--</div>-->
      <div class="col-lg-12" style="background-color:#fff";>
         @if ($errors->any())
         <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
               @foreach ($errors->all() as $error)
               <li>{{ $error }}</li>
               @endforeach
            </ul>
         </div>
         @endif
         <form action="{{route('branches.update',['branch_id'=>$branch->branch_id])}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">Branch Name</label>
                     <input type="text" class="form-control" required name="branch_name" value="{{ $branch->branch_name }}" placeholder="Branch Name">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">Branch address</label>
                     <input type="text" class="form-control" required name="branch_address" value="{{ $branch->branch_address }}" placeholder="Branch Address">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">Branch Contact Number</label>
                     <input type="text" class="form-control" name="branch_contact_number" value="{{ $branch->branch_contact_number }}" placeholder="Branch Contact Number">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">Branch Email</label>
                     <input type="text" class="form-control" name="branch_email" value="{{ $branch->branch_email }}" placeholder="Branch Email">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">Branch Admin Name</label>
                     <input type="text" class="form-control"  name="branch_admin_name" value="{{ $branch->branch_admin_name }}" placeholder="Branch Admin Name">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">Branch Admin Contact Number</label>
                     <input type="text" class="form-control"  name="branch_admin_contact_number" value="{{ $branch->branch_admin_contact_number }}" placeholder="Branch Admin Contact Number">
                  </div>
               </div>
              
               <div class="col-md-6">
                  <div class="form-group">
                     <div class="form-label">Status</div>
                     <label class="custom-switch">
                     <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" @if($branch->is_active) checked @endif>
                     <span id="statusLabel" class="custom-switch-indicator"></span>
                     <span id="statusText" class="custom-switch-description">
                     @if($branch->is_active)
                     Active
                     @else
                     Inactive
                     @endif
                     </span>
                     </label>
                  </div>
               </div>
               <!-- ... -->
               <div class="col-md-12">
                  <div class="form-group">
                     <center>
                        <button type="submit" class="btn btn-raised btn-info">Update</button>
                        <a class="btn btn-danger" href="{{ route('branches') }}">Cancel</a>
                     </center>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>
@endsection
@section('js')
<script>
   function toggleStatus(checkbox) {
       if (checkbox.checked) {
           $("#statusText").text('Active');
           $("input[name=is_active]").val(1); // Set the value to 1 when checked
       } else {
           $("#statusText").text('Inactive');
           $("input[name=is_active]").val(0); // Set the value to 0 when unchecked
       }
   }
</script>
@endsection