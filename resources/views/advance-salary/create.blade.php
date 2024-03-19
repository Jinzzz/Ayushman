@extends('layouts.app')
@section('content')
    <style>
        .form-control[readonly] {
            background-color: #c7c7c7 !important;
        }

        .page input[type=text][readonly] {
            background-color: #c7c7c7 !important;
        }

        .form-group .last-row {
            border-top: 1px solid #0d97c6;
            padding-top: 15px;
        }
    </style>
    <div class="container">
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">Add Advance Salary</h3>
                    </div>
                    <!-- Success message -->
                    <div class="col-lg-12 card-background" style="background-color:#fff";>
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
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Salary Month*</label>
                                        <input type="month" class="form-control" required name="salary_month"
                                            id="salary_month" placeholder="Date" value="{{ old('salary_month') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Payed Date*</label>
                                        <input type="date" id="payed_date" class="form-control" required
                                            name="payed_date" value="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Branch*</label>
                                        <select class="form-control" required name="branch_id" id="branch">
                                            <option value="">--Select Branch--</option>
                                            @foreach ($branches as $id => $branch)
                                                <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Staff*</label>
                                        <select class="form-control" required name="staff_id" id="staff">
                                            <option value="">--Select Staff--</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Payment Amount*</label>
                                        <input type="text" class="form-control paid_amount" required name="paid_amount" placeholder="Payment Amount">
                                    </div>
                                </div>
                            </div>
                            {{-- second div --}}
                            <div class="row"
                                style="border: 1px solid #0d97c6;
                            margin: 0 10px 10px;
                            background: #c5dfe84f;">
                                <div class="col-4" style="border-right: 1px solid #0d97c6;">
                                    <div class="form-group" style="margin-top: 1rem">
                                        <div class="row">
                                            {{-- <div class="col-4"><label class="form-label">Earnings</label>
                                            </div> --}}
                                            <div class="col-12">
                                                <label class="form-label">Earnings</label>
                                                <input type="text" class="form-control total_earnings" required
                                                    name="total_earnings" placeholder="Total Earnings" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4" style="border-right: 1px solid #0d97c6;">
                                    <div class="form-group" style="margin-top: 1rem">
                                        <div class="row">
                                            {{-- <div class="col-4"><label class="form-label">Deductions</label>
                                            </div> --}}
                                            <div class="col-12">
                                                <label class="form-label">Deductions</label>
                                                <input type="text" class="form-control total_deductions" required
                                                    name="total_deductions" placeholder="Total Deductions" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group" style="margin-top: 1rem">
                                        <div class="row">
                                            {{-- <div class="col-4">
                                            </div> --}}
                                            <div class="col-12">
                                                <label class="form-label">Net Earnings</label>
                                                <input type="text" class="form-control net_earnings" required
                                                    name="net_earnings" readonly placeholder="Net Earnings">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin: 0 10px 10px;">
                               {{-- payment info  --}}
                               <div class="col-md-4">
                                <div class="form-group">
                                   <label for="payment-type" class="form-label">Payed through*</label>
                                   <select class="form-control" required name="payed_through_mode" placeholder="Payed Mode" id="payed_through_mode" onchange="updateDepositTo()">
                                      <option value="">--Select--</option>
                                      @foreach($paymentType as $id => $value)
                                      <option value="{{ $id }}">{{ $value }}</option>
                                      @endforeach
                                   </select>
                                </div>
                             </div>
                             <div class="col-md-4">
                                <div class="form-group">
                                   <label class="form-label">Account ledger*</label>
                                   <select class="form-control" name="payed_through_ledger_id" id="payed_through_ledger_id" required="">
                                      <option value="">Account ledger</option>
                                   </select>
                                </div>
                             </div>
                             <div class="col-md-4">
                                <div class="form-group">
                                   <label for="payment-type" class="form-label">Payment Mode*</label>
                                   <select class="form-control" required name="payment_mode" placeholder="Payment Mode" id="payment_mode">
                                      <option value="">--Select--</option>
                                      @foreach($paymentType as $id => $value)
                                      <option value="{{ $id }}">{{ $value }}</option>
                                      @endforeach
                                   </select>
                                </div>
                             </div>
                             <div class="col-6">
                                <div class="form-group" style="margin-top: 1rem">
                                            <label class="form-label">Remarks</label>
                                            <input type="text" class="form-control remarks" required
                                                name="remarks" placeholder="Remarks"> 
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group" style="margin-top: 1rem">
                                            <label class="form-label">Reference Number</label>
                                            <input type="text" class="form-control reference_number" required
                                                name="reference_number" placeholder="Reference Number"> 
                                </div>
                            </div>
                            </div>
                           
                            <!-- ... -->
                            <div class="form-group">
                                <center>
                                    <button type="submit" class="btn btn-raised btn-primary">
                                        <i class="fa fa-check-square-o"></i> Add</button>
                                    <button type="reset" class="btn btn-raised btn-success">
                                        <i class="fa fa-refresh"></i> Reset</button>
                                    <a class="btn btn-danger" href="{{ route('advance-salary.index') }}"> <i
                                            class="fa fa-times"></i> Cancel</a>
                                </center>
                            </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        document.getElementById('numericInput').addEventListener('input', function(event) {
            let inputValue = event.target.value;
            inputValue = inputValue.replace(/[^0-9.]/g, '');
            inputValue = inputValue.replace(/(\..*)\./g, '$1');
            event.target.value = inputValue;
        });
    </script>
    <script>
        //set current date in transfer date
        $(document).ready(function() {
            var currentDate = new Date().toISOString().slice(0,10);
            $('#payed_date').val(currentDate);
            
            //payment mode
            $('#payed_through_mode').change(function() {
                var selectedPaymentMode = $(this).val();
                $.ajax({
                    url: '{{ route("getLedgerNames1") }}',
                    type: 'GET',
                    data: {
                    payment_mode: selectedPaymentMode
                    },
                    success: function(data) {
                    $('#payed_through_ledger_id').empty();
                    $('#payed_through_ledger_id').append('<option value="">Account ledger</option>');
                    $.each(data, function(key, value) {
                        $('#payed_through_ledger_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                    },
                error: function(error) {
                console.log(error);
                }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.total_earnings, .total_deductions').val('');
            $('.earnings, .deductions').hide();
            $('#salary_month').change(function() {
                var selectedMonth = $(this).val();
                //reset all values to default since leave of a staff is populated from month and selected year. thus if the user is trying to change the month in between the process there will be conflicts in calculation.
                $('#total_salary').val('');
                $('#total_leave').val('');
                $('#working_days').val('');
                $('#branch').val('');
                $('#staff').val('');
                $.ajax({
                    url: '{{ route('getWorkingDays') }}',
                    type: 'GET',
                    data: {
                        month: selectedMonth
                    },
                    success: function(response) {
                        $('#working_days').val(response.workingDays);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
    <script>
        //total salary
        $(document).ready(function() {
            function resetFields() {
                $('.earnings, .deductions').hide();
                $('.total_earnings, .total_deductions, .earning_head, .deduction_head').val('');
            }
            
            // Function to update total earnings
        function updateTotalEarnings() {
            var totalEarnings = 0;
            $('.bonus, .overtime, .other_earnings, .earning_head').each(function() {
                var amount = parseFloat($(this).val());
                if (!isNaN(amount)) {
                    totalEarnings += amount;
                }
            });
            $('.total_earnings').val(totalEarnings.toFixed(2));
        }

        // Function to update total deductions
        function updateTotalDeductions() {
            var totalDeductions = 0;
            $('.lop, .other_deductions, .deduction_head').each(function() {
                var amount = parseFloat($(this).val());
                if (!isNaN(amount)) {
                    totalDeductions += amount;
                }
            });
            $('.total_deductions').val(totalDeductions.toFixed(2));
        }
        
            $('#staff').change(function() {
                resetFields();
                var staffId = $(this).val();
                var selectedMonth = $('#salary_month').val();
                if (staffId) {
                    $.ajax({
                        url: '/getStaffSalary/' + staffId,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $('#total_salary').val(data.total_salary);
                        }
                    });
                    //get total leaves took by the staff in the selected month
                    if (selectedMonth) {
                        $.ajax({
                            url: '/getStaffLeaves/' + staffId + '/' + selectedMonth,
                            type: "GET",
                            dataType: "json",
                            success: function(data) {
                                $('#total_leave').val(data.total_leave);
                            }
                        });
                        //deductible leaves
                        $.ajax({
                            url: '/getDeductibleLeaveCount/' + staffId + '/' + selectedMonth,
                            type: "GET",
                            dataType: "json",
                            success: function(data) {
                                $('#deductible_leave_count').val(data.deductible_leave_count);
                            },
                            error: function() {
                                console.log('Error fetching deductible leave count');
                            }
                        });
                    } else {
                        $('#total_leave').val('');
                        $('#deductible_leave_count').val('');
                        alert('Please choose a salary month.');
                    }

                    //earnings and deduction listing
                    $.ajax({
                        url: '/get-salary-heads/' + staffId,
                        type: 'GET',
                        dataType: "json",
                        success: function(response) {
                            $('.earnings, .deductions').hide();
                            // Show earnings and calculate total earnings
                                $.each(response.earnings, function(index, value) {
                                    var inputField = $('input[name="earning_head"][placeholder="' + value.salary_head_name + '"]');
                                    inputField.val(value.amount).closest('.form-group').show();
                                });
                                // Update total earnings when staff is selected
                                updateTotalEarnings();

                                // Show deductions and calculate total deductions
                                $.each(response.deductions, function(index, value) {
                                    var inputField = $('input[name="deduction_head"][placeholder="' + value.salary_head_name + '"]');
                                    inputField.val(value.amount).closest('.form-group').show();
                                });
                                // Update total deductions when staff is selected
                                updateTotalDeductions();
                        }
                    });

                } else {
                    $('#total_salary').val('');
                    $('#total_leave').val('');
                    $('#deductible_leave_count').val('');
                }
            });
            // Listen for changes in bonus, overtime, and other earnings fields
            $('.bonus, .overtime, .other_earnings').on('input', function() {
                        updateTotalEarnings();
                    });

                    // Listen for changes in lop and other deductions fields
                    $('.lop, .other_deductions').on('input', function() {
                        updateTotalDeductions();
                    });
        });
    </script>


    <script>
        $(document).ready(function() {
            $('#branch').change(function() {
                var branchId = $(this).val();
                if (branchId) {
                    $.ajax({
                        url: '/getStaffs/' + branchId,
                        type: 'GET',
                        success: function(response) {
                            $('#staff').empty();
                            $('#staff').append('<option value="">--Select Staff--</option>');
                            $.each(response, function(key, value) {
                                $('#staff').append('<option value="' + value.staff_id +
                                    '">' + value.staff_name + ' (' + value
                                    .staff_code + ')' + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    $('#staff').empty();
                    $('#staff').append('<option value="">--Select Staff--</option>');
                }
            });
        });
    </script>
@endsection
