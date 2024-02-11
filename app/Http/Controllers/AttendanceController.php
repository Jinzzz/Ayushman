<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff_Leave;
use App\Models\Mst_Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class AttendanceController extends Controller
{
    public function viewAttendance()
    {
        $pageTitle = "Attendance View";
        $selectedMonthYear = now()->format('Y-m');
        $firstDayOfMonth = Carbon::parse($selectedMonthYear . '-01');
        if (Auth::user()->user_type_id == 1) {
        $staffLeaves = Staff_Leave::whereYear('from_date', '=', now()->year)
        ->whereMonth('from_date', '=', now()->month)
        ->get();
        $allStaff = Mst_Staff::all();
        }else{
            $staffId = Auth::user()->staff_id;
            $branchId = Mst_Staff::where('staff_id', $staffId)->value('branch_id');
            $staffLeaves = Staff_Leave::where('branch_id', $branchId)
                        ->whereYear('from_date', '=', now()->year)
                        ->whereMonth('from_date', '=', now()->month)
                        ->get();
            $allStaff = Mst_Staff::where('branch_id', $branchId)->get();
        }
        
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        
        return view('attendance.view', compact('pageTitle', 'selectedMonthYear','staffLeaves','daysInMonth','allStaff','firstDayOfMonth'));
    }
    public function monthlyAttendance(Request $request)
    {
        $selectedMonthYear = $request->input('month_year', now()->format('Y-m'));
        $firstDayOfMonth = Carbon::parse($selectedMonthYear . '-01');
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        if (Auth::user()->user_type_id == 1) {
        $staffLeaves = Staff_Leave::whereYear('from_date', '=', now()->year)
        ->whereMonth('from_date', '=', now()->month)
        ->get();
        $absentStaffIds = Staff_Leave::where('from_date', '<=', $lastDayOfMonth)
            ->where('to_date', '>=', $firstDayOfMonth)
            ->pluck('staff_id')
            ->toArray();
        $allStaff = Mst_Staff::all();
        }else{
            $staffId = Auth::user()->staff_id;
            $branchId = Mst_Staff::where('staff_id', $staffId)->value('branch_id');
            $staffLeaves = Staff_Leave::where('branch_id', $branchId)->whereYear('from_date', '=', now()->year)
            ->whereMonth('from_date', '=', now()->month)
            ->get();
            $absentStaffIds = Staff_Leave::where('branch_id', $branchId)->where('from_date', '<=', $lastDayOfMonth)
            ->where('to_date', '>=', $firstDayOfMonth)
            ->pluck('staff_id')
            ->toArray();
            $allStaff = Mst_Staff::where('branch_id', $branchId)->get();
        }
    
        return view('attendance.view', compact('allStaff', 'absentStaffIds', 'selectedMonthYear', 'daysInMonth','firstDayOfMonth','staffLeaves'));
    }
    
    
    
    
}
