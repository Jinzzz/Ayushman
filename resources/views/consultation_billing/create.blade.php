@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Create Invoive</h3>
                </div>
                <div class="col-lg-12" style="background-color: #fff;">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('consultation_billing.generateisnvoice') }}" id="addFm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $consultation_id }}">
                        <input type="hidden" name="patient_id" value="{{ $data->patient_id }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label branch">Booking Reference</label>
                                    <input type="text" class="form-control" name="booking_reference_number" id="booking_reference_number"  value="{{ $data->booking_reference_number }}" readonly>
                                </div>
                            </div>
                           
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label branch">Invoice Date</label>
                                    <input type="date" class="form-control" name="invoice_date" id="invoice_date" value="{{ old('invoice_date') ?? now()->format('Y-m-d') }}" placeholder="Invoice Date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Booking Date</label>
                                    <input type="date" class="form-control" name="booking_date" id="booking_date" value="{{ $data->booking_date }}" readonly>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Patient Name</label>
                                    <input type="text" class="form-control" name="patient_name" id="patient_name" value="{{ $data->patient_name }}" readonly>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Patient Contact</label>
                                    <input type="text" class="form-control" name="patient_contact" id="patient_contact" value="{{ $data->patient_mobile }}"  readonly>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"> Amount</label>
                                    <input type="text" class="form-control" name="due_amount" id="due_amount" value="{{ $data->booking_fee }}" >

                                </div>
                            </div>
                        </div>
                        </div>
                        </br></br>
                        <div class="form-group">
                            <center>
                                <button type="submit" id="submitForm" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Generate Invoice
                                </button>
                            </center>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
@endsection