@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Edit Supplier</h3>
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

              <form action="{{route('supplier.update',['id'=>$supplier->id])}}" method="POST" enctype="multipart/form-data">
                 @csrf
                @method('PUT')

                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Supplier Name</label>
                           <input type="text" class="form-control" required name="supplier_name" value="{{$supplier->supplier_name}}" placeholder="Supplier Name">
                        </div>
                     </div>

                      <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Supplier Contact</label>
                           <input type="text" class="form-control" required name="supplier_contact" value="{{$supplier->supplier_contact}}" placeholder="Supplier Contact">
                        </div>
                     </div>

                      <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Supplier Email</label>
                           <input type="email" class="form-control" required name="supplier_email" value="{{$supplier->supplier_email}}" placeholder="Supplier Email">
                        </div>
                     </div>

                       <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Supplier Address</label>
                           <input type="text" class="form-control" required name="supplier_address" value="{{$supplier->supplier_address}}" placeholder="Supplier Address">
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">GSTNO</label>
                           <input type="text" class="form-control" required name="gstno" value="{{$supplier->gstno}}" placeholder="GSTNO">
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Remarks</label>
                           <input type="text" class="form-control" required name="remarks" value="{{$supplier->remarks}}" placeholder="Remarks">
                        </div>
                     </div>
                   

                  

                      
<div class="col-md-6">
    <div class="form-group">
        <div class="form-label">Status</div>
        <label class="custom-switch">
            <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" @if($supplier->is_active) checked @endif>
            <span id="statusLabel" class="custom-switch-indicator"></span>
            <span id="statusText" class="custom-switch-description">
                @if($supplier->is_active)
                    Active
                @else
                    Inactive
                @endif
            </span>
        </label>
    </div>
</div>

<!-- ... -->

                  


                        <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{route('supplier.index')}}">Cancel</a>
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
