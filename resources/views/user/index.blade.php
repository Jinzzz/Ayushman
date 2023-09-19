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
                <h3 class="card-title">List Users</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('user.create') }}" class="btn btn-block btn-info">
                    <i class="fa fa-plus"></i>
                    Create User
                </a>
                
               
                
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Username</th>
                                    <th class="wd-15p">User Email</th>
                                    <th class="wd-15p">User Type</th>
                                    <th class="wd-15p">Branch</th>
                                    <th class="wd-15p">Last Login Time</th>
                                    <th class="wd-15p">Status</th>
                                    <th class="wd-15p">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i = 0;
                                @endphp
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->user_email }}</td>
                                    <td>{{ $user->userType->user_type}}</td>
                                    <td>{{ $user->branch->branch_name }}</td>
                                    <td>{{ $user->last_login_time }}</td>
                                    <td>
                                       <form action="{{ route('user.changeStatus', $user->id) }}" method="POST">
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
                                        </form>
                                    </td>
                                       
                                    <td>
                                        <a class="btn btn-secondary"
                                            href="{{ route('user.edit', $user->id) }}"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a>
                                        <form style="display: inline-block"
                                            action="{{ route('user.destroy', $user->id) }}" method="post">
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



