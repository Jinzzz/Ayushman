@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Create External Doctor</h3>
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
                    <form action="{{ route('externaldoctors.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Doctor Name*</label>
                                    <input type="text" class="form-control" required name="doctor_name" value="{{ old('doctor_name') }}" placeholder="Doctor Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Contact No*</label>
                                    <input type="text" class="form-control" required name="contact_no" value="{{ old('contact_no') }}" placeholder="Contact No">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control"  name="contact_email" id="contact_email" value="{{ old('contact_email') }}" placeholder="Email">
                                    <div class="text-danger" id="email-error"></div>
                                </div>
                            </div>
                       
                       
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control"  name="address" placeholder="Address">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Remarks</label>
                                    <textarea class="form-control"  name="remarks" placeholder="Remarks">{{ old('remarks') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Commission(%)</label>
                                    <input type="text" class="form-control"  required name ="commission" value="{{ old('commision') }}" placeholder="Commission">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-label">Status</div>
                                    <label class="custom-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <!-- Hidden field for false value -->
                                        <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" checked>
                                        <span id="statusLabel" class="custom-switch-indicator"></span>
                                        <span id="statusText" class="custom-switch-description">Active</span>
                                    </label>
                                </div>
                            </div>
                          </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <center>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-check-square-o"></i> Add
                                    </button>
                                    <a class="btn btn-danger" href="{{ route('externaldoctors.index') }}">Cancel</a>
                                </center>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#contact_email').on('input', function() {
            var emailInput = $(this).val();
            var emailErrorDiv = $('#email-error');
            
            if (emailInput.trim() === '' || isValidEmail(emailInput)) {
                emailErrorDiv.text('');
            } else {
                emailErrorDiv.text('Please enter a valid email address.');
            }
        });
        
        function isValidEmail(email) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    });
</script>
@endsection
