@extends('layouts.app')
@section('content')
<div class="row">
   <div class="col-md-12 col-lg-12">
      <div class="card">
         <div class="card-header">
            <h3 class="card-title"><strong>Add Slots to {{@$staff->staff_name}}</strong></h3>
         </div>
         <form action="{{ route('timeslotStaff.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Day *</label>
                        <select class="form-control" required name="week_day" id="week_day">
                           <option value="">Select Day</option>
                           @foreach( $weekday as $masterId => $masterValue)
                           <option value="{{ $masterId }}">{{ $masterValue }}</option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Slot *</label>
                        <select class="form-control" required name="slot" id="slot">
                           <option value="">Select Slot</option>
                           @foreach( $slot as $key => $slots)
                           <option value="{{ $slots->id }}">{{ $slots->slot_name }} : {{$slots->time_from}} - {{$slots->time_to}}</option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label for="tokens" class="form-label">Max Tokens*</label>
                        <input type="text" id="tokens" required name="tokens" class="form-control" placeholder="Max Tokens">
                     </div>
                  </div>
               </div>
               <div class="col-md-2">
                  <div class="form-group">
                     <input type="hidden" class="form-control" required name="staff_id" value="{{$id}}">
                  </div>
               </div>
               <div>
                  <button type="submit" class="btn btn-primary">
                  <i class="fa fa-check-square-o"></i> Submit
                  </button>&nbsp;&nbsp;
                  <a class="btn btn-primary" href="{{ route('timeslot.index') }}">
                  <i class="fa fa-times" aria-hidden="true"></i> Reset
                  </a>
               </div>
            </div>
      </div>
      </form>
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
      </div>
      @endif
      <div class="card-header">
         <h3 class="card-title">List Timeslots</h3>
      </div>
      <div class="card-body">
         <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered text-nowrap w-100">
               <thead>
                  <tr>
                     <th class="wd-20p">SL.NO</th>
                     <th class="wd-15p">Day</th>
                     <th class="wd-15p">Slot</th>
                     <th class="wd-15p">Max Tokens</th>
                     <th class="wd-15p">Action</th>
                  </tr>
               </thead>
               <tbody>
                  @php
                  $i = 0;
                  @endphp
                  @foreach($timeslot as $slot)
                  <tr>
                     <td>{{ ++$i }}</td>
                     <td>{{ $slot->weekDay['master_value']}}</td>
                     <td>{{ $slot->timeSlot['slot_name']}}</td>
                     <td>{{ $slot->no_tokens}}</td>
                     <td>
                       
                        <form style="display: inline-block"
                           action="{{ route('timeslotStaff.destroy', $slot->id) }}" method="get">
                           @csrf
                           @method('delete')
                           <button type="submit" onclick="return confirm('Do you want to delete it?');"
                              class="btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i>Delete</button>
                        </form>
                     </td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
</div>
@endsection