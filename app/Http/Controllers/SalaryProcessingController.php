<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trn_staff_salary_processing;
use App\Models\Trn_staff_salary_processing_detail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Holiday;
use App\Models\Mst_Branch;
use App\Models\Mst_Staff;
use App\Models\Mst_Salary;
use App\Models\Staff_Leave;
use App\Models\Mst_Leave_Type;
use App\Models\Salary_Head_Master;
use App\Models\Trn_Staff_Advance_Salary;
use App\Models\Mst_Master_Value;

class SalaryProcessingController extends Controller
{
    public function index(Request $request)
    {
        return view('salary-processing.index', [
            'processDatas' => Trn_staff_salary_processing::orderBy('created_at','DESC')->get(),
            'pageTitle' => 'Salary Processing'
        ]);
    }

    public function create(Request $request)
    {
        return view('salary-processing.create', [
            'branches' => Mst_Branch::where('is_active','=',1)->orderBy('branch_name','ASC')->get(),
            'earnings' => Salary_Head_Master::where('salary_head_type','=',1)->get(),
            'deductions' => Salary_Head_Master::where('salary_head_type','=',2)->get(),
            'pageTitle' => 'Add Salary Processing'
        ]);
    }

    public function getWorkingDays(Request $request)
    {
        $selectedMonth = Carbon::parse($request->input('month'));
        $totalDays = $selectedMonth->daysInMonth;
        $holidays = Holiday::where(function ($query) use ($selectedMonth) {
            $query->where('year', '=', $selectedMonth->year)->whereMonth('from_date', '=', $selectedMonth->month);
        })
        ->orWhere(function ($query) use ($selectedMonth) {
            $query->where('year', '=', $selectedMonth->year)->whereMonth('to_date', '=', $selectedMonth->month);
        })
        ->orWhere(function ($query) use ($selectedMonth) {
            $query->where('from_date', '<=', $selectedMonth->endOfMonth())->where('to_date', '>=', $selectedMonth->startOfMonth());
        })
        ->get();
        $totalLeaveDays = 0;
        foreach ($holidays as $holiday) {
            $fromDate = Carbon::parse($holiday->from_date);
            $toDate = Carbon::parse($holiday->to_date);
            $leaveDays = $toDate->diffInDays($fromDate) + 1;
            if ($fromDate->month === $selectedMonth && $toDate->month === $selectedMonth) 
                {
                    $totalLeaveDays += $leaveDays;
                } 
                elseif ($fromDate->month === $selectedMonth) 
                    {
                    $totalLeaveDays += $toDate->diffInDays($selectedMonth->startOfMonth()) + 1; 
                    } 
                elseif ($toDate->month === $selectedMonth) 
                    {
                        $totalLeaveDays += $selectedMonth->endOfMonth()->diffInDays($fromDate) + 1; 
                    } 
            else {
                $totalLeaveDays += $leaveDays;
            }
        }
        $workingDays = $totalDays - $totalLeaveDays;
        return response()->json(['workingDays' => $workingDays]);
    }

    public function getStaffs($branch_id)
    {
        $staffs = Mst_Staff::where('branch_id', $branch_id)->select('staff_id','staff_name','staff_code')->get();
        return response()->json($staffs);
    }

    public function getStaffSalary($staff_id)
    {
        $totalSalary = Mst_Salary::where('staff_id', $staff_id)->where('salary_head_type','Earning')->sum('amount');
        return response()->json([
            'total_salary' => $totalSalary,
        ]);
    }

    public function getStaffLeaves($staff_id, $month)
    {

        $yearMonth = explode('-', $month);
        $year = $yearMonth[0];
        $month = $yearMonth[1];
        $totalLeaves = Staff_Leave::where('staff_id', $staff_id)->whereYear('created_at', $year)->whereMonth('created_at', $month)->sum('days');
        return response()->json([
            'total_leave' => $totalLeaves
        ]);
    }

    public function getDeductibleLeaveCount($staffId, $month)
    {
        $yearMonth = explode('-', $month);
        $year = $yearMonth[0];
        $month = $yearMonth[1];
        $leaves = Staff_Leave::where('staff_id', $staffId)->whereYear('created_at', $year)->whereMonth('created_at', $month)->get();
        $deductibleLeaveCount = 0;
        foreach($leaves as $leave) {
            $leaveType = Mst_Leave_Type::find($leave->leave_type);
            if($leaveType && $leaveType->is_dedactable == 1) {
                $deductibleLeaveCount++;
            }
        }
        
        return response()->json(['deductible_leave_count' => $deductibleLeaveCount]);
    }

    
    public function getSalaryHeads($staffId, Request $request)
    {
        $earnings = Mst_Salary::where('staff_id', $staffId)
            ->leftJoin('salary_head_masters', 'mst_salary.salary_head', '=', 'salary_head_masters.id')
            ->where('mst_salary.salary_head_type', 1)
            ->get(['salary_head_masters.salary_head_name', 'mst_salary.amount']);

        $deductions = Mst_Salary::where('staff_id', $staffId)
            ->leftJoin('salary_head_masters', 'mst_salary.salary_head', '=', 'salary_head_masters.id')
            ->where('mst_salary.salary_head_type', 2)
            ->get(['salary_head_masters.salary_head_name', 'mst_salary.amount']);

        return response()->json([
            'earnings' => $earnings,
            'deductions' => $deductions
        ]);
    }

    
    public function AdvanceSalaryIndex(Request $request)
    {
        return view('advance-salary.index', [
            'processDatas' => Trn_Staff_Advance_Salary::orderBy('created_at','DESC')->get(),
            'pageTitle' => 'Advance Salary'
        ]);
    }
    
    public function AdvanceSalaryCreate(Request $request)
    {
        return view('advance-salary.create', [
            'branches' => Mst_Branch::where('is_active','=',1)->orderBy('branch_name','ASC')->get(),
            'paymentType' => Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id'),
            'pageTitle' => 'Add Advance Salary'
        ]);
    }



}
