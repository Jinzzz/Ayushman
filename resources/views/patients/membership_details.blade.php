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
                              <h5 class="mb-0">Basic Details</h5>
                           </div>

                           <div class="wideget-user-img ml-auto mb-4">
                              <a href="{{ URL::previous() }}" class="btn btn-secondary">Back</a>
                           </div>
                        </div>

                        <div class="card-body">
                           <div class="row">
                              <div class="col-md-6">
                                 <!-- Left Div - terms_condition -->
                                 <div id="terms_condition" class="custom-margin">
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
                                       <input type="date" class="form-control" required name="start_date" placeholder="Start Date">
                                    </div>
                                    <div class="form-group">
                                       <label for="payment-type" class="form-label">Payment Mode</label>
                                       <select class="form-control" required name="payment_mode" placeholder="Payment Mode" id="payment_mode" onchange="updateDepositTo()">
                                          <option value="">--Select--</option>
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
                              </div>

                              <div class="col-md-6">
                                 <!-- Right Div - discount_amount -->
                                 <!-- <div id="discount_amount" class="custom-margin">
                                    <div class="card card-ash-border">
                                       <div class="card-body">
                                       </div>
                                    </div>
                                 </div> -->
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
            </div>
            <div class="border-top">
               <div class="wideget-user-tab">
                  <div class="tab-menu-heading">
                     <div class="tabs-menu1">
                        <ul class="nav">
                           <li class=""><a href="#tab-51" class="active show" data-toggle="tab">Membership History</a></li>
                           {{-- <li><a href="#tab-61" data-toggle="tab" class="">Included benefits</a></li> --}}
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

                           <div class="container">
                              <div id="patient-membership-history" class="row">
                                 @foreach($patientMemberships as $membership)
                                 <div class="col-lg-6 mb-3">
                                    <div class="card">
                                       <div class="card-body">
                                          <h4 class="card-title">Patient Membership History</h4>
                                          <p class="card-text">
                                             <strong>Membership Package: </strong>{{ $membership->membershipPackage->package_title }}<br>
                                             <strong>Start Date: </strong> {{ $membership->start_date }}<br>
                                             <strong>Payment Type: </strong> {{ $membership->payment_type == 1 ? 'Cash' : 'Liquid'}}<br>
                                             <strong>Payment Amount: </strong> {{ $membership->payment_amount }}<br>
                                             <strong>Expiry Date: </strong> {{ $membership->membership_expiry_date }}<br>
                                             <strong>Status: </strong>
                                             {{ $membership->is_active == 0 ? 'Inactive' : 'Active' }}
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                                 @endforeach
                              </div>
                           </div>
                        </div>
                     </div>
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
   $(document).on('change', '#payment_mode', function() {
      // Get the selected value
      alert(1);
      var selectedPaymentMode = $(this).val();
      // alert(selectedPaymentMode);
      // Make an AJAX request to fetch the ledger names based on the selected payment mode
      $.ajax({
         url: '{{ route("getLedgerNames") }}',
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
   });
</script>
@endsection