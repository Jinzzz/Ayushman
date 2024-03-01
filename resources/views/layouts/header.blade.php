<?php 
use App\Models\Mst_Pharmacy;
use App\Models\Mst_Staff;
$pharmacyList = Mst_Pharmacy::where('status','=',1)->orderBy('pharmacy_name','ASC')->get();
?>

<div class="page-header">
    <a aria-label="Hide Sidebar" class="app-sidebar__toggle close-toggle" data-toggle="sidebar"
        href="#"></a><!-- sidebar-toggle-->

    <div>
        <h1 class="page-title"></h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ isset($pageTitle) ? $pageTitle : 'Default Text' }}
            </li>
        </ol>
    </div>
   
    <div class="d-flex  ml-auto header-right-icons header-search-icon">
        @if (Auth::user()->user_type_id == 1)
        @if(Session::has('pharmacy_id'))
            {{-- <div class="card overflow-hidden"> --}}
                <div class="card-header" style="background-color: #0d97c673;border-bottom: 1px solid #eaedf100;">
                    <h3 class="card-title" style="font-size: 12px;
                    color: #0d97c6;">PHARMACY: {{ Session::get('pharmacy_name') }} &nbsp;</h3>
                    <div class="card-options">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#pharmacyModal">Change</button>
                    </div>
                </div>
            {{-- </div> --}}
    @endif
    {{-- pharmacy selection --}}
<div class="modal fade" id="pharmacyModal" tabindex="-1" role="dialog" aria-labelledby="pharmacyModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">PHARMACY</h5>
			</div>
			<form id="pharmacyForm" action="{{ route('save-default-pharmacy') }}" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="form-label">Choose Pharmacy*</label>
							<select class="form-control" required name="pharmacy_id" id="pharmacy_id">
								<option value="">--Select Pharmacy--</option>
                                <option value="all">All Pharmacies</option>
								@foreach ($pharmacyList as $id => $List)
									<option value="{{ $List->id }}">{{ $List->pharmacy_name }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary">Save changes</button>
			</div>
		</form>
		</div>
	</div>
</div>
{{-- pharmacy selection ends --}}
@elseif(Auth::user()->user_type_id == 96)  {{-- if pharmacy --}}

@php
    $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
    $pharmacies = $staff->pharmacies()->pluck('pharmacy_name')->toArray();
@endphp

<div class="card-header" style="background-color: #0d97c673;border-bottom: 1px solid #eaedf100;">
    <h3 class="card-title" style="font-size: 10px; margin:-15px; 
    color: #0d97c6;">PHARMACIES: @foreach ($pharmacies as $pharmacy)
            <li style="list-style: circle;">{{ $pharmacy }}</li>
        @endforeach &nbsp;</h3>
</div>

@endif

        <!-- SEARCH -->
        <div class="dropdown d-md-flex">
            <a class="nav-link icon full-screen-link nav-link-bg">
                <i class="fe fe-maximize fullscreen-button"></i>
            </a>
        </div>

        <!-- FULL-SCREEN -->
        <div class="dropdown profile-1">
            <a href="#" data-toggle="dropdown" class="nav-link pr-2 leading-none d-flex">
                <span>

                    <img src="{{ asset('assets/images/avatar.png') }}" alt="profile-user"
                        class="avatar  profile-user brround cover-image">

                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                <div class="drop-heading">
                    <div class="text-center">
                        <h5 class="text-dark mb-0">{{ auth()->user()->username }}</h5>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </div>
                </div>
                <div class="dropdown-divider m-0"></div>
                {{-- <a class="dropdown-item" href="#">
                    <i class="dropdown-icon mdi mdi-account-outline"></i> Profile
                </a> --}}
                <a class="dropdown-item"
                    onclick="event.preventDefault();
                 document.getElementById('logout-form').submit();">
                    <i class="dropdown-icon mdi  mdi-logout-variant"></i> Sign Out
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>

    </div>
</div>
