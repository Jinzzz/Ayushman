@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Staff Details</h3>
    
    <div class="show-container">
    
            <p><strong>Staff Code:</strong> {{$show->staff_code}}</p>
            <p><strong>Staff Type:</strong> {{ $show->staffType->master_value}}</p>
            <p><strong>Employment Type:</strong> {{ $show->staffType->master_value}}</p>
            <p><strong>Staff Username:</strong> {{ $show->staff_username}}</p>
            <p><strong>Staff Name:</strong> {{ $show->staff_name }}</p>
            <p><strong>Gender:</strong> {{ $show->Gender->master_value}}</p>
            <p><strong>Branch:</strong> {{ $show->branch->branch_name}}</p>
            <p><strong>DOB:</strong> {{\Carbon\Carbon::parse($show->date_of_birth)->format('d/m/Y')}}</p>
            <p><strong>Staff Email:</strong> {{ $show->staff_email}}</p>
            <p><strong>Staff Contact Number:</strong> {{ $show->staff_contact_number}}</p>
            <p><strong>Staff Address:</strong> {{ $show->staff_address}}</p>
            <p><strong>Qualification:</strong> {{ $show->qualification->master_value}}</p>
            <p><strong>Work Experience:</strong> {{ $show->staff_work_experience}}</p>
            <p><strong>Logon Type:</strong> {{ $show->stafflogonType->master_value}}</p>
            <p><strong>Commission Type:</strong> {{ $show->commissionType->master_value}}</p>
            <p><strong>Staff Commission:</strong> {{ $show->staff_commission}}</p>
            <p><strong>Booking Fee:</strong> {{ $show->staff_booking_fee}}</p>
            <p><strong>Last Login Time:</strong> {{ $show->last_login_time}}</p>
            


                      <a class="btn btn-secondary ml-2" href="{{ route('staffs.index') }}"><i class="fa fa-times" aria-hidden="true"></i>Back</a>
       
</div>

@endsection
