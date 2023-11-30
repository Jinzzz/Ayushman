@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">View Leave Details</h3>
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Salary Head Name</label>
                                    <input type="text" class="form-control" readonly name="salary_head_name"
                                        value="{{ $show->salary_head_name }}" placeholder="Staff Name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Salary Head Type</label>
                                    <input type="text" class="form-control" readonly name="salary_head_type"
                                        value="{{ $show->salary_head_type }}" placeholder="Branch Name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="custom-switch">
                                        <input type="checkbox" id="is_active" name="is_active" @if($show->status) checked @endif disabled class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">
                                            @if($show->status)
                                                Active
                                            @else
                                                Inactive
                                            @endif
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Company</label>
                                    <input type="text" class="form-control" readonly name="company"
                                        value="{{ $show->company }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Remarks</label>
                                    <input type="text" class="form-control" readonly name="remark"
                                        value="{{ $show->remark }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                        <a class="btn btn-danger" href="{{ route('salarys.index') }}">Back</a>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>

@endsection
