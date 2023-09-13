@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Edit Tax</h3>
            </div>
            <div class="card-body">
               @if ($message = Session::get('status'))
               <div class="alert alert-success">
                  <p></p>
               </div>
               @endif
            </div>
            <div class="col-lg-12">
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
               <form action="{{route('tax.update',['id'=>$tax->id])}}" method="POST" enctype="multipart/form-data">
                 @csrf
                @method('PUT')
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Tax Title*</label>
                           <input type="text" class="form-control" required name="tax_title" value="{{$tax->tax_title}}" placeholder="Tax Title">
                        </div>
                     </div>

                      <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Split Value 1*</label>
                           <input type="text" class="form-control" required name="split_value_1" value="{{$tax->split_value_1}}" placeholder="Split Value 1">
                        </div>
                     </div>

                       <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Split Value 2*</label>
                           <input type="text" class="form-control" required name="split_value_2" value="{{$tax->split_value_2}}" placeholder="Split Value 2">
                        </div>
                     </div>
                   

                  

                     
  <!-- ... -->
                      
<div class="col-md-6">
   <div class="form-group">
      <div class="form-label">Status</div>
      <label class="custom-switch">
         <input type="hidden" name="is_active" value="0"> <!-- Hidden field for false value -->
         <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" checked>
         <span id="statusLabel" class="custom-switch-indicator"></span>
         <span id="statusText" class="custom-switch-description">Active</span>
      </label>
   </div>
</div>
</div>



                  

                       <div class="col-md-12">
                        <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
                          
                           <a class="btn btn-danger" href="{{route('tax.index')}}">Cancel</a>
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
{ <script>
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
