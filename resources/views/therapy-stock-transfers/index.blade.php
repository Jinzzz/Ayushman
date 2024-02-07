@extends('layouts.app')
 @section('content')
 <div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Therapy Stock Transfer</h3>
            </div>
            <form action="{{ route('therapy-stock-transfers.index') }}" method="GET">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                          <div class="form-group">
                            <label for="medicine_name" class="form-label">Medicine Name</label>
                            <select class="form-control" name="medicine_name" id="medicine_name">
                            <option value="" disabled selected>Choose Medicine</option> 
                            @foreach($medicines as $medicine) 
                            <option value="{{ $medicine->medicine_name }}">{{ $medicine->medicine_name }}</option>
                             @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label for="therapy_name" class="form-label">Therapy Name</label>
                            <select class="form-control" name="therapy_name" id="therapy_name">
                            <option value="" disabled selected>Choose Therapy</option>
                             @foreach($therapys as $therapy)
                              <option value="{{ $therapy->therapy_name }}">{{ $therapy->therapy_name }}</option>
                               @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label for="transfer_date" class="form-label">Transfer Date</label>
                            <input type="date" id="transfer_date" name="transfer_date" class="form-control" value="{{ request('transfer_date') }}">
                          </div>
                        </div>
                    
                    <div class="col-md-12">
                      <div class="form-group">
                        <center>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-filter" aria-hidden="true"></i> Filter
                        </button>
                        &nbsp;
                        <a class="btn btn-primary" href="{{ route('therapy-stock-transfers.index') }}">
                            <i class="fa fa-times" aria-hidden="true"></i> Reset
                        </a>
                        <center>
                      </div>
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
    <p>{{$message}}</p>
  </div>
   @endif
    @if ($message = Session::get('error'))
     <div class="alert alert-danger">
    <p>{{$message}}</p>
  </div> @endif <div class="card-header">
    <h3 class="card-title">List Therapy Stock</h3>
  </div>
  <div class="card-body">
    <a href="{{ route('therapy-stock-transfers.create') }}" class="btn btn-block btn-info">
      <i class="fa fa-plus"></i> Add New Therapy Stock </a>
    <div class="table-responsive">
      <table id="example" class="table table-striped table-bordered text-nowrap w-100 leave_request_table">
        <thead>
          <tr>
            <th class="wd-15p">SL.NO</th>
            <th class="wd-10p">Medicine Name</th>
            <th class="wd-10p">Therapy</th>
            <th class="wd-10p">Branch Code</th>
            <th class="wd-10p">Transfer Quantity</th>
            <th class="wd-15p">Transfer Date</th>
          </tr>
        </thead>
        <tbody> @php $i = 0;
           @endphp
           @foreach($stocks as $stock)
                        <tr id="dataRow_{{$stock->id }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ $stock->medicine_name }}</td>
                            <td>{{ $stock->therapy_name }}</td>
                            <td>{{ $stock->batch_id }}</td>
                            <td>{{ $stock->transfer_quantity }}</td>
                            <td>{{ $stock->transfer_date}}</td>
                        </tr>
                        @endforeach

        </tbody>
      </table>
    </div>
    <!-- TABLE WRAPPER -->
  </div>
  <!-- SECTION WRAPPER -->
</div>
</div></div>
<!-- ROW-1 CLOSED -->
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


