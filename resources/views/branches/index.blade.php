@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Branch Search</h3>
            </div>
           <form action="{{ route('branches') }}" method="GET">

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="pat-code">Branch Code:</label>
                            <input type="text" id="branch-code" name="branch_code" class="form-control" value="{{ request('branch_code') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="pat-name">Branch Name:</label>
                            <input type="text" id="branch-name" name="branch_name" class="form-control" value="{{ request('branch_name') }}">
                        </div>
                       
                        
                        <div class="col-md-4 d-flex align-items-end">
                            <div>
                                <button type="submit" class="btn btn-secondary"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                                <a class="btn btn-secondary ml-2" href="{{ route('branches') }}"><i class="fa fa-times" aria-hidden="true"></i> Reset</a>
                            </div>
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
                  <p></p>
               </div>
               @endif

         
            <div class="card-header">
                <h3 class="card-title">List Branches</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('branches.create') }}" class="btn btn-block btn-info">
                    <i class="fa fa-plus"></i>
                    Create Branch
                </a>
                
               
                
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Branch Code</th>
                                    <th class="wd-15p">Branch Name</th>
                                    <th class="wd-15p">Contact Number</th>
                                    <th class="wd-15p">Branch Admin Name</th>
                                    <th class="wd-15p"> Admin Contact Number</th>
                                    <th class="wd-20p">Status</th>
                                    <th class="wd-15p">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i = 0;
                                @endphp
                                @foreach($branches as $branch)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $branch->branch_code }}</td>
                                    <td>{{ $branch->branch_name }}</td>
                                    <td>{{ $branch->branch_contact_number }}</td>
                                    <td>{{ $branch->branch_admin_name }}</td>
                                    <td>{{ $branch->branch_admin_contact_number }}</td>
                                    <td>
                                        <form action="{{ route('branches.changeStatus', $branch->branch_id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                            <button type="submit"
                                                onclick="return confirm('Do you want to change status?');"
                                                class="btn btn-sm @if($branch->is_active == 0) btn-danger @else btn-success @endif">
                                                @if($branch->is_active == 0)
                                                InActive
                                                @else
                                                Active
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary edit-btn"
                                            href="{{ route('branches.edit', $branch->branch_id) }}"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a>
                                                  <a class="btn btn-secondary view-btn" href="{{ route('branches.show',$branch->branch_id) }}">
                                                <i class="fa fa-eye" aria-hidden="true"></i> View </a>
                                        <form style="display: inline-block"
                                            action="{{ route('branches.destroy', $branch->branch_id) }}" method="post">
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



