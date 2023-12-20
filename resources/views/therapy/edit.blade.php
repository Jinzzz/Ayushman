@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">Edit Therapy</h3>
                </div>
                <div class="col-lg-12" style="background-color: #fff;">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('therapy.update', ['id' => $therapy->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Therapy Name*</label>
                                    <input type="text" class="form-control @error('therapy_name') is-invalid @enderror"
                                        required name="therapy_name" maxlength="100" value="{{ $therapy->therapy_name }}"
                                        placeholder="Therapy Name">
                                    @error('therapy_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Therapy Cost</label>
                                    <input type="number" class="form-control @error('therapy_cost') is-invalid @enderror"
                                        required name="therapy_cost" maxlength="14" value="{{ $therapy->therapy_cost }}"
                                        placeholder="Therapy Cost">
                                    @error('therapy_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Remarks</label>
                                    <textarea class="form-control @error('remarks') is-invalid @enderror" required
                                        name="remarks" placeholder="Remarks">{{ $therapy->remarks }}</textarea>
                                    @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-label">Status</div>
                                    <label class="custom-switch">
                                        <input type="checkbox" id="is_active" name="is_active"
                                            onchange="toggleStatus(this)"
                                            class="custom-switch-input" {{ $therapy->is_active ? 'checked' : '' }}>
                                        <span id="statusLabel" class="custom-switch-indicator"></span>
                                        <span id="statusText" class="custom-switch-description">
                                            {{ $therapy->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-raised btn-primary">
                                            <i class="fa fa-check-square-o"></i> Update</button>
                                        <a class="btn btn-danger" href="{{ route('therapy.index') }}">Cancel</a>
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
<script>
    function toggleStatus(checkbox) {
        const statusText = checkbox.checked ? 'Active' : 'Inactive';
        $("#statusText").text(statusText);
        $("input[name=is_active]").val(checkbox.checked ? 1 : 0);
    }
</script>
@endsection
