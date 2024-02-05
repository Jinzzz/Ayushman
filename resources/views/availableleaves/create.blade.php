@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="mb-0 card-title">Create Available Leave</h3>
          </div>
          <div class="col-lg-12" style="background-color: #fff;"> 
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
            <form action="{{ route('availableleaves.store') }}" id="addFm" method="POST" enctype="multipart/form-data">
              @csrf 
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label branch">Package Name*</label>
                    <select class="form-control" name="staff_id" id="staff_id">
                      <option value="" disabled selected>Choose Employee</option>
                      @foreach($employees as $employee)
                        <option value="{{ $employee->staff_id }}" {{ old('staff_id') == $employee->staff_id ? 'selected' : '' }}>
                          {{ $employee->staff_name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="row"> 
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label branch">No Of Days*</label>
                    <input type="number" class="form-control" name="total_leaves" id="total_leaves"  pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="{{ old('total_leaves') }}" placeholder="Total Leaves">  
                  </div>
                </div> 
              </div>

              <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Remark*</label>
                                    <textarea class="form-control" name="remark" placeholder="Remark">{{ old('remark') }}</textarea>

                                </div>
                            </div>
                        </div>
             </br></br>
              <div class="form-group">
                <center>
                  <button type="submit" id="submitForm" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Add
                  </button>
                  <button type="reset" class="btn btn-raised btn-success"> Reset </button>
                  <a class="btn btn-danger" href="{{ route('availableleaves.index') }}">Cancel</a>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
        var validator; // Declare validator outside $(document).ready()

        $(document).ready(function () {
            validator = $("#addFm").validate({
                rules: {
                  staff_id: "required",
                  total_leaves: "required",
                  remark: "required",
                },
                messages: {
                    staff_id: "Please select employee.",
                    total_leaves: "Please enter total leaves.",
                    remark: "Please enter remark.",
                },
                submitHandler: function (form) {
                    // Your form submission logic here
                    form.submit();
                },
            });

            $(document).on('click', '#submitForm', function () {
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
    </script>
@endsection
