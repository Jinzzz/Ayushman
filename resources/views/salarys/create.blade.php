@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Create Salary Head</h3>
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
                    <form action="{{ route('salarys.store') }}" id="addFm" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label branch" id="branchLabel">Salary Head Name*</label>
                                    <input type="text" class="form-control" name="salary_head_name" id="salary_head_name" value="{{ old('salary_head_name') }}" placeholder="Salary Head Name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Salary Head Type*</label>
                                    <select class="form-control" name="salary_head_type" id="salary_head_type">
                                        <option value="" disabled selected>Choose Salary Head Type</option>
                                        @foreach($branch as $branchName)
                                            <option value="{{ $branchName->id }}">{{ $branchName->salary_head_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Remark*</label>
                                    <textarea class="form-control" name="remark" placeholder="Remark">{{ old('remark') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-label">Status*</div>
                                    <label class="custom-switch">
                                        <input type="hidden" name="status" value="0">
                                        <!-- Default value for Inactive -->
                                        <input type="checkbox" id="statusSwitch" name="status" onchange="toggleStatus(this)" class="custom-switch-input" checked>
                                        <span id="statusLabel" class="custom-switch-indicator"></span>
                                        <span id="statusText" class="custom-switch-description">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <center>
                                <button type="submit" id="submitForm" class="btn btn-raised btn-primary">
                                    <i class="fa fa-check-square-o"></i> Add
                                </button>
                                <a class="btn btn-success" href="{{ route('salarys.create') }}">Reset</a>
                                <a class="btn btn-danger" href="{{ route('salarys.index') }}">Cancel</a>
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

    <script>
        function toggleStatus(checkbox) {
            if (checkbox.checked) {
                $("#statusText").text('Active');
                $("input[name=status]").val(1); // Set the value to 1 when checked (Active)
            } else {
                $("#statusText").text('Inactive');
                $("input[name=status]").val(0); // Set the value to 0 when unchecked (Inactive)
            }
        }
    </script>

    <script>
        var validator; // Declare validator outside $(document).ready()

        $(document).ready(function () {
            validator = $("#addFm").validate({
                rules: {
                    salary_head_name: "required",
                    salary_head_type: "required",
                    status: "required",
                    remark: "required",
                },
                messages: {
                    salary_head_name: "Please select a salary head name.",
                    salary_head_type: "Please select a salary head type.",
                    status: "Please enter status.",
                    remark: "Please enter remark.",
                },
                submitHandler: function (form) {
                    // Your form submission logic here
                    form.submit();
                },
            });

            $(document).on('click', '#submitForm', function () {
                if (validator.form()) {
                    $('#addFm').submit();
                } else {
                    flashMessage('w', 'Please fill all mandatory fields');
                }
            });

            function flashMessage(type, message) {
                // Implement or replace this function based on your needs
                console.log(type, message);
            }
        });
    </script>
@endsection
