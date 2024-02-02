@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Employee Available Leave</h3>
            </div>
            <form action="{{ route('availableleaves.index') }}" method="GET">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="employee_name" class="form-label">Employee Name</label>
                        <select class="form-control" name="staff_name" id="staff_name">
                            <option value="" disabled selected>Choose Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->staff_name }}" {{ old('staff_name') == $employee->staff_name ? 'selected' : '' }}>
                                    {{ $employee->staff_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-filter" aria-hidden="true"></i> Filter
                        </button>
                        <a class="btn btn-primary ml-2" href="{{ route('availableleaves.index') }}">
                            <i class="fa fa-times" aria-hidden="true"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="card">
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>
    @endif
    <div class="card-header">
        <h3 class="card-title">List Available Leave</h3>
    </div>
    <div class="card-body">
        <!-- <a href="{{ route('availableleaves.create') }}" class="btn btn-block btn-info">
            <i class="fa fa-plus"></i> Add Employee Leave
        </a> -->
        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th class="wd-15p">SL.NO</th>
                        <th class="wd-10p">Employee Name</th>
                        <th class="wd-10p">Total Leaves</th>
                        <!-- <th class="wd-15p">Action</th> -->
                    </tr>
                </thead>
                <tbody>
                    @php $i = 0;
                     @endphp
                     @foreach($availableleaves as $availableleave)
             <tr id="dataRow_{{$availableleave->id }}">
            <td>{{ ++$i }}</td>
            <td>{{ $availableleave->staff_name }}</td>
            <td>{{ $availableleave->total_leaves }}</td>
            <!-- <td>
              <a class="btn btn-secondary btn-sm" href="{{ route('availableleaves.show', $availableleave->id) }}">
                <i class="fa fa-eye" aria-hidden="true"></i> View </a>
              <a class="btn btn-primary btn-sm edit-custom" href="{{ route('availableleaves.edit', $availableleave->id) }}">
                <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a>
            </td> -->
          </tr> @endforeach 
                </tbody>
            </table>
        </div>
        <!-- TABLE WRAPPER -->
    </div>
    <!-- SECTION WRAPPER -->
</div>
</div>
</div>
<!-- ROW-1 CLOSED -->
@endsection
