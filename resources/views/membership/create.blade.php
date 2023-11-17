@extends('layouts.app')
@section('content')
<div class="container">
   <link rel="stylesheet" type="text/css" href="{{ asset('plugins/date-picker/spectrum.css') }}">
   <script src="{{ asset('js/form-elements.js') }}"></script>

   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
               <p>{{$message}}</p>
            </div>
            @endif
            @if ($messages = Session::get('error'))
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

            <div class="card-header">
               <h3 class="mb-0 card-title">Create Membership Packages</h3>
            </div>
            <div class="col-lg-12" style="background-color:#fff">
               <form action="{{ route('membership.store') }}" method="POST" enctype="multipart/form-data">
                  <input type="hidden" id="checking_benefits" value="{{ isset($benefits) ? 1 : 0 }}">
                  @csrf
                  @if(isset($membership)) @method('PUT') @endif
                  <div class="col-md-12">
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="form-label">Membership Package Name*</label>
                              <input type="text" class="form-control" required name="membership_package_name" value="{{ old('membership_package_name') }}" placeholder="Membership Package Name">
                           </div>
                        </div>

                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="form-label">Membership Package Duration(Days)*</label>
                              <input type="number" min="0" class="form-control" required name="membership_package_duration" value="{{ old('package_duration') }}" placeholder="Membership Package Duration(Days)">
                           </div>
                        </div>

                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="form-label">Regular Price*</label>
                              <input type="number" id="regularPrice" min="0" class="form-control" required name="membership_package_price" value="{{ old('package_price') }}" placeholder="Regular Price">
                           </div>
                        </div>

                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="form-label">Offer Price</label>
                              <input type="number" id="offerPrice" min="0" class="form-control" name="discount_price" value="{{ old('package_discount_price') }}" placeholder="Offer Price" oninput="validatePrices()">
                              <span id="priceError" style="color: red;"></span>
                           </div>
                        </div>

                        <div class="col-md-11">
                           <div class="form-group">
                              <label class="form-label">Membership Package Description</label>
                              <textarea class="form-control ckeditor" id="benefitsDescription" name="membership_package_description" placeholder="Membership Package Description">{{ old('package_description') }}</textarea>
                           </div>
                        </div>

                        <div class="row wizard-title" style="margin-left:0;margin-right:0;">
                           <h6 class="mb-0 card-title" style="margin-left:15px;">Include Wellness</h6>
                        </div>
                        <div class="col-md-12">
                           <div class="container" id="include_wellness" style="padding-left:0;padding-right:0;"></div>
                        </div>

                        <div class="col-md-12">
                           <h6 class="mb-0 card-title" style="margin-left:15px;">Package Benefits*</h6><br>
                           <div class="form-group">
                              <textarea class="form-control ckeditor" required id="benefitsEditor" name="benefits" placeholder="Membership Package Benefits">{{ old('package_description') }}</textarea>
                           </div>
                        </div>

                        <div class="col-md-1">
                           <div class="form-group">
                              <div class="form-label">Status</div>
                              <label class="custom-switch">
                                 <input type="hidden" name="membership_package_active" value="1">
                                 <!-- Hidden field for false value -->
                                 <input type="checkbox" id="membership_package_active" value="1" name="membership_package_active" onchange="toggleStatus(this)" class="custom-switch-input" checked>
                                 <span id="statusLabel" class="custom-switch-indicator"></span>
                                 <span id="statusText" class="custom-switch-description">Active</span>
                              </label>
                           </div>
                        </div>

                        <div class="col-md-12">
                           <div class="form-group">
                              <center>
                                 <button type="submit" class="btn btn-raised btn-primary btn-small-margin"><i class="fa fa-check-square-o"></i> Add</button>
                                 <button type="reset" id="reset" class="btn btn-raised btn-success btn-small-margin">Reset</button>
                                 <a class="btn btn-danger btn-small-margin" href="{{ route('membership.index') }}">Cancel</a>
                              </center>
                           </div>
                        </div>

                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection


@section('js')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js"></script>

