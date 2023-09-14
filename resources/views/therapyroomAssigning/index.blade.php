@extends('layouts.app')

@section('content')
<div class="row">
    <div class="card">

        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{$message}}</p>
        </div>
        @endif
        @if ($message = Session::get('error'))
        <div class="alert alert-danger">
            <p></p>
        </div>
        @endif
        <div class="card-header">
            <h3 class="card-title"> {{$pageTitle}}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('therapyroomassigning.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="branch_id" name="branch_id" value="{{$branch_id}}">
                <input type="hidden" id="therapy_room_id" name="therapy_room_id" value="{{$id}}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Staff*</label>
                            <select class="form-control" name="assigned_staff_id" id="assigned_staff_id">
                                <option value="">Select staff</option>
                                @foreach($staffs as $staff)
                                <option value="{{ $staff->staff_id }}">{{ $staff->staff_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <center>
                            <button type="submit" class="btn btn-raised btn-primary">
                                <i class="fa fa-check-square-o"></i> Add
                            </button>
                            <button type="reset" class="btn btn-raised btn-success">
                                Reset
                            </button>
                            <a href="{{ route('therapyrooms.index') }}" class="btn btn-secondary">
                                Therapy Rooms
                            </a>

                        </center>
                    </div>
            </form>
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th class="wd-15p">SL.NO</th>
                            <th class="wd-20p">Therapy Room</th>
                            <th class="wd-20p">Branch</th>
                            <th class="wd-15p">Staff Name</th>
                            <th class="wd-15p">Status</th>
                            <th class="wd-15p">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach($roomAssigning as $assign)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $assign->therapyroomName->room_name}}</td>
                            <td>{{ $assign->branch->branch_name}}</td>
                            <td>{{ $assign->staff->staff_name}}</td>
                            <td>
                                <form action="{{ route('therapyroomassigning.changeStatus', $assign->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm @if($assign->is_active == 0) btn-danger @else btn-success @endif">
                                        @if($assign->is_active == 0)
                                        InActive
                                        @else
                                        Active
                                        @endif
                                    </button>
                                </form>
                            </td>

                            <td>
                                <!-- <a class="btn btn-secondary" href="{{ route('therapyroomassigning.edit', $assign->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a> -->
                                <form style="display: inline-block" action="{{ route('therapyroomassigning.destroy', $assign->id) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" onclick="return confirm('Do you want to delete it?');" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i>Delete</button>
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