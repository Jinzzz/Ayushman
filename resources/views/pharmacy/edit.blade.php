@extends('layouts.app')

@section('content')
<div class="container">
    <style>
        .no-updation {
            display: none;
        }
    </style>
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Edit Pharmacy</h3>
                </div>
                <div class="col-lg-12" style="background-color: #fff;">
                    @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                        <p>{{$message}}</p>
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
                    <form action="{{route('pharmacy.update',['id'=>$pharmacy->id])}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Pharmacy Name*</label>
                                    <input type="text" class="form-control" required name="pharmacy_name" value="{{$pharmacy->pharmacy_name}}" placeholder="Pharmacy Name">
                                </div>
                            </div>
                            <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label branch" id="branchLabel">Branch*</label>
                                <select class="form-control" name="branch" id="branch_field">
                      <option value="">Choose Branch</option>
                       @foreach($branchs as $branch)
                        <option value="{{ $branch->branch_id }}" {{ $pharmacy->branch == $branch->branch_id ? 'selected' : '' }}>
                        {{ $branch->branch_name }}
                      </option> @endforeach
                    </select>
                            </div>
                        </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-label">Status*</div>
                                    <label class="custom-switch">
                                        <input type="hidden" name="status" value="{{ $pharmacy->status }}">

                                        <input type="checkbox" id="status" name="status" onchange="toggleStatus(this)" class="custom-switch-input" @if($pharmacy->status == 1) checked @endif>
                                        <span id="statusLabel" class="custom-switch-indicator"></span>
                                        <span id="statusText" class="custom-switch-description">
                                            @if($pharmacy->status == 1)
                                            Active
                                            @else
                                            Inactive
                                            @endif
                                        </span>
                                    </label>
                                </div>
                            </div>


                        </div>

                        <div class="form-group">
                            <center>
                                <button type="submit" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Update</button>
                                <a class="btn btn-danger" href="{{ route('pharmacy.index') }}">Cancel</a>
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
<script>
    function toggleStatus(checkbox) {
        if (checkbox.checked) {
            $("#statusText").text('Active');
            $("input[name=status]").val(1);
        } else {
            $("#statusText").text('Inactive');
            $("input[name=status]").val(0);
        }
    }
</script>
@endsection