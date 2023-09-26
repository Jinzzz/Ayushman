@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Create Tax</h3>
            </div>
            <div class="card-body">
               @if ($message = Session::get('status'))
               <div class="alert alert-success">
                  <p></p>
               </div>
               @endif
            </div>
            <div class="col-lg-12" style="background-color:#fff">
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
               <form action="{{ route('tax.group.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Name*</label>
                           <input type="text" class="form-control" required name="tax_group_name" value="{{ old('tax_group_name') }}" placeholder="Tax Group Name">
                        </div>
                     </div>
                  </div>
                  <h6><b>Include taxes*</b></h6>
                  <div class="row">
                     @foreach($taxes as $tax)
                     <div class="col-md-3">
                        <div class="form-check">
                           <input type="checkbox" class="form-check-input" name="included_tax[]" id="included_tax{{ $tax->id }}" value="{{ $tax->id }}">
                           <label class="form-check-label" for="included tax{{ $tax->id }}">{{ $tax->tax_name }}</label>
                        </div>
                     </div>
                     @if($loop->iteration % 4 == 0)
                  </div>
                  <div class="row">
                     @endif
                     @endforeach
                  </div>


                  <div class="form-group">
                     <center>
                        <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add
                        </button>
                        <button type="reset" class="btn btn-raised btn-success">
                           Reset
                        </button>
                        <a class="btn btn-danger" href="{{ route('tax.group.index') }}">Cancel</a>
                     </center>
                  </div>
               </form>


            </div>
         </div>
      </div>

      @endsection
      @section('js')
      <script>
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