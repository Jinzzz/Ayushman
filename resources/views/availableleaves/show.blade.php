@extends('layouts.app') @section('content') <div class="container">
  <div class="row" style="min-height: 70vh;">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h3 class="mb-0 card-title">View Employee Available Details</h3>
        </div>
        <div class="col-lg-12" style="background-color: #fff;"> @if ($errors->any()) <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input. <br>
            <br>
            <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
          </div> @endif
           <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Employee Name</label>
                <input type="text" class="form-control" name="staff_name" value="{{ $show->staff_name }}" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Amount/Hour</label>
                <input type="text" class="form-control" readonly name="total_leaves" value="{{ $show->total_leaves }}" readonly>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label">Remarks</label>
                <textarea class="form-control" name="remark" readonly>{{ $show->remark }}</textarea>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <center>
                  <a class="btn btn-danger" href="{{ route('availableleaves.index') }}">Back</a>
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
 @endsection