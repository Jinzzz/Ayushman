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
</style>
<!-- ROW-1 OPEN -->
<form action="{{ route('patientsMembership.store',['id' => $id ]) }}" method="POST" enctype="multipart/form-data">
   @csrf
   <div class="row" id="user-profile">
      <div class="col-lg-12">
         <div class="card">
            <div class="card-body">
               <div class="wideget-user">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="wideget-user-desc d-sm-flex">
                           <div class="wideget-user-img">
                              <h5 class="mb-0">Membership Assigning</h5>
                           </div>
                           <div class="wideget-user-img ml-auto mb-4">
                              <a href="{{ URL::previous() }}" class="btn btn-secondary">Back</a>
                           </div>
                        </div>
                        <!-- <hr> -->
                        <div class="card-body">
                           <div class="row" style="align-items: flex-end;">
                              <div class="col-md-6">
                                 <!-- Left Div -->
                                 <div class="form-group">
                                    <label for="branch_id" class="form-label">Select Membership</label>
                                    <select id="membership" class="form-control" name="membership_id">
                                       <option value="">Choose Membership</option>
                                       @foreach($memberships as $membershipId => $packageTitle)
                                       <option value="{{ $membershipId }}">
                                          {{ $packageTitle }}
                                       </option>
                                       @endforeach
                                    </select>
                                 </div>
                                 <div class="form-group">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" required name="start_date"
                                       placeholder="Start Date">
                                 </div>
                                 <div class="form-group">
                                    <label for="payment-type" class="form-label">Payment Type</label>
                                    <select class="form-control" id="payment_mode" required name="payment_type"
                                       placeholder="Payment Type">
                                       <option value="">Choose Payment Type</option>
                                       @foreach($paymentType as $id => $value)
                                       <option value="{{ $id }}">{{ $value }}</option>
                                       @endforeach
                                    </select>
                                 </div>
                                 <div class="form-group">
                                       <label class="form-label">Deposit To</label>
                                       <select class="form-control" name="deposit_to" id="deposit_to">
                                          <option value="">Deposit To</option>
                                       </select>
                                    </div>
                                    <div class="form-group">
                                       <label class="form-label">Reference No.</label>
                                       <input type="text" class="form-control" required name="reference_no" placeholder="Reference No">

                                    </div>

                              </div>
                              <!-- Right side content -->
                              <div class="col-md-6">
                                 <button type="button" class="btn btn-primary" style="margin-bottom: 1rem;"
                                    id="viewDetails" onclick="loadWellness()">View Details</button>

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
               <div class="row col-md-12">
                  <div class="col-md-3">
                     <button type="submit" class="btn btn-raised btn-primary" style="margin: 0 24px;">
                        <i class="fa fa-check-square-o"></i> Add Membership
                     </button>
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
   <div class="tab-pane" id="tab-71">
      {{-- <div class="row">
         <div class="col-lg-3 col-md-6">
            <img class="img-fluid rounded mb-5" src="./assets/images/media/8.jpg " alt="banner image">
         </div>
         <!-- Other tab content goes here -->
      </div> --}}
   </div>
   <!-- Add more tab content as needed -->
   </div>
   </div>
   </div>
   </div>
   </div>
   <!-- COL-END -->
   </div>
</form>
<!-- ROW-1 CLOSED -->
@endsection
@section('js')
<!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


<script>
   function loadWellness() {
      // alert("tets");
      var membershipId = document.getElementById("membership").value;
      //alert(x);
      // var membershipId = $("#membership").val();

      if (membershipId) {
         // Now you have the selected membership ID in the 'membershipId' variable
         console.log("Selected Membership ID:", membershipId);
         $.ajax({
            type: 'GET',
            url: '/get-wellness-details/' + membershipId,
            success: function (response) {
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
                  //alert(element.wellness_name);
                  // const divEl = `<p>${element.wellness_name} ( ${ element.wellness_duration}) - ${element.maximum_usage_limit} times </p>`

                  const divEl = `<p>${element.wellness_name} - ${element.maximum_usage_limit} times </p>`
                  wellnessData.append(divEl);
               });

               const benefitEl = `
   				<span>${response.benefits.title}</span>
   				`

               benefits.append(benefitEl);
               console.log(response.package_details.package_duration);
               const packageEl = `
   				<h2 class="package-title"><b>${response.package_details.package_title}</b> </h2>
   								<ul class="package-info">
   									<li><strong>Validity </strong>${response.package_details.package_duration} days</li>
   									<li><strong>$ </strong> ${response.package_details.package_price} </li>
   								</ul>   				
   				`

               packageDetails.append(packageEl);


            },
            error: function (error) {
               console.error(error);
            }
         });
      } else {
         // Handle the case when no package is selected
         $("#wellness-details").html('');
      }
   }
   $(document).on('change', '#payment_mode', function () {
      // Get the selected value
      var selectedPaymentMode = $(this).val();
      // alert(selectedPaymentMode);
      // Make an AJAX request to fetch the ledger names based on the selected payment mode
      $.ajax({
         url: '{{ route("getLedgerNames") }}',
         type: 'GET',
         data: {
            payment_mode: selectedPaymentMode
         },
         success: function (data) {
            // Clear existing options
            $('#deposit_to').empty();

            // Add default option
            $('#deposit_to').append('<option value="">Deposit To</option>');

            // Add options based on the response
            $.each(data, function (key, value) {
               $('#deposit_to').append('<option value="' + key + '">' + value + '</option>');
            });
         },
         error: function (error) {
            console.log(error);
         }
      });
   });
</script>
@endsection