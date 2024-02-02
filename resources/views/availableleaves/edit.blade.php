@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row" style="min-height: 70vh;">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h3 class="mb-0 card-title">Edit Available Leave</h3>
        </div>
        <div class="col-lg-12" style="background-color: #fff;">
          @if ($errors->any())
          <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input. <br>
            <br>
            <ul>
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif

          <form action="{{ route('availableleaves.update', ['id' => $availableleaves->staff_id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Employee Name</label>
                  <select class="form-control" name="staff_id" id="staff_id" disabled>
                    <option value="" selected>Choose Employee Name</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->staff_id }}" {{ $employee->staff_id == $availableleaves->staff_id ? 'selected' : '' }}>
                      {{ $employee->staff_name }}
                    </option>
                    @endforeach
                  </select>
                  <input type="hidden" name="staff_id" value="{{ $availableleaves->staff_id }}">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label branch" id="branchLabel">Package Amount Value*</label>
                  <input type="number" class="form-control" name="total_leaves" id="total_leaves" pattern="[0-9]*"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    value="{{ $availableleaves->total_leaves }}" placeholder="Package Amount Value">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="form-label">Remarks</label>
                  <textarea class="form-control" name="remark"
                    placeholder="Reason For Leave">{{ $availableleaves->remark ?? old('remark') }}</textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <center>
                    <button type="submit" class="btn btn-raised btn-primary">
                      <i class="fa fa-check-square-o"></i> Update
                    </button>
                    <button type="reset" class="btn btn-raised btn-success"> Reset </button>
                    <a class="btn btn-danger" href="{{ route('availableleaves.index') }}">Cancel</a>
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
<!-- Your JavaScript scripts go here -->
@endsection
``
