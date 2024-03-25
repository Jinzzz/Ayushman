@extends('layouts.app')
@section('content')
<style>
   .package-details {
      background-color: #f5f5f5;
      padding: 20px;
      border-radius: 10px;
   }

   .package-title {
      font-size: 18px;
      color: #333;
      margin-bottom: 10px;
   }

   .package-description {
      font-size: 14px;
      color: #666;
      margin-bottom: 15px;
   }

   .package-info {
      list-style-type: none;
      padding: 0;
      margin: 0;
   }

   .package-info li {
      font-size: 14px;
      color: #444;
      margin-bottom: 8px;
   }

   .bullet-list {
      list-style-type: disc;
   }

   #wellnessData {
      list-style-type: disc;
      margin: 0;
      padding: 0;
   }

   #wellnessData li {
      margin-bottom: 10px;
   }
</style>
<!-- ROW-1 OPEN -->
<form action="{{ route('patientsMembership.store',['id' => $id ]) }}" method="POST" enctype="multipart/form-data">
   @csrf
   <div class="row" id="user-profile">
      <div class="col-lg-12">
         <div class="card">
            @if ($message = Session::get('error'))
            <div class="alert alert-danger">
               <p>{{ $message }}</p>
            </div>
            @endif
            @if ($messages = Session::get('errors'))
            <div class="alert alert-danger">
               <ul>
                  @foreach (json_decode($messages, true) as $field => $errorMessages)
                  @foreach ($errorMessages as $errorMessage)
                  <li>{{$errorMessage}}</li>
                  @endforeach
                  @endforeach
               </ul>
            </div>
            @endif
            <div class="card-body">
               <div class="wideget-user">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="wideget-user-desc d-sm-flex">
                           <div class="wideget-user-img">
                              <h5 class="mb-0">Membership Assigning</h5>
                           </div>
                           <div class="wideget-user-img ml-auto mb-4">
                              <a href="{{ url('/patients/index')}}" class="btn btn-secondary">Back</a>
                           </div>
                        </div>
                        <!-- <hr> -->
                        <div class="card-body">
                           <div class="row" style="align-items: flex-start;">
                              <div class="col-md-6">
                                 <!-- Left Div -->
                                 <div class="form-group">
                                    <label for="branch_id" class="form-label">Select Membership*</label>
                                    <select id="membership" required class="form-control" value="{{ old('membership_id') }}" name="membership_id">
                                       <option value="">Choose Membership</option>
                                       @foreach($memberships as $membershipId => $packageTitle)
                                       <option value="{{ $membershipId }}">
                                          {{ $packageTitle }}
                                       </option>
                                       @endforeach
                                    </select>
                                 </div>
                                 <div class="form-group">
                                    <label class="form-label">Start Date*</label>
                                    <input type="date" class="form-control" required value="{{ old('start_date') }}" name="start_date" placeholder="Start Date">
                                 </div>
                                 <div class="form-group">
                                    <label for="payment-type" class="form-label">Payment Type*</label>
                                    <select class="form-control" id="payment_mode" required value="{{ old('payment_type_id') }}" name="payment_type_id" placeholder="Payment Type">
                                       <option value="">Choose Payment Type</option>
                                       @foreach($paymentType as $id => $value)
                                       <option value="{{ $id }}">{{ $value }}</option>
                                       @endforeach
                                    </select>
                                 </div>
                                 <div class="form-group">
                                    <label class="form-label">Deposit To*</label>
                                    <select class="form-control" required value="{{ old('deposit_to') }}" name="deposit_to" id="deposit_to">
                                       <option value="">Deposit To</option>
                                    </select>
                                 </div>
                                 <div class="form-group">
                                    <label class="form-label">Reference No.</label>
                                    <input type="text" class="form-control" value="{{ old('reference_no') }}" name="reference_no" placeholder="Reference No">
                                 </div>

                                 <div class="form-group">
                                    <center>
                                       <button type="submit" class="btn btn-raised btn-primary" style="margin: 0 24px;">
                                          <i class="fa fa-check-square-o"></i> Add Membership
                                       </button>
                                    </center>
                                 </div>

                              </div>
                              <!-- Right side content -->
                              <div class="col-md-6">
                                 <button type="button" class="btn btn-primary" style="margin-bottom: 1rem;margin-top: 37px;height: 38px;" id="viewDetails" onclick="loadWellness()">View Details</button>

                                 <div class="membership-more-details">
                                    <div>
                                       <h6>Basic Details</h6>
                                       <div class="card">
                                          <div class="card-body" id="packageDetails">
                                             <!-- Package details will be appended here -->
                                          </div>
                                       </div>
                                    </div>

                                    <div>
                                       <h6>Included Wellness</h6>
                                       <div class="card">
                                          <div class="card-body" id="wellness-details">
                                             <!-- Wellness details will be appended here -->
                                             <ul id="wellnessData"></ul>
                                          </div>

                                       </div>
                                    </div>

                                    <div>
                                       <h6>Benefits</h6>
                                       <div class="card">
                                          <div class="card-body" id="benefitDiv">
                                             <!-- Benefits will be appended here -->
                                          </div>
                                       </div>
                                    </div>
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
                           <li class=""><a href="#tab-51" class="active show" data-toggle="tab">Patient Membership History</a></li>
                           {{-- <li><a href="#tab-61" data-toggle="tab" class="">Included benefits</a></li> --}}
                        </ul>
                     </div>
                  </div>
               </div>
            </div>

         </div>
      </div>

   </div>

   <div id="patient-membership-history" class="row">
      <div class="col-lg-12 mb-3">
         <div class="card">
            <div class="card-body">
               <h4 class="card-title">Previous Memberships</h4>
               <table class="table table-bordered">
                  <thead>
                     <tr>
                        <th>Membership Package</th>
                        <th>Start Date</th>
                        <th>Expiry Date</th>
                        <th>Validity</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($patientMemberships as $membership)
                     <tr>
                        <td>{{ $membership->membershipPackage->package_title }}</td>
                        <td>{{ date('d-m-Y', strtotime($membership->start_date)) }}</td>
                        <td>{{ date('d-m-Y', strtotime($membership->membership_expiry_date)) }}</td>
                        <td>{{ $membership->membershipPackage->package_duration }} days</td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
   <div class="tab-pane" id="tab-61">
      <div id="profile-log-switch">
         <div class="media-heading">
            <div class="container">
               <div class="row">
                  <div class="col-lg-6 mb-3" id="benefitDiv">

                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</form>
