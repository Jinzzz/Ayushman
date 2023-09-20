@extends('layouts.app')

@section('content')
<style>
    .package-details {
        background-color: #f5f5f5;
        padding: 20px;
        border-radius: 10px;
    }

    .package-title {
        font-size: 18px;
        color: #333;
        margin-bottom: 10px;
    }

    .package-description {
        font-size: 14px;
        color: #666;
        margin-bottom: 15px;
    }

    .package-info {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .package-info li {
        font-size: 14px;
        color: #444;
        margin-bottom: 8px;
    }

    .bullet-list {
        list-style-type: disc;
    }
</style>
<!-- ROW-1 OPEN -->
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Membership Assigning</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('therapyrooms.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="membership_package_id">Select Membership Package</label>
                                <select class="form-control" name="membership_package_id" id="membership_package_id">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="branch_id">Starting date</label>
                                <input type="date" class="form-control" name="starting_date" id="starting_date">
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
                                <a class="btn btn-danger" href="{{ route('patients.index') }}">Cancel</a>
                            </center>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="row" id="user-profile">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="wideget-user">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="wideget-user-desc d-sm-flex">
                                <!-- Heading -->
                                <div class="wideget-user-img">
                                    <h5 class="mb-0">Basic Details</h5>
                                </div>
                                <!-- Back Button -->
                                <div class="wideget-user-img ml-auto mb-4">
                                    <a href="{{ URL::previous() }}" class="btn btn-secondary">Back</a>
                                </div>
                            </div>
                            <div class="package-details">
                                <h2 class="package-title">package_title </h2>
                                <ul class="package-info">
                                    <li><strong>Duration:</strong> package_duration days</li>
                                    <li><strong>Price:</strong> package_price </li>
                                    <li><strong>Status:</strong>
                                        @if(isset($package_details->is_active) && $package_details->is_active == 1)
                                        <span class="badge badge-success">Active</span>
                                        @else
                                        <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </li>
                                </ul>
                                <p class="package-description">{package_description}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="border-top">
                <div class="wideget-user-tab">
                    <div class="tab-menu-heading">
                        <div class="tabs-menu1">
                            <ul class="nav">
                            <li><a href="#tab-41" data-toggle="tab" class="">Basic details</a></li>
                                <li class=""><a href="#tab-51" class="active show" data-toggle="tab">Included wellnesses</a></li>
                                <li><a href="#tab-61" data-toggle="tab" class="">Included benefits</a></li>
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
                            <div id="profile-log-sw	itch">
                                <div class="media-heading">
                                    <h5><strong>Included wellnesses and their details</strong></h5>
                                </div>
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4 class="card-title"> wellness_name</h4>
                                                    <p class="card-text">
                                                        <strong>Duration:</strong> wellness_duration<br>
                                                        <strong>Maximum Usage Limit:</strong> maximum_usage_limit<br>
                                                        <strong>Wellness Inclusions:</strong> wellness_inclusions<br>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="tab-pane" id="tab-61">
                            <div id="profile-log-switch">
                                <div class="media-heading">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-6 mb-3">
                                                {title}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab-71">
                            <div class="row">
                                <div class="col-lg-3 col-md-6">
                                    <img class="img-fluid rounded mb-5" src="./assets/images/media/8.jpg " alt="banner image">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <img class="img-fluid rounded mb-5" src="./assets/images/media/10.jpg" alt="banner image ">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <img class="img-fluid rounded mb-5" src="./assets/images/media/11.jpg" alt="banner image ">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <img class="img-fluid rounded mb-5 " src="./assets/images/media/12.jpg" alt="banner image ">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <img class="img-fluid rounded mb-5" src="./assets/images/media/13.jpg " alt="banner image">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <img class="img-fluid rounded mb-5" src="./assets/images/media/14.jpg " alt="banner image">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <img class="img-fluid rounded mb-5" src="./assets/images/media/15.jpg " alt="banner image">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <img class="img-fluid rounded mb-0" src="./assets/images/media/16.jpg " alt="banner image">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <img class="img-fluid rounded mb-0" src="./assets/images/media/17.jpg " alt="banner image">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <img class="img-fluid rounded mb-0" src="./assets/images/media/18.jpg " alt="banner image">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <img class="img-fluid rounded mb-0" src="./assets/images/media/19.jpg " alt="banner image">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <img class="img-fluid rounded" src="./assets/images/media/20.jpg " alt="banner image">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-81">
                            <div class="row">
                                <div class=" col-lg-6 col-md-12">
                                    <div class="card borderover-flow-hidden">
                                        <div class="media card-body media-xs overflow-visible ">
                                            <img class="avatar brround avatar-md mr-3" src="./assets/images/users/18.jpg" alt="avatar-img">
                                            <div class="media-body valign-middle mt-2">
                                                <a href="" class=" font-weight-semibold text-dark">John Paige</a>
                                                <p class="text-muted ">johan@gmail.com</p>
                                            </div>
                                            <div class="media-body valign-middle text-right overflow-visible mt-2">
                                                <button class="btn btn-primary" type="button">Follow</button> </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-lg-6 col-md-12">
                                    <div class="card borderover-flow-hidden">
                                        <div class="media card-body media-xs overflow-visible ">
                                            <span class="avatar cover-image avatar-md brround bg-pink mr-3">LQ</span>
                                            <div class="media-body valign-middle mt-2">
                                                <a href="" class="font-weight-semibold text-dark">Lillian Quinn</a>
                                                <p class="text-muted">lilliangore</p>
                                            </div>
                                            <div class="media-body valign-middle text-right overflow-visible mt-2">
                                                <button class="btn btn-secondary" type="button">Follow</button> </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-lg-6 col-md-12">
                                    <div class="card borderover-flow-hidden mb-lg-0">
                                        <div class="media card-body media-xs overflow-visible ">
                                            <span class="avatar cover-image avatar-md brround mr-3">IH</span>
                                            <div class="media-body valign-middle mt-2">
                                                <a href="" class="font-weight-semibold text-dark">Irene Harris</a>
                                                <p class="text-muted">ireneharris@gmail.com</p>
                                            </div>
                                            <div class="media-body valign-middle text-right overflow-visible mt-2">
                                                <button class="btn btn-success" type="button">Follow</button> </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-lg-6 col-md-12">
                                    <div class="card borderover-flow-hidden mb-lg-0">
                                        <div class="media card-body media-xs overflow-visible ">
                                            <img class="avatar brround avatar-md mr-3" src="./assets/images/users/2.jpg" alt="avatar-img">
                                            <div class="media-body valign-middle mt-2">
                                                <a href="" class="text-dark font-weight-semibold">Harry Fisher</a>
                                                <p class="text-muted mb-0">harryuqt</p>
                                            </div>
                                            <div class="media-body valign-middle text-right overflow-visible mt-2">
                                                <button class="btn btn-danger" type="button">Follow</button> </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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