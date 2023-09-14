@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Search Therapy Room</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('therapyrooms.index') }}" method="GET">
                      <div class="col-md-6">
                            <div class="form-group">
                                <label for="branch_id">Branch</label>
                                <select class="form-control" name="branch_id" id="branch_id">
                                    <option value="">Choose Branch</option>
                                    @foreach($branch as $id => $branchName)
                                    <option value="{{ $id }}"{{ old('branch_id') == $id ? 'selected' : '' }}>
                                        {{ $branchName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                   
                    
                       
                        <div class="col-md-3 d-flex align-items-end">
                            <div>
                                <button type="submit" class="btn btn-secondary"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                                <a class="btn btn-secondary ml-2" href="{{ route('therapyrooms.index') }}"><i class="fa fa-times" aria-hidden="true"></i> Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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
               </div>
               @endif
            <div class="card-header">
                <h3 class="card-title">{{$pageTitle}}</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('therapyrooms.create') }}" class="btn btn-block btn-info">
                    <i class="fa fa-plus"></i>
                    Create Therapy Room
                </a>
                
               
                
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Branch</th>
                                    <th class="wd-20p">Room Name</th>
                                    <!-- <th class="wd-20p">Room Type</th>
                                    <th class="wd-20p">Room Capacity</th> -->
                                    <th class="wd-20p">Room Assigning</th>
                                    <th class="wd-15p">Status</th>
                                    <th class="wd-15p">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i = 0;
                                @endphp
                                @foreach($therapyrooms as $therapyroom)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $therapyroom->branch->branch_name }}</td>
                                    <td>{{ $therapyroom->room_name }}</td>
                                    <!-- <td>{{ $therapyroom->roomType->master_value}}</td>
                                    <td>{{ $therapyroom->room_capacity }}</td> -->

                                    <td>
                                        <a class="btn btn-sm  btn-outline-success "
                                            href="{{ route('therapyroomassigning.index', $therapyroom->id) }}"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i>RoomAssigning</a>
                                                </td>
                                    <td>
                                         <form action="{{ route('therapyrooms.changeStatus', $therapyroom->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                            <button type="submit"
                                                onclick="return confirm('Do you want to Change status?');"
                                                class="btn btn-sm @if($therapyroom->is_active == 0) btn-danger @else btn-success @endif">
                                                @if($therapyroom->is_active == 0)
                                                InActive
                                                @else
                                                Active
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                       
                                    <td>
                                        <a class="btn btn-secondary"
                                            href="{{ route('therapyrooms.edit', $therapyroom->id) }}"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a>
                                        <form style="display: inline-block"
                                            action="{{ route('therapyrooms.destroy', $therapyroom->id) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" onclick="return confirm('Do you want to delete it?');" class="btn btn-danger"><i class="fa fa-trash"
                                                    aria-hidden="true"></i>Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
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



