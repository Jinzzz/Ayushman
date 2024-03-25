@extends('layouts.app')

@section('content')
<div class="container">
    <style>
        .no-updation {
            display: none;
        }
    </style>
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Application Settings</h3>
                </div>
                <div class="col-lg-12" style="background-color: #fff;">
                    @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                        <p>{{$message}}</p>
                    </div>
                    @endif
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
                    @if ($message = Session::get('status'))
                        <div class="alert alert-success">
                            <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
                        </div>
                    @endif
                    <form action="{{route('settings.update',['id'=>1])}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Company Logo* (Format : png, jpg, jpeg. Max Size: 2 MB) </label>
                                    <input type="file" class="form-control" name="company_logo" value="{{ $settings->company_logo ?? '' }}" accept=".png, .jpeg, .jpg">
                                    <br>
                                    @isset($settings->company_logo)
                                    <div class="name">  
                                        <span><img style="width:80px;" src="{{asset('assets/uploads/'.$settings->company_logo)}}" ></span>   
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Company Name*</label>
                                    <input type="text" class="form-control" required name="company_name" value="{{ $settings->company_name ?? '' }}" placeholder="Company Name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Company E-mail*</label>
                                    <input type="text" class="form-control" required name="company_email" value="{{ $settings->company_email ?? '' }}" placeholder="Company E-mail">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Company GSTIN*</label>
                                    <input type="text" class="form-control" required name="gst_number" value="{{ $settings->gst_number ?? '' }}" placeholder="Company GSTIN">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Location*</label>
                                    <input type="text" class="form-control" required name="company_location" value="{{ $settings->company_location ?? '' }}" placeholder="Company Location">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Primary Contact Number*</label>
                                    <input type="text" class="form-control numericInput" required name="contact_number_1"  value="{{ $settings->contact_number_1 ?? '' }}" placeholder="Primary Contact Number">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Secondary Contact Number*</label>
                                    <input type="text" class="form-control numericInput" required name="contact_number_2"  value="{{ $settings->contact_number_2 ?? '' }}" placeholder="Secondary Contact Number">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Company Address*</label>
                                    <textarea class="form-control" required name="company_address" placeholder="Company Address">{{ $settings->company_address ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Company Website URL*</label>
                                    <input type="text" class="form-control" required name="company_website_link" value="{{ $settings->company_website_link ?? '' }}" placeholder="Company Website URL">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <center>
                                <button type="submit" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Update</button>
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

<script>
    $(document).ready(function() {
        $('.numericInput').on('input', function(event) {
            var inputValue = $(this).val();
            inputValue = inputValue.replace(/[^0-9]/g, ''); // Remove anything that's not a digit
            $(this).val(inputValue);
        });
    });
</script>
@endsection