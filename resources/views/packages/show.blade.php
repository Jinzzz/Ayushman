@extends('layouts.app') @section('content') <div class="container">
  <div class="row" style="min-height: 70vh;">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h3 class="mb-0 card-title">View Package Details</h3>
        </div>
        <div class="col-lg-12" style="background-color: #fff;"> @if ($errors->any()) <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input. <br>
            <br>
            <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
          </div> @endif <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Package Name</label>
                <input type="text" class="form-control" readonly name="package_name" value="{{ $show->package_name }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Company Name</label>
                <input type="text" class="form-control" readonly name="company_name" value="{{ $show->company_name }}">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Salary Head</label>
                <input type="text" class="form-control" readonly name="salary_head_id" value="{{ $show->salary_head_name }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Salary Head Type</label>
                <input type="text" class="form-control" readonly name="salary_head_type_id" value="{{ $show->salary_head_type }}">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Package Amount Type</label>
                <input type="text" class="form-control" readonly name="package_amount_type" value="{{ $show->package_amount_type }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Amount/Hour</label>
                <input type="text" class="form-control" readonly name="package_amount_value" value="{{ $show->package_amount_value }}">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="custom-switch">
                  <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" readonly class="custom-switch-input" @if($show->status) checked @endif> <span id="statusLabel" class="custom-switch-indicator"></span>
                  <span id="statusText" class="custom-switch-description"> @if($show->status) Active @else Inactive @endif </span>
                </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label">Remarks</label>
                <textarea class="form-control" readonly name="remark">{{ $show->remark }}</textarea>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <center>
                  <a class="btn btn-danger" href="{{ route('packages.index') }}">Back</a>
                </center>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div> @endsection @section('js') <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script type="text/javascript">
  function toggleStatus(checkbox) {
    if (checkbox.checked) {
      $("#statusText").text('Active');
      $("input[name=status]").val(1); // Set the value to 1 when checked (Active)
    } else {
      $("#statusText").text('Inactive');
      $("input[name=status]").val(0); // Set the value to 0 when unchecked (Inactive)
    }
  }
</script> @endsection