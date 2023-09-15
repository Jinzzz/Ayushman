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
                <h3 class="card-title">List Therapies</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('therapy.create') }}" class="btn btn-block btn-info">
                    <i class="fa fa-plus"></i>
                    Create Therapy
                </a>
                
               
                
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Therapy Name</th>
                                    <th class="wd-20p">Therapy Cost </th>
                                    <th class="wd-15p">Remarks</th>
                                    <th class="wd-15p">Status</th>
                                    <th class="wd-15p">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i = 0;
                                @endphp
                                @foreach($therapies as $therapy)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $therapy->therapy_name }}</td>
                                    <td>{{ $therapy->therapy_cost }}</td>
                                    <td>{{ $therapy->remarks}}</td>
                                    <td>
                                         <form action="{{ route('therapy.changeStatus', $therapy->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                            <button type="submit"
                                                onclick="return confirm('Do you want to Change status?');"
                                                class="btn btn-sm @if($therapy->is_active == 0) btn-danger @else btn-success @endif">
                                                @if($therapy->is_active == 0)
                                                InActive
                                                @else
                                                Active
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                       
                                    <td>
                                        <a class="btn btn-primary btn-sm edit-custom"
                                            href="{{ route('therapy.edit', $therapy->id) }}"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a>
                                        <form style="display: inline-block"
                                            action="{{ route('therapy.destroy', $therapy->id) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" onclick="return confirm('Do you want to delete it?');" class="btn-danger btn-sm"><i class="fa fa-trash"
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



