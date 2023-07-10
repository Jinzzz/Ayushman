@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Update Profile</h3>
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
               <form action="{{route('doctor.profile.updateProfile')}}" method="POST" enctype="multipart/form-data">

                  @csrf
                  <div class="row">
                     
                    
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Name(*)</label>
                           <input type="text" class="form-control" required name="user_name"  placeholder="Enter username" value="{{$doctor_user->username??''}}">
                        </div>
                     </div>
                      <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Email(*)</label>
                           <input type="email" class="form-control" required name="user_email"  placeholder="Enter email" value="{{$doctor_user->user_email??''}}">
                        </div>
                     </div>
                       <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Date Of Birth(*)</label>
                           <input type="date" class="form-control" required name="date_of_birth"  placeholder="Enter Date of birth" value="{{$doctor_user->profile->date_of_birth??''}}">
                        </div>
                     </div>
                      <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Gender</label>
                           <select class="form-control" required name="gender_id">
                           <option value="">Choose Gender</option>
                           @foreach($genders as $gender)
                           <option value="{{$gender->id}}" @if($doctor_user->profile!=NULL) @if($gender->id==$doctor_user->profile->gender_id) selected @endif @endif>{{$gender->gender_name}}</option>
                           @endforeach
                           </select>
                           
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Blood Group</label>
                           <select class="form-control" required name="blood_group_id">
                           <option value="">Choose Group</option>
                           @foreach($blood_groups as $group)
                           <option value="{{$group->blood_group_id}}" @if($doctor_user->profile!=NULL) @if($group->blood_group_id==$doctor_user->profile->blood_group_id) selected @endif @endif>{{$group->blood_group_name}}</option>
                           @endforeach
                           </select>
                           
                     </div>
                     </div>
                       <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Address(*)</label>
                           <textarea class="form-control" required name="user_address"  placeholder="Enter Address">{{$user->profile->address??''}}</textarea>
                        </div>
                     </div>
                      <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Profile Image(*)</label>
                           <input type="file" class="form-control" required name="user_profile_image" accept="image/png, image/jpeg, image/jpg"  placeholder="Enter username">
                        </div>
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
