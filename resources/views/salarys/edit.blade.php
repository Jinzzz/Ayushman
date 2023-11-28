@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">Edit Salary Head</h3>
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

                        <form action="{{ route('salarys.update', ['id' => $id]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Salary Head Name</label>
                                        <input type="text" class="form-control" name="salary_head_name"
                                            value="{{ $masters->salary_head_name }}" placeholder="Staff Name">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Salary Head Type</label>
                                        <select class="form-control" name="salary_head_type" id="salary_head_type">
                                        <option value="" disabled selected>Choose Salary Head Type</option>
                                            @foreach($branchs as $lt)
                                                <option value="{{ $lt->id }}" {{ $lt->salary_head_type == $masters->salary_head_type ? 'selected' : '' }}>
                                                    {{ $lt->salary_head_type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="custom-switch">
                                            <input type="checkbox" id="is_active" name="status" onchange="toggleStatus(this)"
                                                 class="custom-switch-input" @if($masters->status) checked @endif>
                                            <span id="statusLabel" class="custom-switch-indicator"></span>
                                            <span id="statusText" class="custom-switch-description">
                                                @if($masters->status)
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Company</label>
                                        <input type="text" class="form-control" readonly name="company"
                                            value="{{ $masters->company }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Remarks</label>
                                        <textarea class="form-control" name="remark" placeholder="Reason For Leave">{{ $masters->remark ?? old('remark') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <center>
                                            <button type="submit" class="btn btn-raised btn-primary">
                                                <i class="fa fa-check-square-o"></i> Update
                                            </button>
                                            <button type="reset" class="btn btn-raised btn-success">
                                                Reset
                                            </button>
                                            <a class="btn btn-danger" href="{{ route('salarys.index') }}">Cancel</a>
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
    <script type="text/javascript">
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
@endsection
