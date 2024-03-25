@extends('layouts.app')

@section('content')

@php
use App\Models\Mst_Branch;
use App\Models\Mst_Staff;
use App\Models\Mst_Supplier;
use App\Models\Trn_Consultation_Booking;
use App\Models\Trn_Medicine_Stock;

@endphp

<style>
	.bg-primary {
		background: #5e2dd8 !important;
	}

	.bg-secondary {
		background: #d43f8d !important;
	}

	.bg-success {
		background: #09ad95 !important;
	}

	.bg-info {
		background: #0774f8 !important;
	}
</style>
@if ($message = Session::get('error'))
<div class="alert alert-danger">
	<p>{{$message}}</p>
</div>
@endif
<div class="container">
	<div class="row">
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
			<div class="card bg-success img-card box-success-shadow">
				<div class="card-body bg-transparent">
					<div class="d-flex">
						<div class="text-white">
							<h2 class="mb-0 number-font">{{@$totalSales->sales ?? '0'}}</h2>
							<p class="text-white mb-0" style="font-size:12px;">Total Sales</p>
						</div>
						<div class="ml-auto">
							<i class="fa fa-bar-chart text-white fs-30 mr-2 mt-2"></i>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
			<div class="card bg-success img-card box-success-shadow">
				<div class="card-body bg-transparent">
					<div class="d-flex">
						<div class="text-white">
							<h2 class="mb-0 number-font">{{@$totalPurchase->purchase ?? '0'}}</h2>
							<p class="text-white mb-0" style="font-size:12px;">Total Purchase</p>
						</div>
						<div class="ml-auto">
							<i class="fa fa-bar-chart text-white fs-30 mr-2 mt-2"></i>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
			<div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
			<div class="card bg-success img-card box-success-shadow">
				<div class="card-body bg-transparent">
					<div class="d-flex">
						<div class="text-white">
							<h2 class="mb-0 number-font">{{@$creditPurchase ?? '0'}}</h2>
							<p class="text-white mb-0" style="font-size:12px;">Count of Credit Purchases </p>
						</div>
						<div class="ml-auto">
							<i class="fa fa-bar-chart text-white fs-30 mr-2 mt-2"></i>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
		
		
		
		
	</div>
	<!-- ROW -->
</div>
@endsection