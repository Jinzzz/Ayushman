@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">Edit Master Value</h3>
            </div>
            <div class="card-body">
               @if ($message = Session::get('status'))
               <div class="alert alert-success">
                  <p>wafa</p>
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
               <form action="{{route('mastervalues.update',['id'=>$mastervalue->id])}}" method="POST" enctype="multipart/form-data">
                 @csrf
                @method('PUT')
                  <div class="row">
                    <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Master Type*</label>
                                    <select class="form-control" name="master_type" id="master_id">
                                        <option value="">Select Master Type</option>
                                        @foreach($master as $masterId => $masterValue)
                                        <option value="{{ $masterId }}"{{ $masterId == $mastervalue->master_id ? ' selected' : '' }}>{{ $masterValue }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                             <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Group Type</label>
                                    <select class="form-control" name="group_id" id="group_id">
                                        <option value="">Select Group Type</option>
                                        @foreach($group as $masterId => $masterValue)
                                        <option value="{{ $masterId }}"{{ $masterId == $mastervalue->group_id ? ' selected' : '' }}>{{ $masterValue }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                      <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Master Value*</label>
                           <input type="text" class="form-control" required name="master_value"  value="{{ $mastervalue->master_value }}" placeholder="Master Value">
                        </div>
                     </div>

  <!-- ... -->
                      
<div class="col-md-6">
    <div class="form-group">
        <div class="form-label">Status</div>
        <label class="custom-switch">
            <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" @if($mastervalue->is_active) checked @endif>
            <span id="statusLabel" class="custom-switch-indicator"></span>
            <span id="statusText" class="custom-switch-description">
                @if($mastervalue->is_active)
                    Active
                @else
                    Inactive
                @endif
            </span>
        </label>
    </div>
</div>

<!-- ... -->

                  

<div class="col-md-12">
                        <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-info">Update</button>
                             <a class="btn btn-danger" href="{{ route('mastervalues.index') }}">Cancel</a>
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
