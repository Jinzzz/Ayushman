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
                <label>Doctor Name:  {{$consultations->staff_name}}</label><br>
                <label>Booking Date: {{$consultations->booking_date}}</label><br>
                <label>Consultation Date: {{$consultations->booking_date}}</label><br>
                <label>Time Slot: {{$consultations->time_from}}</label> - <label>{{$consultations->time_to}}</label>


            </div>
        </div>
    </div>
</div>

         </div>
      </div>

   </div>
   <div class="card">
      <div class="card-body">
         <div class="border-0">
            <div class="tab-content">
               <div class="tab-pane active show" id="tab-51">
                  <div id="profile-log-switch">
                     <div class="media-heading">
                     <h4><strong>Consultation Details</strong></h4>
                     </div>
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
               <form action="{{ route('addmedicine.consultation',['id' => $consultations->consultation_id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" class="form-control"  required name="doctor_id" value ="{{$consultations->doctor_id }}">
                <input type="hidden" class="form-control"  required name="consultation_id" value ="{{$consultations->consultation_id }}">
                  <div class="row">

                     <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Medicine*</label>
                            <select class="form-control" required name="medicine" id="medicine">
                                <option value="" selected disabled> Medicine</option>
                                @foreach($medicines as $id => $value)
                                <option value="{{ $id }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Dosage*</label>
                           <input type="text" class="form-control"  required name="medicine_dosage"  placeholder="Medicine Dosage">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Duration*(In Days)</label>
                           <input type="text" class="form-control"  required name="duration"  placeholder="Duration">
                        </div>
                     </div>
    
                       <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Remarks</label>
                           <textarea name="remarks" class="form-control" name="remarks" placeholder="Medicine Remark"></textarea>
                        </div>
                     </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-12">
                       <!-- An empty column that spans the entire width -->
                    </div>
                 </div>
                 <div class="row mb-3">
                    <div class="col-md-12">
                       <!-- An empty column that spans the entire width -->
                    </div>
                 </div>
                  <!-- ... -->
                  <div class="form-group">
                     <center>
                        <button type="submit" class="btn btn-raised btn-primary">
                        <i class="fa fa-check"></i> Add Medicine</button>

                     </center>
                  </div>
            </div>
         </div>
         </form>
                  </div>
               </div>

         </div>
      </div>

      <div class="card">
      <div class="card-body">
      <div class="media-heading">
                    <h4><strong>Medicine Details</strong></h4>
                </div>
                <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100 leave_request_table">
                    <thead>
                        <tr>
                            <th class="wd-25p">SL.NO</th>
                            <th class="wd-25p">Medicine Name</th>
                            <th class="wd-25p">Medicine Dosage</th>
                            <th class="wd-25p">Duration</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach ($prescriptions as $prescription)
                        <tr id="dataRow_{{ $prescription->prescription_id }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ $prescription->medicine_name }}</td>
                            <td>{{ $prescription->medicine_dosage }}</td>
                            <td>{{ $prescription->duration }}</td>
                            
                        </tr>
                    @endforeach
                    </tbody>
                </table>
         </div>
      </div>

   </div>
   </div>
   <!-- COL-END -->
</div>

<!-- ROW-1 CLOSED -->
@endsection