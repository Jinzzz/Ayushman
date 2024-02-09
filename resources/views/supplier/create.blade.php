@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
                </div>
                <div class="col-lg-12" style="background-color: #fff;">
                    @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                        <p>{{$message}}</p>
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <!-- <strong>Whoops!</strong> There were some problems with your input.<br><br> -->
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('supplier.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Supplier Type*</label>
                                    <select class="form-control" required name="supplier_type_id" id="supplier_type_id">
                                        <option value="">Select Supplier Type</option>
                                        <option value="1">Individual</option>
                                        <option value="2">Business</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Supplier Name*</label>
                                    <input type="text" class="form-control" required name="supplier_name" value="{{ old('supplier_name') }}" placeholder="Supplier Name">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Supplier Address*</label>
                                    <textarea class="form-control" required name="supplier_address" placeholder="Supplier Address">{{ old('supplier_address') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Country*</label>
                                <select class="form-control" required name="country"  id="country">
                                    <option value="" disabled selected>Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->country_id  }}">{{ $country->country_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">State*</label>
                            <select class="form-control" required name="state" id="state">
                                <option value="" disabled selected>Select State</option>
                                <!-- States will be dynamically populated here -->
                            </select>
                        </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label class="form-label">City*</label>
                                    <input type="text" class="form-control" required name="supplier_city" value="{{ old('supplier_city') }}" placeholder="Supplier City">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Pincode</label>
                                    <input type="number" class="form-control" max="999999" min="100000" name="pincode" placeholder="Pincode">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Business Name</label>
                                    <input type="text" class="form-control" name="business_name" value="{{ old('business_name') }}" placeholder="Business Name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Contact Number*</label>
                                    <input type="number" class="form-control" max="9999999999" min="1000000000" required name="phone_1" value="{{ old('phone_1') }}" placeholder="Phone Number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Alternative Mobile Number</label>
                                    <input type="number" max="9999999999" min="1000000000" class="form-control" name="phone_2" value="{{ old('phone_2') }}" placeholder="Alternative Number">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Website</label>
                                    <input type="text" class="form-control" name="website" value="{{ old('website') }}" placeholder="Website">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Credit Period</label>
                                    <input type="number" class="form-control" max="99" min="0" pattern="\d*" name="credit_period" value="{{ old('credit_period') }}" placeholder="Credit Period">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Credit Limit</label>
                                    <input type="number" class="form-control" max="999999" min="0" pattern="\d*" name="credit_limit" value="{{ old('credit_limit') }}" placeholder="Credit Limit">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Opening Balance</label>
                                    <input type="number" class="form-control" max="999999" min="0" pattern="\d*" name="opening_balance" value="{{ old('opening_balance', '0') }}" placeholder="Opening Balance">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Opening Balance Type</label>
                                    <select class="form-control" name="opening_balance_type" id="opening_balance_type">
                                        <option value="">Select Balance Type</option>
                                        <option value="1">Debit</option>
                                        <option value="2">Credit</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Opening Balance As On</label>
                                    <input type="date" class="form-control" name="opening_balance_date" value="{{ old('opening_balance_date') }}" placeholder="Opening Balance Date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">GSTIN Number</label>
                                    <input type="number" class="form-control" min="0" name="GSTNO" value="{{ old('GSTIN NO') }}" placeholder="GSTIN NO">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Terms And Conditions</label>
                                    <textarea class="form-control" name="terms_and_conditions" placeholder="Terms And Condition">{{ old('terms_and_conditions') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-label">Status*</div>
                                    <label class="custom-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" checked>
                                        <span id="statusLabel" class="custom-switch-indicator"></span>
                                        <span id="statusText" class="custom-switch-description">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <center>
                                <button type="submit" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Add</button>
                                <button type="reset" class="btn btn-raised btn-success">
                                    Reset</button>
                                <a class="btn btn-danger" href="{{ route('supplier.index') }}">Cancel</a>
                            </center>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
@endsection
@section('js')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    function toggleStatus(checkbox) {
        if (checkbox.checked) {
            $("#statusText").text('Active');
            $("input[name=is_active]").val(1); // Set the value to 1 when checked
        } else {
            $("#statusText").text('Inactive');
            $("input[name=is_active]").val(0); // Set the value to 0 when unchecked
        }
    }

    $(document).ready(function () {
        $('#country').on('change', function () {
            var countryId = $(this).val();

            // Make an AJAX request to get states based on the selected country
            $.ajax({
                url: '/get-states/' + countryId, // Replace with the actual route
                type: 'GET',
                success: function (data) {
                    // Clear existing options
                    $('#state').empty();

                    // Add new options based on the response
                    $.each(data, function (key, value) {
                        $('#state').append('<option value="' + value.state_id + '">' + value.state_name + '</option>');
                    });
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
    });

    $(document).ready(function () {
        // Function to toggle the 'required' attribute on the 'Opening Balance As On' field
        function toggleOpeningBalanceAsOnRequired() {
            var openingBalance = parseFloat($("[name='opening_balance']").val());

            if (openingBalance > 0) {
                $("[name='opening_balance_date']").prop('required', true);
            } else {
                $("[name='opening_balance_date']").prop('required', false);
            }
        }

        // Call the function on page load
        toggleOpeningBalanceAsOnRequired();

        // Add an event listener to 'Opening Balance' field
        $("[name='opening_balance']").on('input', function () {
            // Call the function whenever the 'Opening Balance' value changes
            toggleOpeningBalanceAsOnRequired();
        });
    });
</script>
@endsection