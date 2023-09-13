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
                <h3 class="card-title">List Suppliers</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('supplier.create') }}" class="btn btn-block btn-info">
                    <i class="fa fa-plus"></i>
                    Create Supplier
                </a>
                
               
                
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">supplier Name</th>
                                    <th class="wd-15p">supplier Contact</th>
                                    <th class="wd-15p">supplier Email</th>
                                    <th class="wd-15p">supplier Address</th>
                                    <th class="wd-15p">GSTNO</th>
                                    <th class="wd-15p">Remarks</th>
                                    <th class="wd-15p">Status</th>
                                    <th class="wd-15p">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i = 0;
                                @endphp
                                @foreach($suppliers as $supplier)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $supplier->supplier_name }}</td>
                                    <td>{{ $supplier->supplier_contact }}</td>
                                    <td>{{ $supplier->supplier_email }}</td>
                                    <td>{{ $supplier->supplier_address }}</td>
                                    <td>{{ $supplier->gstno }}</td>
                                    <td>{{ $supplier->remarks}}</td>
                                    <td>
                                       <form action="{{ route('supplier.changeStatus', $supplier->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                            <button type="submit"
                                                onclick="return confirm('Do you want to Change status?');"
                                                class="btn btn-sm @if($supplier->is_active == 0) btn-danger @else btn-success @endif">
                                                @if($supplier->is_active == 0)
                                                InActive
                                                @else
                                                Active
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                       
                                    <td>
                                        <a class="btn btn-secondary"
                                            href="{{ route('supplier.edit', $supplier->id) }}"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a>
                                        <form style="display: inline-block"
                                            action="{{ route('supplier.destroy', $supplier->id) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit"onclick="return confirm('Do you want to delete it?');" class="btn btn-danger"><i class="fa fa-trash"
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