<script type="text/javascript">
   function validatePrices() {
      var regularPrice = parseFloat(document.getElementById('regularPrice').value);
      var offerPrice = parseFloat(document.getElementById('offerPrice').value);
      var priceError = document.getElementById('priceError');

      if (offerPrice >= regularPrice) {
         priceError.textContent = 'Offer Price must be less than Regular Price';
      } else {
         priceError.textContent = '';
      }
   }
   $(document).ready(function() {

      CKEDITOR.replace('benefitsDescription', {
         removePlugins: 'image',
         toolbar: [{
               name: 'basicstyles',
               items: ['Bold', 'Italic', 'Underline']
            },
            {
               name: 'paragraph',
               items: ['BulletedList']
            },
         ]
      });

      // Initialize the color pickers



      CKEDITOR.replace('benefitsEditor', {
         toolbar: []
      });



      $('#reset').click(function() {
         CKEDITOR.instances.benefitsEditor.setData('');
         CKEDITOR.instances.benefitsDescription.setData('');
      });

      // Initial render of wellness form
      renderForm();

      // Add Wellness button click event
      $('#add-wellness').click(function() {
         renderForm();
      });

      // Click event for adding/removing wellness rows
      $(document).on('click', '.add-btn', function() {
         var $includeWellness = $('#include_wellness');
         $includeWellness.find('.add-btn').html("<span class='glyphicon glyphicon-minus' style='color:green;'></span>")
            .addClass("remove-btn")
            .removeClass("add-btn");
         renderForm();
      });

      $(document).on('click', '.remove-btn', function() {
         $(this).closest('.row').remove();
      });

      // Initial render of package benefits form
      benefitsForm();

      // Add Package Benefits button click event
      $(document).on('click', '.add-benefits-btn', function() {
         var $packageBenefits = $('#package_benefits');
         $packageBenefits.find('.add-benefits-btn').html("<span class='glyphicon glyphicon-minus' style='color:green;'></span>")
            .addClass("remove-benefits-btn")
            .removeClass("add-benefits-btn");
         benefitsForm();
      });

      // Remove Package Benefits row
      $(document).on('click', '.remove-benefits-btn', function() {
         $(this).closest('.row').remove();
      });
   });

   // Render wellness form fields
   function renderForm() {
      var data = '';
      data += '<div class="row">';
      data += '<div class="col-md-6 col-sm-offset-1">';
      data += '<div class="form-group label-floating">';
      data += '<label class="form-label">Select wellness*</label>';
      data += '<select required name="wellness_id[]" class="form-control" required>';
      data += '<option disabled selected value="">Select wellness</option>'; // Add the first option
      @foreach($wellnesses as $wellness)
      data += '<option value="{{ $wellness->wellness_id }}" data-duration="{{ $wellness->wellness_duration >= 60 ? ($wellness->wellness_duration / 60) . " hour" : $wellness->wellness_duration . " minutes" }}" data-cost="{{" ₹ ". $wellness->wellness_cost }}">{{ $wellness->wellness_name }}</option>';
      @endforeach
      data += '</select>';
      data += "<label class='form-label'>Duration: <span class='selected_duration'></span>, Cost: <span class='selected_cost'></span></label>";
      data += '</div></div>';
      data += "<div class='col-md-5'><div class='form-group label-floating'><label class='form-label'>Max limit*</label><input min='1' required type='number' name='max_limit[]' class='form-control dob' required></div></div>";
      data += "<div style='align-self:center;' class='col-md-1 remove_field add-btn'><a href='javascript:void(0);'><span class='glyphicon glyphicon-plus' style='color:green;'></span></a></div>";
      data += "</div>";

      $("#include_wellness").append(data);

      // Add an event listener to update the duration and cost labels when a wellness option is selected
      $("select[name='wellness_id[]']").on('change', function() {
         var selectedOption = $(this).find(':selected');
         var duration = selectedOption.data('duration');
         var cost = selectedOption.data('cost');
         $(this).closest('.row').find('.selected_duration').text(duration);
         $(this).closest('.row').find('.selected_cost').text(cost);
      });
   }

   function toggleStatus(checkbox) {
      if (checkbox.checked) {
         $("#statusText").text('Active');
         $('[name="membership_package_active"]').val(1);
      } else {
         $("#statusText").text('Inactive');
         $('[name="membership_package_active"]').val(0);
      }
   }

   // Rest of your JavaScript code...
</script>
@endsection