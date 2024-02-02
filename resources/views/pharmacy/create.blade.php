@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Create Pharmacy</h3>
            </div>
            <!-- Success message -->
            <div class="col-lg-12 card-background" style="background-color:#fff" ;>
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
               <form action="{{ route('pharmacy.store') }}" id="addFm" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Pharmacy Name*</label>
                           <input type="text" class="form-control" required name="pharmacy_name" value="{{old('pharmacy_name')}}" placeholder="Pharmacy Name">
                        </div>
                     </div>

                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Branch*</label>
                           <select class="form-control" name="branch" id="branch_id" required>
                              <option value="">Select Branch</option>
                              @foreach ($branch as $id => $branchName)
                              <option value="{{ $id }}">{{ $branchName }}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>

                     <div class="col-md-4">
                        <div class="form-group">
                           <div class="form-label">Status</div>
                           <label class="custom-switch">
                              <input type="checkbox" id="status" name="status" onchange="toggleStatus(this)" class="custom-switch-input" checked>
                              <span id="statusLabel" class="custom-switch-indicator"></span>
                              <span id="statusText" class="custom-switch-description">Active</span>
                           </label>
                        </div>
                     </div>
                  </div>
                  <!-- ... -->
                  <div class="form-group">
                     <center>
                        <button type="submit" id="submitForm" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                        <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                        <a class="btn btn-danger" href="{{route('pharmacy.index')}}">Cancel</a>
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
@endsection