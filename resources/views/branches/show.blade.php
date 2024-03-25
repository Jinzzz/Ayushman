@extends('layouts.app')

@section('content')
    <style>
        .btn:not(:disabled):not(.disabled) {
            padding: 5px 15px;
        }
    </style>
    <!-- ROW-1 OPEN -->
    <div class="row" id="user-profile">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="wideget-user">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="wideget-user-desc d-sm-flex">
                                    <div class="wideget-user-img">
                                        <img class="user-pic" src="{{ asset('assets/images/ayushman.jpg') }}"
                                            alt="img">
                                    </div>
                                    <div class="user-wrap">
                                        <h6><strong>Branch: {{ $show->branch_name }} ( {{ $show->branch_code }} )</strong>
                                        </h6>
                                        <h6><strong>General URL: <a href="{{ route('general.index', $show->branch_code) }}">{{asset('/general/index/'.$show->branch_code)}}</a></strong></h6>
                                        <h6><strong>Contact: {{ $show->branch_contact_number }}</strong></h6>
                                        @if ($show->is_active == 0)
                                            <span class="btn  btn-sm btn-danger">Inactive</span>
                                        @else
                                            <span class="btn  btn-sm btn-success">Active</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="border-0">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tab-51">
                                <div id="profile-log-switch">
                                    <div class="media-heading">
                                        <h5><strong>Branch Information</strong></h5>
                                    </div>
                                    <div class="table-responsive ">
                                        <table class="table row table-borderless">
                                            <tbody class="col-lg-12 col-xl-6 p-0">
                                                <tr>
                                                    <td><strong>Branch Code: </strong>{{ $show->branch_code }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Branch Name: </strong>{{ $show->branch_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Branch Address: </strong>{{ $show->branch_address }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Branch Contact Number:
                                                        </strong>{{ $show->branch_contact_number }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Branch Email: </strong>{{ $show->branch_email }}</td>
                                                </tr>
                                            </tbody>
                                            <tbody class="col-lg-12 col-xl-6 p-0">

                                                <tr>
                                                    <td><strong>Branch Admin Name: </strong>{{ $show->branch_admin_name }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Branch Admin Contact Number: </strong>
                                                        {{ $show->branch_admin_contact_number }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Latitude: </strong>{{ $show->latitude }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Longitude: </strong>{{ $show->longitude }}</td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                    <a class="btn btn-secondary ml-2" href="{{ route('branches') }}"><i class=""
                                            aria-hidden="true"></i>Back</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div><!-- COL-END -->
    </div>
    <!-- ROW-1 CLOSED -->
@endsection
