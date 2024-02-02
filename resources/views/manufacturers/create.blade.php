@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
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
               <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
            </div>
            <!-- Success message -->
            <div class="col-lg-12" style="background-color:#fff">
               <form action="{{ route('manufacturer.store') }}" method="POST" id="addFm" enctype="multipart/form-data">
                  <input type="hidden" name="hidden_id" value="{{ isset($manufacturer->manufacturer_id) ? $manufacturer->manufacturer_id : '' }}">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Manufacturer*</label>
                           <input required type="text" class="form-control" name="manufacturer" value="{{ isset($manufacturer->name) ? $manufacturer->name : old('manufacturer') }}" placeholder="Manufacturer">
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                           <div class="form-label">Status</div>
                           <label class="custom-switch">
                              <input type="hidden" name="is_active" value="0"> <!-- Hidden field for false value -->
                              <input type="checkbox" id="is_active" name="is_active" value="1" onchange="toggleStatus(this)" class="custom-switch-input" {{ old('is_active', isset($manufacturer->is_active) ? $manufacturer->is_active : 1) ? 'checked' : '' }}>
                              <span id="statusLabel" class="custom-switch-indicator"></span>
                              <span id="statusText" class="custom-switch-description">
                                 {{ old('is_active', isset($manufacturer->is_active) ? ($manufacturer->is_active ? 'Active' : 'Inactive') : 'Active') }}
                              </span>
                           </label>
                        </div>
                     </div>


                  </div>
                  <div class="form-group">
                     <center>
                        <button type="submit" id="submitForm" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> {{ isset($manufacturer->manufacturer_id) ? 'Update' : 'Add' }}</button>
                        @if (!isset($manufacturer->manufacturer_id))
                        <button type="reset" class="btn btn-raised btn-success">Reset</button>
                        @endif
                        <a class="btn btn-danger" href="{{route('manufacturer.index')}}">Cancel</a>
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/latest/jquery.validate.min.js"></script>
<script>
   $(document).ready(function() {
      var validator = $("#addFm").validate({
         ignore: "",
         rules: {
            manufacturer: {
               required: true,
               maxlength: 255
            },
         },
         messages: {
            manufacturer: {
               required: 'Please enter manufacturer.',
               maxlength: 'Manufacturer must not exceed 255 characters.'
            },
         },
         errorPlacement: function(label, element) {
            label.addClass('text-danger');
            label.insertAfter(element.parent().children().last());
         },
         highlight: function(element, errorClass) {
            $(element).parent().addClass('has-error');
            $(element).addClass('form-control-danger');
         },
         unhighlight: function(element, errorClass, validClass) {
            $(element).parent().removeClass('has-error');
            $(element).removeClass('form-control-danger');
         }
      });

      $(document).on('click', '#submitForm', function() {
         if (validator.form()) {
            $('#addFm').submit();
         } else {
            flashMessage('w', 'Please fill all mandatory fields');
         }
      });

      function flashMessage(type, message) {
         // Implement or replace this function based on your needs
         console.log(type, message);
      }
   });
   // impliment jQuery Validation 
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