@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Apply Leave</h3>
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
               <form action="{{route('doctor.leave.submit')}}" method="POST" enctype="multipart/form-data">

                  @csrf
                  <div class="row">
                  <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Leave Type</label>
                           <select class="form-control" required name="leave_type">
                           <option value="">Choose Leave Type</option>
                           @foreach($leave_types as $leave_type)
                           <option value="{{$leave_type->id}}">{{$leave_type->leave_type_name}}</option>

                           @endforeach
                           </select>
                           
                     </div>
                     </div>
                      <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Duration</label>
                           <select class="form-control" required name="leave_duration">
                           <option value="">Choose Duration</option>
                            <option value="1">Full</option>
                            <option value="2">First Half</option>
                            <option value="3">Second Half</option>
                            <!--<option value="4">Multiple</option>-->

                          
                           </select>
                           
                     </div>
                     </div>
                       <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Date(*)</label>
                           <input type="date" class="form-control leave_dates" required name="leave_date"  placeholder="Enter Date" value="" min="{{ date('Y-m-d') }}">
                           
                     </div>
                     </div>
                       <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Reason(*)</label>
                           <textarea class="form-control" required name="leave_reason"  placeholder="Enter Reason"></textarea>
                        </div>
                     </div>
                    
                     
                    
                   
                     
                      
                     
                     
                     </div>


                   
                     <!-- ... -->


  <!-- ... -->
                      

                                


<!-- ... -->

                  


                        <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Submit Leave</button>
                          
                           <a class="btn btn-danger" href="{{route('doctor.leave.history')}}">Cancel</a>
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
    $(function() {
      $(".leave_dates").datepicker({
        minDate: 0 // Restricts selection to current date onwards
      });
    });
  </script>


@endsection
