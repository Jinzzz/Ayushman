@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Change Password</h3>
            </div>
            <div class="card-body">
               @if ($message = Session::get('status'))
               <div class="alert alert-success">
                  <p>{{$message}}</p>
               </div>
               @endif
                 @if ($message = Session::get('error'))
               <div class="alert alert-danger">
                  <p>{{$message}}</p>
               </div>
               @endif
            </div>
            <div class="col-lg-12">
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
               <form action="{{route('doctor.profile.updatePassword')}}" method="POST" enctype="multipart/form-data">

                  @csrf
                  <div class="row">
                     
                    
                     <div class="col-md-10">
                        <div class="form-group">
                           <label class="form-label">Current Password</label>
                           <input type="password" class="form-control" required name="old_password"  placeholder="Enter current password">
                        </div>
                     </div>
                      <div class="col-md-10">
                        <div class="form-group">
                           <label class="form-label">New Password</label>
                           <input type="password" class="form-control" required name="password"  placeholder="Enter new password">
                        </div>
                     </div>
                      <div class="col-md-10">
                        <div class="form-group">
                           <label class="form-label">Re-enter New Password</label>
                           <input type="password" class="form-control" required name="password_confirmation"  placeholder="Retype new password">
                        </div>
                     </div>


                   
                     <!-- ... -->


  <!-- ... -->
                      

                                


<!-- ... -->

                  


                        <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
                          
                           <a class="btn btn-danger" href="#">Cancel</a>
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
