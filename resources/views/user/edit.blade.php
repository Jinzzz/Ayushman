@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Edit User</h3>
            </div>
            <div class="card-body">
               @if ($message = Session::get('status'))
               <div class="alert alert-success">
                  <p>wafa</p>
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
               <form action="{{route('user.update',['id'=>$user->id])}}" method="POST" enctype="multipart/form-data">
                 @csrf
                @method('PUT')
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">User Name*</label>
                           <input type="text" class="form-control" required name="username" value="{{ $user->username }}" placeholder="User Name">
                        </div>
                     </div>
                     
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Password*</label>
                           <input type="text" class="form-control" required name="password"  placeholder="password">
                        </div>
                     </div>

                       <div class="col-md-6">
                        <div class="form-group">
                             <label class="form-label">Confirm Password</label>
                         <input type="text" class="form-control" required name="confirm_password" placeholder="Confirm Password">
                        </div>
                       </div>


                      <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">User Email*</label>
                           <input type="text" class="form-control" required name="user_email" value="{{$user->user_email}}" placeholder="User Email">
                        </div>
                     </div>


<div class="col-md-6">
                     <div class="form-group">
    <label for="user_type_id">User Type*</label>
    <select class="form-control" name="user_type_id" id="user_type_id">
        <option value="">Choose User Type</option>
        @foreach($userTypes as $id => $userType)
            <option value="{{ $id }}"{{$id == $user->user_type_id ?' selected' : ''}}>{{ $userType }}</option>
        @endforeach
    </select>
</div>
</div>

                        <div class="col-md-6">
                                <div class="form-group">
                                    <label for="branch_id">Branch*</label>
                                    <select class="form-control" name="branch_id" id="branch_id">
                                    <option value="">Choose Branch</option>
                                       @foreach($branches as $id => $branchName)
                                    <option value="{{ $id }}"{{$id == $user->branch_id ?' selected' : ''}}>{{ $branchName }}</option>
                                       @endforeach
                                    </select>
                                </div>
                                </div>
                                


                      
<div class="col-md-6">
    <div class="form-group">
        <div class="form-label">Status</div>
        <label class="custom-switch">
            <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" @if($user->is_active) checked @endif>
            <span id="statusLabel" class="custom-switch-indicator"></span>
            <span id="statusText" class="custom-switch-description">
                @if($user->is_active)
                    Active
                @else
                    Inactive
                @endif
            </span>
        </label>
    </div>
</div>

<!-- ... -->

                  

                     
                    <div class="col-md-6">
                        <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-info">Update</button>
                             <a class="btn btn-danger" href="{{ route('user.index') }}">Cancel</a>
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
