@extends('layouts.app')

@section('content')


<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Leave History</h3>
            </div>
            <div class="card-body">
                <form>
                 <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">Leave Type</label>
                                            
                        <select class="form-control"  name="leave_type_id">
                           <option value="">Choose Leave Type</option>
                           @foreach($leave_types as $leave_type)
                           <option  {{request()->input('leave_type_id') == @$leave_type->id ? 'selected':''}} value="{{$leave_type->id}}">{{$leave_type->leave_type_name}}</option>

                           @endforeach
                        </select>
                  </div>
               </div>
                <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">Status</label>
                      <select name="leave_status_id"  class="form-control" id="leave_status_id" >
                      <option value="">Choose status</option>
                      <option value="1" {{request()->input('leave_status_id') == "1" ? 'selected':''}}>Pending</option>
                      <option value="2" {{request()->input('leave_status_id') == "2" ? 'selected':''}}>Approved</option>
                      <option value="3" {{request()->input('leave_status_id') == "3" ? 'selected':''}}>Rejected</option>
                       
                       </select>
                  </div>
               </div>
                <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">From Date</label>
                     <input type="date"  class="form-control" name="from_date" id="from_date" value="{{@$dateFrom}}">
                           
                  </div>
               </div>

                <div class="col-md-6">
                  <div class="form-group">
                      <label class="form-label">To Date</label>
                     <input type="date"  class="form-control" name="to_date" id="to_date" value="{{@$dateTo}}">
                  </div>
               </div>
               <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           {{-- <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button> --}}
                          <a href="{{route('doctor.leave.history')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
                </div>
                </form>


          
             <a href="{{ route('doctor.leave.viewApplyLeave') }}" class="btn btn-block btn-info">
                    <i class="fa fa-plus"></i>
                    Apply Leave
                </a>
                
               
                
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Leave Date</th>
                                    <th class="wd-15p">Leave Type</th>
                                    <th class="wd-20p">Leave Status</th>
                                    <th class="wd-10p">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i = 0;
                                @endphp
                                @foreach($leaves as $leave)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ date('d-M-Y',strtotime($leave->leave_date)) }}</td>
                                    <td>{{ $leave->leave_type->leave_type_name}}@if($leave->leave_duration>1)-<small>@if($leave->leave_duration==2) First Half @endif @if($leave->leave_duration==3) Second Half @endif</small>@endif</td>
                            
                                    
                                    <td>
                                    @if($leave->leave_status==1)
                                     <button type="button" class="btn btn-info">{{(__('Pending'))}}</button>
                                    @endif
                                    @if($leave->leave_status==2)
                                     <button type="button" class="btn btn-success">{{(__('Approved'))}}</button>
                                    @endif
                                    @if($leave->leave_status==3)
                                     <button type="button" class="btn btn-danger">{{(__('Rejected'))}}</button>
                                    @endif
                                      {{--<form action="{{ route('user.changeStatus', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                            <button type="submit"
                                                onclick="return confirm('Do you want to Change status?');"
                                                class="btn btn-sm @if($user->is_active == 0) btn-danger @else btn-success @endif">
                                                @if($user->is_active == 0)
                                                InActive
                                                @else
                                                Active
                                                @endif
                                            </button>
                                        </form>--}}
                                    </td>
                                    <td>
                                    
                                        <a class="btn btn-secondary"
                                            data-toggle="modal" data-target="#modal{{$leave->leave_id}}"><i
                                                class="fa fa-eye" aria-hidden="true"></i> View </a>
                                       {{-- <form style="display: inline-block"
                                            action="{{ route('user.destroy', $user->id) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"
                                                    aria-hidden="true"></i>Delete</button>
                                        </form>--}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @foreach($leaves as $leave)
    <!-- Modal Trigger -->
    

    <!-- Modal -->
    <div class="modal fade" id="modal{{$leave->leave_id}}" tabindex="-1" role="dialog" aria-labelledby="modal{{$leave->leave_id}}Label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal{{$leave->leave_id}}Label">View Leave</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Modal Content -->
                    <p>Leave Date:{{date('d-M-Y',strtotime($leave->leave_date))}}</p><br>
                     <p>Leave Type:{{$leave->leave_type->leave_type_name}}@if($leave->leave_duration>1)-<small>@if($leave->leave_duration==2) First Half @endif @if($leave->leave_duration==3) Second Half @endif</small>@endif</p><br>
                     <p>Leave Status: @if($leave->leave_status==1)
                                     <button type="button" class="btn btn-info">{{(__('Pending'))}}</button>
                                    @endif
                                    @if($leave->leave_status==2)
                                     <button type="button" class="btn btn-success">{{(__('Approved'))}}</button>
                                    @endif
                                    @if($leave->leave_status==3)
                                     <button type="button" class="btn btn-danger">{{(__('Rejected'))}}</button>
                                    @endif</p><br>
                    <p>Reason:{{$leave->leave_reason}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

                    </div>
                
                <!-- TABLE WRAPPER -->
            </div>
            <!-- SECTION WRAPPER -->
        </div>
    </div>
</div>
<!-- ROW-1 CLOSED -->







@endsection



