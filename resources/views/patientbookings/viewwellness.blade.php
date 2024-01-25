@extends('layouts.app')
@section('content')
<!-- ROW-1 OPEN -->

<div class="row" id="user-profile">
   <div class="col-lg-12">
      <div class="card">
         <div class="card-body">
            <div class="wideget-user">
               <div class="row">
                  <div class="col-lg-6 col-md-12">
                     <div class="widget-user-desc d-sm-flex">
                        <div class="widget-user-img">
                           <div class="media-heading">
                              <h4><strong>Booking Details</strong></h4>
                           </div>
                           <label>Patient Name: {{$consultations->patient_name}}</label><br>
                           <label>Patient Email: {{$consultations->patient_email}}</label><br>
                           <label>Patient Contact Number: {{$consultations->patient_mobile}}</label><br>
                           <label>Doctor Name: {{$consultations->staff_name}}</label><br>
                           <label>Booking Date: {{$consultations->booking_date}}</label><br>
                           <label>Consultation Date: {{$consultations->booking_date}}</label><br>
                           <label>Time Slot: {{$consultations->time_from}}</label> - <label>{{$consultations->time_to}}</label><br>
                           <label>Booking Status: {{$consultations->master_value}}</label>

                        </div>
                     </div>
                  </div>

               </div>


            </div>
         </div>

      </div>

   </div>
   <!-- COL-END -->
</div>

<!-- ROW-1 CLOSED -->
@endsection