@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><strong>EMPLOYEE BRANCH TRANSFER</strong></h3>
            </div>
            <form action="{{ route('branchTransfer.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="from_branch" class="form-label"> From Branch</label>
                                <select class="form-control" name="from_branch" id="from_branch" onchange="loadEmployees()">
                                    <option value="">Choose Branch</option>
                                    @foreach($branch as $id => $branchName)
                                    <option value="{{ $id }}"{{ old('branch_id') == $id ? 'selected' : '' }}>
                                        {{ $branchName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="to_branch" class="form-label"> To Branch</label>
                                <select class="form-control" name="to_branch" id="to_branch">
                                    <option value="">Choose Branch</option>
                                    @foreach($branch as $id => $branchName)
                                    <option value="{{ $id }}"{{ old('branch_id') == $id ? 'selected' : '' }}>
                                        {{ $branchName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="search-sec col-sm-12 float-left" style="display:flex;align-items:center;">
                        <div class="col-sm-12 col-md-5 float-left card mt-3">
                            <div class="card-header" style="padding-top: 10px; padding-bottom:10px">
                                <h6 class="mb-0 card-title" style="width:95%">Employees </h6>
                            </div>
                            <div class="content vscroll h-200 mt-1">
                                <div class="table-responsive" id="employee_list">
                                    <table class="w-100">
                                        <thead>
                                            <tr>
                                                <th style="width:5%">#</th>
                                                <th style="width:55%;">Employees</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employees as $employee)
                                            <tr>
                                                <td style="vertical-align:top;">
                                                    <input type="checkbox" name="selected_staff[]" >
                                                </td>
                                                <td style="padding-top:3px;">
                                                    
                                                </td>
                                            </tr>
                                        @endforeach
                                        
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-2 float-left waves_btn_sec " style="">
                            <div class="btn_wrap" style="text-align:center;">
                                <button type="button" onclick="transferEmployees()" class="btn btn-info waves-effect waves-light" tabindex="4"> >> </button>
                            </div>
                            <div class="btn_wrap mt-7" style="text-align:center;">
                                <button type="button" onclick=" transferEmployeesBack()" id="transfer_back_button" ng-click="UnAssignActivity()" my-click-once class="btn btn-info waves-effect waves-light" tabindex="5"> << </button>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-5 float-left card mt-3">
                            <div class="card-header" style="padding-top: 10px; padding-bottom:10px">
                                <h6 class="mb-0 card-title" style="width:95%">Transferred  Employees</h6>
                            </div>
                            <div class="content vscroll h-200">
                                <div class=" table-responsive "  id="transferred_employee_list" ng-show="CategoryActivityList.length>0">
                                    <table class="w-100">
                                        <thead>
                                            <tr>
                                                <th style="width:5%">#</th>
                                                <th style="width:55%;">Employees</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr ng-repeat="categoryactivity in CategoryActivityList">
                                                <td style="vertical-align:top;"></td>
                                                <td style="padding-top:3px;vertical-align:top;">
                                                    <input type="checkbox" ng-model="active" ng-change="ChangeCategoryActivity(categoryactivity, active)" tabindex="3" class="chck_btn" style="display:inline;">
                                                    <label class="ng-binding" style="text-align:left;display:inline;"></label>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div>
                            <button type="submit" class="btn btn-raised btn-primary">
                                <i class="fa fa-check-square-o"></i>Save
                            </button>
                            <a class="btn btn-primary" href="{{ route('branchTransfer.index') }}">
                                <i class="fa fa-times" aria-hidden="true"></i> Close
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- <script>
    function transferEmployees() {
        var selectedEmployees = [];

        // Iterate through selected checkboxes and push their values (employee IDs) to the array
        $("input[type=checkbox]:checked").each(function () {
            selectedEmployees.push($(this).val());
        });

        // Set the hidden input value with the selected employees' IDs
        $("#selected-employees").val(selectedEmployees);
    }
</script> --}}
<script>
    // This function will be triggered when the "From Branch" selection changes
    function loadEmployees() {
        var fromBranchId = $("#from_branch").val();

        // Make an AJAX request to fetch employees based on the selected branch
        $.ajax({
            url: '/get-employees/' + fromBranchId,
            method: 'GET',
            success: function(data) {
                // Clear the current list of employees
                $("#employee_list").empty();


                // Append the fetched employees to the list
                $.each(data, function(index, employee) {
                    console.log(employee);
                    var checkbox = $('<input type="checkbox" onclick="AddEmployee('+employee.staff_name+')" class="chck_btn" style="display:inline;">');
                    var label = $('<label class="ng-binding" style="display:inline;">').text(employee.staff_name);
                    var row = $('<tr>').append($('<td>').append(checkbox, label));
                    $("#employee_list").append(row);
                });
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(xhr.responseText);
            }
        });
        $("#transferred_employee_list").empty();
    }
   

    function transferEmployees() {
    // Get the selected employees from the "From Branch" container
    var selectedEmployees = [];
    $("input[type=checkbox]:checked", "#employee_list").each(function () {
        var checkboxValue = $(this).val();
        var labelText = $(this).closest('tr').find('label').text();
        
        selectedEmployees.push({ value: checkboxValue, label: labelText });

        // Remove the selected employee row from the "From Branch" container
        $(this).closest('tr').remove();
    });

    // Append the selected employees to the "To Branch" container
    $.each(selectedEmployees, function(index, employee) {
        var checkbox = $('<input type="checkbox" class="chck_btn" style="display:inline;">').val(employee.value);
        var label = $('<label class="ng-binding" style="display:inline;">').text(employee.label);

        var row = $('<tr>').append($('<td>').append(checkbox, label));
        $("#transferred_employee_list").append(row);
    });
}



    function transferEmployeesBack() {
    // Get the selected employees from the "Transferred Employees" container
    var selectedEmployees = [];
    $("#transferred_employee_list input[type=checkbox]:checked").each(function () {
        selectedEmployees.push($(this).closest('tr')); // Store the whole row
    });

    // Append the selected employees back to the "Employees" container
    $.each(selectedEmployees, function (index, employeeRow) {
        var checkbox = $('<input type="checkbox" class="chck_btn" style="display:inline;">').val(employeeRow.find('input[type=checkbox]').val());
        var label = $('<label class="ng-binding" style="display:inline;">').text(employeeRow.find('label').text());
            var row = $('<tr>').append($('<td>').append(checkbox, label));
            $("#employee_list").append(row);

        // Remove the selected employee row from the "Transferred Employees" container
        employeeRow.remove();
    });
}

// Event handling to trigger the transfer back operation
$(document).ready(function () {
    $("#transfer_back_button").click(function () {
        transferEmployeesBack();
    });
});

</script>

@endsection
