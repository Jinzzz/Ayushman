@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Create User</h3>
            </div>
            <div class="card-body">
               @if ($message = Session::get('status'))
               <div class="alert alert-success">
                  <p></p>
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
               <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data">

                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Username</label>
                           <input type="text" class="form-control" required name="username" value="{{old('username')}}" placeholder="Username">
                        </div>
                     </div>

                    
<div class="col-md-6">
    <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-group">
            <input type="password" class="form-control" required name="password" id="password" placeholder="Password" autocomplete="off">
            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="fa fa-eye toggle-password" onclick="togglePasswordVisibility('password')"></i>
                </span>
            </div>
        </div>
    </div>
</div>

                         <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" required name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye toggle-password" onclick="togglePasswordVisibility('confirm_password')"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">User Email</label>
                           <input type="text" class="form-control" required name="user_email" value="{{old('user_email')}}" placeholder="User Email">
                        </div>
                     </div>
<div class="col-sm-6">                   <!-- ... -->
   <div class="form-group"> 
    <label for="user_type_id" class="form-label">User Type</label>
    <select class="form-control" name="user_type_id" id="user_type_id">
        <option value="">Choose User Type</option>
        @foreach($userTypes as $id => $userType)
            <option value="{{ $id }}">{{ $userType }}</option>
        @endforeach
    </select>
   </div>
</div>

  <!-- ... -->
                      
<div class="col-sm-6">
                                 <div class="form-group">
                                    <label for="branch_id" class="form-label">Branch</label>
                                    <select class="form-control" name="branch_id" id="branch_id">
                                    <option value="">Choose Branch</option>
                                       @foreach($branches as $id => $branchName)
                                    <option value="{{ $id }}">{{ $branchName }}</option>
                                       @endforeach
                                    </select>
                                 </div>
                                 </div>
<div class="col-md-6">
   <div class="form-group">
      <div class="form-label">Status</div>
      <label class="custom-switch">
         <input type="hidden" name="is_active" value="0"> <!-- Hidden field for false value -->
         <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" checked>
         <span id="statusLabel" class="custom-switch-indicator"></span>
         <span id="statusText" class="custom-switch-description">Active</span>
      </label>
   </div>
</div>
</div>

<!-- ... -->

                  


                        <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                          
                           <a class="btn btn-danger" href="{{route('user.index')}}">Cancel</a>
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

    function togglePasswordVisibility(inputFieldId) {
        var inputField = document.getElementById(inputFieldId);
        if (inputField.type === "password") {
            inputField.type = "text";
        } else {
            inputField.type = "password";
        }
    }
</script>


@endsection
