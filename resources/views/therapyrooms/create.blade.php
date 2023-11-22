@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('status'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif

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

                    <form action="{{ route('therapyrooms.store') }}" id="addFm"  method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Branch*</label>
                                    <select class="form-control" name="branch" id="branch_id">
                                        <option value="">Choose Branch</option>
                                        @foreach($branch as $id => $branchName)
                                        <option value="{{ $id }}" {{ old('branch') == $id ? 'selected' : '' }}>{{ $branchName }}</option>
                                        @endforeach
                                    </select>
                                </div>

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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Room Name*</label>
                                    <input type="text" class="form-control" required name="room_name" value="{{old('room_name')}}" placeholder="Room Name">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <center>
                                <button type="submit" id="submitForm" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Add</button>
                                <button type="reset" class="btn btn-raised btn-success">
                                    Reset</button>
                                <a class="btn btn-danger" href="{{ route('therapyrooms.index') }}">Cancel</a>
                            </center>
                        </div>
                    </form>
                </div>
            </div>
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
            room_name: {
               required: true,
               maxlength: 50
            },
            branch: "required",
         },
         messages: {
            room_name: {
               required: 'Please enter room name.',
               maxlength: 'Room name must not exceed 50 characters.'
            },
            branch: {
               required: 'Please select a branch.',
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