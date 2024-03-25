@extends('layouts.app')
@section('content')
<style>
    .flot-r{
        margin-bottom: 25px;
    float: right;
}
    }
</style>
<div class="row" id="user-profile">
<div class="col-lg-12">
   <div class="card">
      <div class="card-body">
         <div class="wideget-user">
            <div class="row">
               <div class="col-lg-12 col-md-12">
                   <a class="btn btn-secondary ml-2 flot-r" href="{{ route('patients.index') }}"><i class="" aria-hidden="true"></i>Back</a>
                  <div class="wideget-user-desc d-sm-flex">
                     <div class="wideget-user-img">
                        <img class="user-pic" src="{{ asset('assets/images/avatar.png') }}" alt="img">
                     </div>
                        <div class="row">
                            <div class="col">
                                <div class="user-wrap">
                                    <h4><strong>{{ $show->patient_name }}</strong></h4>
                                    @if($show->is_active == 0)
                                        <span class="badge badge-danger">Inactive</span>
                                    @else
                                        <span class="badge badge-success">Active</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col">
                                <div class="user-wrap">
                                    @if($show->has_credit == 0)
                                        <span class="badge badge-danger">Credit Inactive</span>
                                    @else
                                        <span class="badge badge-success">Credit Active</span>
                                    @endif
                                </div>
                            </div>
                        </div>


                     
                  </div>
                  
               </div>
            </div>
         </div>
      </div>
      <div class="border-top">
         <div class="wideget-user-tab">
            <div class="tab-menu-heading">
               <div class="tabs-menu1">
                  <ul class="nav">
                     <li class=""><a href="#tab-51" class="active show" data-toggle="tab">Personal Details</a></li>
                     <li><a href="#tab-61" data-toggle="tab" class="">Booking Details</a></li>
                     <li><a href="#tab-71" data-toggle="tab" class="">Invoice Details</a></li>
                      <li><a href="#tab-81" data-toggle="tab" class="">Family Members</a></li>
                  </ul>
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
                        <h5><strong>Patient Information</strong></h5>
                     </div>
                     <div class="table-responsive ">
                        <table class="table row table-borderless">
                           <tbody class="col-lg-12 col-xl-6 p-0">
                              <tr>
                                 <td><strong>Patient Name:</strong>{{$show->patient_name ??''}}</td>
                              </tr>
                              <tr>
                                 <td><strong>Patient Email:</strong>{{ $show->patient_email}}</td>
                              </tr>
                              <tr>
                                 <td><strong>Patient Mobile :</strong> {{ $show->patient_mobile}}</td>
                              </tr>
                              <tr>
                                 <td><strong>Patient Address:</strong> {{ $show->patient_address ??''}}</td>
                              </tr>

                              <tr>
                                 <td><strong>Date Of Birth :</strong> {{ $show->patient_dob}}</td>
                              </tr>
             
                              <tr>
                                 <td><strong>Emergency Contact Person:</strong> {{ $show->emergency_contact_person ??''}}</td>
                              </tr>
                              <tr>
                                 <td><strong>Emergency Contact :</strong> {{ $show->emergency_contact ??''}}</td>
                              </tr>
                              <tr>
                                 <td><strong>Patient Registration Type:</strong> {{ $show->patient_registration_type ??''}}</td>
                              </tr>

                              <tr>
                                 <td><strong>Whatsapp Number:</strong> {{ $show->whatsapp_number ??''}}</td>
                              </tr>
                           </tbody>

                        </table>
                     </div>
                     
                  </div>
               </div>
               <div class="tab-pane" id="tab-61">
                  <div class="media-heading">
                     <h5><strong>Booking Details</strong></h5>
                  </div>
                        <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th class="wd-15p">SL.NO</th>
                            <th class="wd-15p">Booking Reference Number</th>
                            <th class="wd-20p">Doctor Name</th>
                            <th class="wd-15p">Booking Date</th>
                            <th class="wd-15p">Booking Type</th>
                            <th class="wd-15p">Booking Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach($consultationDetails as $consultationDetail)
                        <tr id="dataRow_{{$consultationDetail->id }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ $consultationDetail->booking_reference_number }}</td>
                            <td>{{ $consultationDetail->staff_name }}</td>
                            <td>{{ $consultationDetail->booking_date  }}</td>
                            <td>{{ $consultationDetail->booking_type_value }}</td>
                            <td>{{ $consultationDetail->booking_status_value  }}</td>
       
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
                  
                    
                  </ul>
               </div>
               <div class="tab-pane" id="tab-71">
                  <div class="media-heading">
                     <h5><strong>Booking Invoice Details</strong></h5>
                  </div>
                  <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th class="wd-15p">SL.NO</th>
                            <th class="wd-15p">Booking Reference Number</th>
                            <th class="wd-20p">Invoice Date</th>
                            <th class="wd-15p">Booking Date</th>
                            <th class="wd-15p">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach($invoices as $invoice)
                        <tr id="dataRow_{{$invoice->id }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ $invoice->booking_invoice_number }}</td>
                            <td>{{ $invoice->invoice_date }}</td>
                            <td>{{ $invoice->booking_date  }}</td>
                            <td>{{ $invoice->paid_amount }}</td>
                  
       
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
                 
                  </div>
                      <div class="tab-pane" id="tab-81">
                  <div class="media-heading">
                     <h5><strong>Family Members</strong></h5>
                  </div>
                        <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th class="wd-15p">SL.NO</th>
                            <th class="wd-15p">Member Name</th>
                            <th class="wd-20p">Mobile Number</th>
                            <th class="wd-15p">Email</th>
                            <th class="wd-15p">Gender</th>
                            <th class="wd-15p">Relationship</th>
                            <th class="wd-15p">Blood Group</th>
                             <th class="wd-15p">Date of Birth</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach($show->familyMembers as $member)
                        <tr id="dataRow_{{$member->id }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ @$member->family_member_name }}</td>
                            <td>{{ @$member->mobile_number }}</td>
                            <td>{{ @$member->email_address  }}</td>
                            <td>{{ @$member->gender->master_value  }}</td>
                            <td>{{ @$member->relationship->master_value  }}</td>
                             <td>{{ @$member->bloodGroup->master_value  }}</td>
                            <td>{{ \Carbon\Carbon::parse($member->date_of_birth)->format('d-M-Y') }}
</td>
       
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
                  
                    
                  </ul>
               </div>
               </div>
            </div>
         </div>
      </div>
   </div>
@endsection
