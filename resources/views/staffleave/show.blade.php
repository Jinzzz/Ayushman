@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Show Leave Details</h3>
            </div>
            <div class="col-lg-12" style="background-color:#fff;">
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

               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Staff Name</label>
                        <input type="text" class="form-control" readonly name="staff_name" value="{{$show->staff_name}}" placeholder="Staff Name">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Branch Name</label>
                        <input type="text" class="form-control" readonly name="branch_name" value="{{$show->branch_name}}" placeholder="Branch Name">
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">From Date</label>
                        <input type="text" class="form-control" readonly name="from_date" value="{{$show->from_date}}" placeholder="From Date">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">To Date</label>
                        <input type="text" class="form-control" readonly name="to_date" value="{{$show->to_date}}" placeholder="To Date">
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Start Day</label>
                        <input type="text" class="form-control" readonly name="start_day" value="{{$show->start_day}}" placeholder="Start Day">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">End Day</label>
                        <input type="text" class="form-control" readonly name="end_day" value="{{$show->end_day}}" placeholder="End Day">
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Reason</label>
                        <input type="text" class="form-control" readonly name="reason" value="{{$show->reason}}" placeholder="Reason">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Leave Type</label>
                        <input type="text" class="form-control" readonly name="leave_type" value="{{$show->leave_type_name;}}" placeholder="Leave Type">
                     </div>
                  </div>
               </div>

               
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Total Days</label>
                        <input type="text" class="form-control" readonly name="total_days" value="{{$show->days}}" placeholder="No of Days">
                     </div>
                  </div>
               </div>


               <div class="col-md-12">
                  <div class="form-group">
                     <center>
                        <a class="btn btn-danger" href="{{route('staffleave.index')}}">Back</a>
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

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
   <script type="text/javascript">
      $(document).ready(function() {
         CKEDITOR.replace('medicalHistory', {
            removePlugins: 'image',

         });

         $(document).ready(function() {
            CKEDITOR.replace('currentMedication', {
               removePlugins: 'image',

            });

         });
      });

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