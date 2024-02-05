@extends('layouts.app')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<div class="container">
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Edit Wellness</h3>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('status'))
                    <div class="alert alert-success">
                        <p>{{$message}}</p>
                    </div>
                    @endif
                </div>
                <div class="col-lg-12" style="background-color: #fff;">
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
                    <form action="{{route('wellness.update',['wellness_id'=>$wellness->wellness_id])}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Wellness Name*</label>
                                    <input type="text" class="form-control" required name="wellness_name" value="{{$wellness->wellness_name}}" maxlength="100" placeholder="Wellness Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Wellness Description*</label>
                                    <textarea class="form-control" required name="wellness_description" placeholder="Wellness Description">{{ $wellness->wellness_description }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Regular Price*</label>
                                    <input type="text" id="regularPrice" class="form-control" required name="wellness_cost" value="{{$wellness->wellness_cost}}" placeholder="Wellness Cost" oninput="validateDecimalInput(this)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Wellness Offer Price*</label>
                                    <input type="number" class="form-control" id="offerPrice" oninput="validatePrices()" required name="wellness_offer_price" value="{{$wellness->offer_price}}" placeholder="Wellness Offer Price">
                                    <span id="priceError" style="color: red;"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Wellness Duration(Minutes)*</label>
                                    <input type="text" class="form-control" required name="wellness_duration" value="{{$wellness->wellness_duration}}" maxlength="10" placeholder="Wellness Duration(Minutes)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group checkbox">
                                    <label for="branch_id" class="form-label">Wellness Image*</label>
                                    <input type="file" class="form-control" name="wellness_image" placeholder="Wellness Image">
                                    <input type="hidden" class="form-control" name="saved_img" value="{{$wellness->wellness_image}}">
                                    @if($wellness->wellness_image)
                                    <img src="{{url('/assets/uploads/wellness_image/'.$wellness->wellness_image)}}" alt="Wellness Image" style="max-width: 100px; max-height: 100px;">
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Remarks</label>
                                    <textarea type="text" class="form-control" name="remarks" value="{{$wellness->remarks}}" placeholder="Remarks">{{$wellness->remarks}}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group checkbox">
                                    <label for="branch_id" class="form-label">Branch*</label>
                                    <select class="multi-select" required name="branch[]" multiple style="width: 100%;">
                                        @foreach($branch as $id => $branchName)
                                        <option value="{{ $id }}" {{ $branch_ids->contains($id) ? 'selected' : '' }}>
                                            {{ $branchName }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Wellness Inclusions*</label>
                                    <textarea class="form-control" required name="wellness_inclusions" id="wellnessInclusion" placeholder="Wellness Inclusions">{{$wellness->wellness_inclusions}}</textarea>
                                    <span style="color: red;">*Please provide wellness inclusions using bullet points only.</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Wellness T&C*</label>
                                    <textarea class="form-control" required name="wellness_terms_conditions" id="termsandCondition" placeholder="Wellness T&C">{{$wellness->wellness_terms_conditions}}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-label">Status</div>
                                    <label class="custom-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" value="{{$wellness->is_active}}" id="is_active" name="is_active" onchange="toggleStatus(this)" class="custom-switch-input" @if($wellness->is_active) checked @endif>
                                        <span id="statusLabel" class="custom-switch-indicator"></span>
                                        <span id="statusText" class="custom-switch-description">
                                            @if($wellness->is_active)
                                            Active
                                            @else
                                            Inactive
                                            @endif
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-raised btn-primary"><i class="fa fa-check-square-o"></i>Update</button>
                                        <a class="btn btn-danger" href="{{route('wellness.index')}}">Cancel</a>
                                    </center>
                                </div>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script type="text/javascript">
    function validatePrices() {
        var regularPrice = parseFloat(document.getElementById('regularPrice').value);
        var offerPrice = parseFloat(document.getElementById('offerPrice').value);
        var priceError = document.getElementById('priceError');

        if (offerPrice >= regularPrice) {
            priceError.textContent = 'Offer Price must be less than Regular Price';
        } else {
            priceError.textContent = '';
        }
    }
    $(document).ready(function() {
        CKEDITOR.replace('wellnessInclusion', {
            removePlugins: 'image',

        });

        $(document).ready(function() {
            CKEDITOR.replace('termsandCondition', {
                removePlugins: 'image',
            });
        });
    });
    //js for dropdown:
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
<script>
    function toggleStatus(checkbox) {
        if (checkbox.checked) {
            $("#statusText").text('Active');
            $("input[name=is_active]").val(1);
        } else {
            $("#statusText").text('Inactive');
            $("input[name=is_active]").val(0);
        }
    }

    function validateDecimalInput(input) {
        var numericValue = input.value.replace(/[^0-9.]/g, '').slice(0, 13);
        input.value = numericValue;

        var isValid = /^\d{1,10}(\.\d{1,2})?$/.test(numericValue);
        input.setCustomValidity(isValid ? '' : 'Please enter a valid decimal number (up to 10 digits before the decimal point and up to 2 digits after).');
        input.parentNode.querySelector('.error-message').style.display = isValid ? 'none' : 'block';
    }
</script>
@endsection