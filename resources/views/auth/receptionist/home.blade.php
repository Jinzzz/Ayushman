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
							<h2 class="mb-0 number-font">{{@$currentDayLeave}}</h2>
							<p class="text-white mb-0" style="font-size:12px;">Staffs On Leave</p>
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
							<h2 class="mb-0 number-font">{{@$bookingCount}}</h2>
							<p class="text-white mb-0" style="font-size:12px;">Todays Bookings</p>
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
							<h2 class="mb-0 number-font">{{@$doctorOnLeaveCount}}</h2>
							<p class="text-white mb-0" style="font-size:12px;">Doctors On Leave</p>
						</div>
						<div class="ml-auto">
							<i class="fa fa-bar-chart text-white fs-30 mr-2 mt-2"></i>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
	</div>
	<div class="row">
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
			<div class="card">
				<a class="" href="{{ url('/booking/consultation-booking') }}">
				<div class="card-header">
					<h3 class="card-title">View Consultation Bookings</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
			<div class="card">
				<a class="" href="{{ url('/booking/wellness-booking') }}">
				<div class="card-header">
					<h3 class="card-title">View Wellness Bookings</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
		<div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
			<div class="card">
				<a class="" href="{{ url('/booking/therapy-booking') }}">
				<div class="card-header">
					<h3 class="card-title">View Therapy Bookings</h3>
					<div class="card-options">	
						<i class="fa fa-arrow-circle-o-right text-colored"></i>
					</div>
				</div>
			</a>
			</div>
		</div><!-- COL END -->
	</div>
	<!-- ROW -->
		
		<div class="row">
		<div class="col-12 col-sm-12">
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title mb-0">Todays Consultation Bookings</h3>
				</div>
				<div class="card-body">
					<div class="grid-margin">
						<div class="">
							<div class="table-responsive">
								<table class="table card-table border table-vcenter text-nowrap align-items-center">
									<thead class="">
										<tr>
											<th>ID</th>
											<th>Type</th>
											<th>Patient</th>
											<th>Doctor</th>
											<th>Timeslot</th>
											<th>Created date</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
									    @php
                                            $i = 0;
                                        @endphp
									    @if($todaysConsultationBooking->isEmpty())
                                            <p>No Consultation Bookings for today</p>
                                        @else
                                            @foreach($todaysConsultationBooking as $bookings)
                                              <tr>
											    <td>{{ ++$i }}</td>
												<td>{{@$bookings->booking_reference_number}}</td>
												<td>{{@$bookings->patient['patient_name']}}</td>
												<td>{{@$bookings->doctor['staff_name']}}</td>
												<td>Appointment Date: {{@$bookings->booking_date}}
												<br>{{@$bookings->time_slot_id}}</td>
												<td>{{ @$bookings->created_at->format('Y-m-d') }}</td>
												<td>{{@$bookings->bookingStatus['master_value']}}</td>
											   </tr>
													
                                            @endforeach
                                        @endif
												
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
			<div class="col-12 col-sm-12">
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title mb-0">Todays Wellness Bookings</h3>
				</div>
				<div class="card-body">
					<div class="grid-margin">
						<div class="">
							<div class="table-responsive">
								<table class="table card-table border table-vcenter text-nowrap align-items-center">
									<thead class="">
										<tr>
											<th>ID</th>
											<th>Booking ID</th>
											<th>Patient</th>
											<th>Wellness</th>
											<th>Created date</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
									    @php
                                            $i = 0;
                                        @endphp
									    @if($todaysWellnessBooking->isEmpty())
                                            <p>No Wellness Bookings for today</p>
                                        @else
                                            @foreach($todaysWellnessBooking as $wbookings)
                                              <tr>
											    <td>{{ ++$i }}</td>
												<td>{{@$wbookings->booking_reference_number}}</td>
												<td>{{@$wbookings->patient['patient_name']}}</td>
												<td>
												    <ul>
                                                        @foreach ($wbookings->wellnessBookings as $wellnessBook)
                                                            <li>{{ $wellnessBook->wellness['wellness_name'] }}</li>
                                                        @endforeach
                                                    </ul>
												</td>
												<td>{{ @$wbookings->created_at->format('Y-m-d') }}</td>
												<td>{{@$wbookings->bookingStatus['master_value']}}</td>
											   </tr>
													
                                            @endforeach
                                        @endif
												
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
			<div class="col-12 col-sm-12">
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title mb-0">Todays Therapy Bookings</h3>
				</div>
				<div class="card-body">
					<div class="grid-margin">
						<div class="">
							<div class="table-responsive">
								<table class="table card-table border table-vcenter text-nowrap align-items-center">
									<thead class="">
										<tr>
											<th>ID</th>
											<th>Booking ID</th>
											<th>Patient</th>
											<th>Therapy</th>
											<th>Created date</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
									    @php
                                            $i = 0;
                                        @endphp
									    @if($todaysTherapyBooking->isEmpty())
                                            <p>No Therapy Bookings for today</p>
                                        @else
                                            @foreach($todaysTherapyBooking as $tbookings)
                                              <tr>
											    <td>{{ ++$i }}</td>
												<td>{{@$tbookings->booking_reference_number}}</td>
												<td>{{@$tbookings->patient['patient_name']}}</td>
												<td>
												    <ul>
                                                        @foreach ($tbookings->therapyBookings as $therapyBook)
                                                            <li>{{ @$therapyBook->therapy['therapy_name'] }}</li>
                                                        @endforeach
                                                    </ul>
												</td>
												<td>{{ @$tbookings->created_at->format('Y-m-d') }}</td>
												<td>{{@$tbookings->bookingStatus['master_value']}}</td>
											   </tr>
													
                                            @endforeach
                                        @endif
												
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
		<div class="col-6 col-sm-6">
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title mb-0">Staff On Leave</h3>
				</div>
				<div class="card-body">
					<div class="grid-margin">
						<div class="">
							<div class="table-responsive">
								<table class="table card-table border table-vcenter text-nowrap align-items-center">
									<thead class="">
										<tr>
										    <th>ID</th>
											<th>Staff</th>
											<th>Date</th>
											
											<th>Total Days</th>
										</tr>
									</thead>
									<tbody>
									    @php
                                            $i = 0;
                                        @endphp
                                         @if($staffOnLeave->isEmpty())
                                            <p>No Staffs on leave today</p>
                                        @else
                                            @foreach($staffOnLeave as $leave)
										<tr>
											<td>{{ ++$i }}</td>
												<td>{{@$leave->staff['staff_name']}}</td>
											    <td>{{ $leave->from_date }}
											    <br>
											    {{ $leave->to_date }}
											    </td>
                                                
                                                 <td>{{ $leave->days}}</td>
												
											</tr>
										@endforeach
										@endif
															
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
	<div class="col-6 col-sm-6">
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title mb-0">Doctors On Leave</h3>
				</div>
				<div class="card-body">
					<div class="grid-margin">
						<div class="">
							<div class="table-responsive">
								<table class="table card-table border table-vcenter text-nowrap align-items-center">
									<thead class="">
										<tr>
										    <th>ID</th>
											<th>Doctor</th>
											<th>Date</th>
											<th>Total Days</th>
										</tr>
									</thead>
									<tbody>
									    @php
                                            $i = 0;
                                        @endphp
                                         @if($doctorOnLeave->isEmpty())
                                            <p>No Doctors on leave today</p>
                                        @else
                                            @foreach($doctorOnLeave as $leave)
										<tr>
											<td>{{ ++$i }}</td>
												<td>{{@$leave->staff['staff_name']}}</td>
											    <td>{{ $leave->from_date }}
											    <br>
											    {{ $leave->to_date }}
											    </td>
                                                
                                                 <td>{{ $leave->days}}</td>
												
											</tr>
										@endforeach
										@endif
															
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- COL END -->
	</div>
</div>
@endsection