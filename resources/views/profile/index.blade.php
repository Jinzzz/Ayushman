@extends('layouts.app')
@section('content')
<!-- ROW-1 OPEN -->
<div class="row" id="user-profile">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="wideget-user">
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="wideget-user-desc d-sm-flex">
                                <div class="wideget-user-img">
                                    <img height=100px; width=100px; src="{{asset('assets/images/avatar.png')}}" alt="img">   
                                </div>
                                <div class="user-wrap">
                                    @if (Auth::user()->user_type_id != 1)
                                    <h4 class="mb-3">{{Auth::user()->staff['staff_name']}} - ({{Auth::user()->staff['staff_code']}})</h4>
                                    @else
                                    <h4 class="mb-3">{{Auth::user()->username}}</h4>
                                    @endif
                                    <h6 class="text-muted mb-3">{{Auth::user()->email}}</h6>
                                    <h6 class="text-muted mb-3">Last Login: {{Auth::user()->last_login_time}}</h6>
                                </div>
                            </div>
                        </div>
                        <!--<div class="col-lg-6 col-md-12">-->
                        <!--    <div class="wideget-user-info">-->
                        <!--        <a href="" style="float:right;" class="btn btn-primary mt-1 mb-1"><i class="fa fa-edit"></i> Edit Profile</a>-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>
                </div>
            </div>
            <div class="border-top">
                <div class="wideget-user-tab">
                    <div class="tab-menu-heading">
                        <div class="tabs-menu1">
                            <ul class="nav">
                                <li class=""><a href="#tab-51" class="active show" data-toggle="tab">Profile</a></li>
                            </ul>
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
                                    <h5><strong>Basic Information</strong></h5>
                                </div>
                                <div class="table-responsive ">
                                    <table class="table row table-borderless">
                                        <tbody class="col-lg-12 col-xl-6 p-0">
                                            <tr>
                                                <td><strong>Name : </strong> @if (Auth::user()->user_type_id != 1) {{Auth::user()->staff['staff_name']}} @else Administrator @endif</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email : </strong> {{Auth::user()->email}}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Last Login : </strong> {{Auth::user()->last_login_time}}</td>
                                            </tr>
                                            @if (Auth::user()->user_type_id != 1)
                                            <tr>
                                                <td><strong>Employment Type : </strong>{{Auth::user()->staff->employmemntType['master_value']}} </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Gender : </strong>{{Auth::user()->staff->Gender['master_value']}} </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Qualification : </strong>{{Auth::user()->staff['staff_qualification']}} </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Experience : </strong>{{Auth::user()->staff['staff_work_experience']}} </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                        <tbody class="col-lg-12 col-xl-6 p-0">
                                            <tr>
                                                <td><strong>Username : </strong>{{Auth::user()->username}} </td>
                                            </tr>
                                            @if (Auth::user()->user_type_id != 1)
                                            <tr>
                                                <td><strong>Contact : </strong>{{Auth::user()->staff['staff_contact_number']}} </td>
                                            </tr>
                                            @endif
                                             <tr>
                                                <td><strong>Profile Created Date : </strong>{{Auth::user()->created_at}} </td>
                                            </tr>
                                            @if (Auth::user()->user_type_id != 1)
                                            <tr>
                                                <td><strong>Account Type : </strong>{{Auth::user()->staff->staffType['master_value']}} </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Branch : </strong>{{Auth::user()->staff->branch['branch_name']}} </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Specialization : </strong>{{Auth::user()->staff['staff_specialization']}} </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Date of Join : </strong>{{Auth::user()->staff['date_of_join']}} </td>
                                            </tr>
                                            

                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection