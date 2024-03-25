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
.card-title {
    font-size: 12px;
}
h2, .h2 {
    font-size: 20px;
}
.text-colored{
	color: #16b94d !important;
}

</style>
@if ($message = Session::get('error'))
<div class="alert alert-danger">
	<p>{{$message}}</p>
</div>
@endif
<div class="container">
	<div class="row">
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card bg-success img-card box-success-shadow">
				<div class="card-body bg-transparent">
					<div class="d-flex">
						<div class="text-white">
							<h2 class="mb-0 number-font">{{@$lowStock}}</h2>
							<p class="text-white mb-0" style="font-size:12px;">Low Stock Medicines</p>
						</div>
						<div class="ml-auto">
							<i class="fa fa-bar-chart text-white fs-30 mr-2 mt-2"></i>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card bg-success img-card box-success-shadow">
				<div class="card-body bg-transparent">
					<div class="d-flex">
						<div class="text-white">
							<h2 class="mb-0 number-font">{{@$dailySale->daily_sales ?? '0'}}</h2>
							<p class="text-white mb-0" style="font-size:12px;">Today's Sales</p>
						</div>
						<div class="ml-auto">
							<i class="fa fa-dollar text-white fs-30 mr-2 mt-2"></i>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card bg-success img-card box-success-shadow">
				<div class="card-body bg-transparent">
					<div class="d-flex">
						<div class="text-white">
							<h2 class="mb-0 number-font">{{@$medicineSaleWeekly->weekly_sales ?? '0'}}</h2>
							<p class="text-white mb-0" style="font-size:12px;">Weekly Sales</p>
						</div>
						<div class="ml-auto">
							<i class="fa fa-dollar text-white fs-30 mr-2 mt-2"></i>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card bg-success img-card box-success-shadow">
				<div class="card-body bg-transparent">
					<div class="d-flex">
						<div class="text-white">
							<h2 class="mb-0 number-font">{{@$medicineSaleMonthly->monthly_sales ?? '0'}}</h2>
							<p class="text-white mb-0" style="font-size:12px;">Monthly Sales</p>
						</div>
						<div class="ml-auto">
							<i class="fa fa-dollar text-white fs-30 mr-2 mt-2"></i>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card bg-success img-card box-success-shadow">
				<div class="card-body bg-transparent">
					<div class="d-flex">
						<div class="text-white">
							<h2 class="mb-0 number-font">{{@$totalSales->sales ?? '0'}}</h2>
							<p class="text-white mb-0" style="font-size:12px;">Total Sales</p>
						</div>
						<div class="ml-auto">
							<i class="fa fa-dollar text-white fs-30 mr-2 mt-2"></i>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
	</div>
	<!-- ROW -->
	<div class="row">
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/medicine/index') }}">
				<div class="card-header">
					<h3 class="card-title">View Medicines</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ route('supplier.index') }}">
				<div class="card-header">
					<h3 class="card-title">View Suppliers</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/medicine-purchase-invoice/index') }}">
				<div class="card-header">
					<h3 class="card-title">Purchase Invoice</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/medicine-purchase-return/index') }}">
				<div class="card-header">
					<h3 class="card-title">Purchase Return</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/medicine-stock-updation/index') }}">
				<div class="card-header">
					<h3 class="card-title">Stock Correction</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/medicine-stock-updations') }}">
				<div class="card-header">
					<h3 class="card-title">Initial Stock Update</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/medicine-sales-invoices') }}">
				<div class="card-header">
					<h3 class="card-title">Medicine Sales Invoice</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/medicine-sales-return') }}">
				<div class="card-header">
					<h3 class="card-title">Medicine Sales Return</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/branch/stock-transfer') }}">
				<div class="card-header">
					<h3 class="card-title">Stock transfer to Pharmacy</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ route('prescriptions.index') }}">
				<div class="card-header">
					<h3 class="card-title">Prescriptions</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/sales-report') }}">
				<div class="card-header">
					<h3 class="card-title">Sales Report</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/sales-return-report') }}">
				<div class="card-header">
					<h3 class="card-title">Sales Return Report</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/purchase-report') }}">
				<div class="card-header">
					<h3 class="card-title">Purchase Report</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/purchase-return-report') }}">
				<div class="card-header">
					<h3 class="card-title">Purchase Return Report</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/stock-transfer-report') }}">
				<div class="card-header">
					<h3 class="card-title">Stock Transfer Report</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
			<div class="card">
				<a class="" href="{{ url('/current-stocks-report') }}">
				<div class="card-header">
					<h3 class="card-title">Current stocks Report</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
	
	</div>
</div>
@endsection