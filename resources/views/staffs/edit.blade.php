@extends('layouts.app')

@section('content')
<div class="container">
      <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Edit Staff</h3>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('status'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
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
                     <form action="{{route('staffs.update',['staff_id'=>$staffs->staff_id])}}" method="POST" enctype="multipart/form-data">
                 @csrf
                @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Staff Type</label>
                                    <select class="form-control" name="staff_type" id="staff_type">
                                        <option value="">Select Staff Type</option>
                                        @foreach($stafftype as $masterId => $masterValue)
                                        <option value="{{ $masterId }}"{{ $masterId == $staffs->staff_type ? ' selected' : '' }}>{{ $masterValue }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Employment Type</label>
                                    <select class="form-control" name="employment_type" id="employment_type">
                                        <option value="">Select Employment Type</option>
                                        @foreach($employmentType as $masterId => $masterValue)
                                        <option value="{{ $masterId }}"{{ $masterId == $staffs->employment_type ? ' selected' : '' }}>{{ $masterValue }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" required name="staff_username"  value="{{$staffs->staff_username}}" placeholder="Staff Username">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Password*</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" required name="password" placeholder="Password">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye" id="togglePassword"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Confirm Password*</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" required name="confirm_password" placeholder="Confirm Password">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye" id="toggleConfirmPassword"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Staff Name</label>
                                    <input type="text" class="form-control" required name="staff_name" value="{{$staffs->staff_name}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-control" name="gender" id="gender">
                                        <option value="">Choose Gender</option>
                                        @foreach($gender as $id => $gender)
                                        <option value="{{ $id }}"{{ $id == $staffs->gender ? ' selected' : '' }}>{{ $gender }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Branch</label>
                                    <select class="form-control" name="branch_id" id="branch_id">
                                        <option value="">Choose Branch</option>
                                        @foreach($branch as $id => $branchName)
                                        <option value="{{ $id }}"{{ $id == $staffs->branch_id ? ' selected' : '' }}>{{ $branchName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Date Of Birth</label>
                                    <input type="date" class="form-control" required name="date_of_birth" value="{{$staffs->date_of_birth}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" required name="staff_email" value="{{$staffs->staff_email}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" required name="staff_contact_number" value="{{$staffs->staff_contact_number}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" required name="staff_address" >{{$staffs->staff_address}}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Qualification</label>
                                    <select class="form-control" name="staff_qualification" id="staff_qualification">
                                        <option value="">Select Qualification</option>
                                        @foreach($qualification as $masterId => $masterValue)
                                        <option value="{{ $masterId }}"{{ $masterId == $staffs->staff_qualification ? ' selected' : '' }}>{{ $masterValue }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Work Experience</label>
                                    <input type="text" class="form-control" required name="staff_work_experience" value="{{$staffs->staff_work_experience}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Logon Type</label>
                                    <select class="form-control" name="staff_logon_type" id="staff_logon_type">
                                        <option value="">Select Logon Type</option>
                                        @foreach($stafflogonType as $masterId => $masterValue)
                                        <option value="{{ $masterId }}"{{ $masterId == $staffs->staff_logon_type ? ' selected' : '' }}>{{ $masterValue }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Commission Type</label>
                                    <select class="form-control" name="staff_commission_type" id="staff_commission_type">
                                        <option value="">Select Commission Type</option>
                                        @foreach($commissiontype as $masterId => $masterValue)
                                        <option value="{{ $masterId }}"{{ $masterId == $staffs->staff_commission_type ? ' selected' : '' }}>{{ $masterValue }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Staff Commission</label>
                                    <input type="text" class="form-control" required name="staff_commission" value="{{$staffs->staff_commission}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Booking Fee</label>
                                    <input type="text" class="form-control" required name="staff_booking_fee" value="{{$staffs->staff_booking_fee}}">
                                </div>
                            </div>
<div class="col-md-12">
    <div class="form-group">
        <div class="form-label">Status</div>
        <label class="custom-switch">
            <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" @if($staffs->is_active) checked @endif>
            <span id="statusLabel" class="custom-switch-indicator"></span>
            <span id="statusText" class="custom-switch-description">
                @if($staffs->is_active)
                    Active
                @else
                    Inactive
                @endif
            </span>
        </label>
    </div>
</div>
                     <div class="col-md-12">
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-raised btn-primary">
                                <i class="fa fa-check-square-o"></i> Update
                            </button>
                            <a class="btn btn-danger" href="{{ route('staffs.index') }}">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
