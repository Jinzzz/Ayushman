@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">View Wellness</h3>
            </div>
            <div class="col-lg-12" style="background-color: #fff;">
               @if ($errors->any())
               <div class="alert alert-danger">
                  <!-- <strong>Whoops!</strong> There were some problems with your input.<br><br> -->
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
                        <label class="form-label">Wellness Name</label>
                        <input type="text" class="form-control" readonly name="wellness_name" value="{{$show->wellness_name}}" placeholder="Wellness Name">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Wellness Description</label>
                        <textarea class="form-control" readonly name="wellness_description" placeholder="Wellness Description">{{ $show->wellness_description }}</textarea>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Regular Price</label>
                        <input type="text" class="form-control" readonly name="wellness_cost" value="{{$show->wellness_cost}}" placeholder="Wellness Cost">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Wellness Offer Price</label>
                        <input type="number" class="form-control" readonly required name="wellness_offer_price" value="{{$show->wellness_offer_price}}" placeholder="Wellness Offer Price">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Wellness Duration(Minutes)</label>
                        <input type="number" class="form-control" readonly required name="wellness_duration" value="{{$show->wellness_duration}}" placeholder="Wellness Duration(Minutes)">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group checkbox">
                        <label for="branch_id" class="form-label">Wellness Image</label>
                        <input type="file" class="form-control" readonly required name="wellness_image" value="{{$show->wellness_image}}" placeholder="Wellness Image">
                        @if($show->wellness_image)
                        <img src="{{url('/assets/uploads/wellness_image/'.$show->wellness_image)}}" alt="Wellness Image" style="max-width: 100px; max-height: 100px;">
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Remarks</label>
                        <textarea type="text" class="form-control" readonly name="remarks" value="{{$show->remarks}}" placeholder="Remarks">{{$show->remarks}}</textarea>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group checkbox">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select class="multi-select" multiple style="width: 100%;">
                           @foreach($branch as $id => $branchName)
                           <option disabled value="{{ $id }}" {{ in_array($id, $branch_ids->toArray()) ? 'selected' : '' }}>
                              {{ $branchName }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>

                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Wellness Inclusions</label>
                        <textarea class="form-control" name="wellness_inclusions" id="wellnessInclusion" readonly name="wellness_inclusions" placeholder="Wellness Inclusions">{{ $show->wellness_inclusions}}</textarea>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Wellness T&C</label>
                        <textarea class="form-control" name="wellness_terms_conditions" id="termsandCondition" readonly name="wellness_terms_conditions" placeholder="Wellness T&C">{{$show->wellness_terms_conditions}}</textarea>
                     </div>
                  </div>

                  <div class="col-md-6">
                     <div class="form-group">
                        <div class="form-label">Status</div>
                        <label class="custom-switch">
                           <input type="checkbox" disabled id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" @if($show->is_active) checked @endif>
                           <span id="statusLabel" class="custom-switch-indicator"></span>
                           <span id="statusText" class="custom-switch-description">
                              @if($show->is_active)
                              Active
                              @else
                              Inactive
                              @endif
                           </span>
                        </label>
                     </div>
                  </div>

                  <div class="col-md-12">
                     <div class="form-group">
                        <center>
                           <a class="btn btn-danger" href="{{route('wellness.index')}}">Back</a>
                        </center>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script type="text/javascript">
   $(document).ready(function() {
      CKEDITOR.replace('wellnessInclusion', {
         removePlugins: 'image',

      });

      $(document).ready(function() {
         CKEDITOR.replace('termsandCondition', {
            removePlugins: 'image',

         });

      });

      function toggleStatus(checkbox) {
         if (checkbox.checked) {
            $("#statusText").text('Active');
            $("input[name=is_active]").val(1);
         } else {
            $("#statusText").text('Inactive');
            $("input[name=is_active]").val(0);
         }

         $(document).ready(function() {
            $('select').selectpicker();
         });
      }
   });
</script>
@endsection