<!-- ROW-1 CLOSED -->
@endsection
@section('js')
<!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
   $(document).ready(function() {
      $("#viewDetails").hide();
      $(".membership-more-details").hide();
      $("#viewDetails").click(function() {
         $(".membership-more-details").slideToggle();
         $(this).text(function(i, text) {
            return text === "View Details" ? "Hide Details" : "View Details";
         });
      });
   });

   function loadWellness() {
      // show btn 
      $("#viewDetails").show();
   }
   $(document).on('change', '#payment_mode', function() {
      // Get the selected value
      var selectedPaymentMode = $(this).val();
      // alert(selectedPaymentMode.length);
      // Make an AJAX request to fetch the ledger names based on the selected payment mode
      if (selectedPaymentMode.length > 0) {
         $.ajax({
            url: '{{ route("getLedgerNames1") }}',
            type: 'GET',
            data: {
               payment_mode: selectedPaymentMode
            },
            success: function(data) {
               // Clear existing options
               $('#deposit_to').empty();

               // Add default option
               $('#deposit_to').append('<option value="">Deposit To</option>');

               // Add options based on the response
               $.each(data, function(key, value) {
                  $('#deposit_to').append('<option value="' + key + '">' + value + '</option>');
               });
            },
            error: function(error) {
               console.log(error);
            }
         });
      } else {
         $('#deposit_to').empty();
         $('#deposit_to').append('<option value="">Deposit To</option>');
      }

   });
   $(document).on('change', '#membership', function() {
      // Get the selected value
      var membershipId = document.getElementById("membership").value;
      // alert(selectedPaymentMode.length);
      // Make an AJAX request to fetch the ledger names based on the selected payment mode
      if (membershipId.length > 0) {
         console.log("Selected Membership ID:", membershipId);
         $.ajax({
            type: 'GET',
            url: '/get-wellness-details/' + membershipId,
            success: function(response) {
               $("#viewDetails").show();
               // Update the wellness details container with the received data
               console.log(response)
               // $("#wellness-details").html(response);
               const wellnessData = $("#wellness-details");
               const benefits = $("#benefitDiv");
               const packageDetails = $("#packageDetails");
               wellnessData.empty();
               benefits.empty();
               packageDetails.empty();
               response.wellnessDetails.forEach(element => {
                  const durationInMinutes = element.wellness_duration;
                  const hours = Math.floor(durationInMinutes / 60);
                  const minutes = durationInMinutes % 60;

                  let durationFormatted = '';
                  if (hours > 0) {
                     durationFormatted += `${hours} hr${hours > 1 ? 's' : ''}`;
                  }
                  if (minutes > 0) {
                     if (durationFormatted !== '') {
                        durationFormatted += ' and ';
                     }
                     durationFormatted += `${minutes} min${minutes > 1 ? 's' : ''}`;
                  }

                  const listItemEl = `<li><strong>${element.wellness_name}</strong> (Dur: ${durationFormatted}, Lim: ${element.maximum_usage_limit} times)</li>`;
                  wellnessData.append(listItemEl);
               });


               const benefitEl = `
   				<span>${response.benefits.title}</span>
   				`

               benefits.append(benefitEl);
               console.log(response.package_details.package_duration);
               const packageEl = `
   				<h2 class="package-title"><b>${response.package_details.package_title}</b> </h2>
   								<ul class="package-info">
   									<li>Validity ${response.package_details.package_duration} days</li>
   									<li>₹${response.package_details.package_price} </li>
   								</ul>   				
   				`
               packageDetails.append(packageEl);
            },
            error: function(error) {
               console.error(error);
            }
         });
      } else {
         // hide button 
         $("#viewDetails").hide();
      }

   });
</script>
@endsection