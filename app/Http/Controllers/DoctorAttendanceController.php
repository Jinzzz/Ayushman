<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff_Leave;
use App\Models\Mst_Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DoctorAttendanceController extends Controller
{
    public function viewAttendance()
    {
        $pageTitle = "Attendance View";
        $staffId = Auth::id();

        $selectedMonthYear = now()->format('Y-m');
        $firstDayOfMonth = Carbon::parse($selectedMonthYear . '-01');
        $staffLeaves = Staff_Leave::whereYear('from_date', '=', now()->year)
        ->whereMonth('from_date', '=', now()->month)
        ->where('staff_id', $staffId)
        ->get();
 
        $allStaff = Mst_Staff::join('mst_users', 'mst_users.staff_id', '=', 'mst_staffs.staff_id')
               ->select('mst_staffs.date_of_join','mst_staffs.staff_username','mst_users.user_id')
               ->where('mst_users.user_id', $staffId)
                  ->get();
            
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        
        return view('doctor-attendance.view', compact('pageTitle', 'selectedMonthYear','staffLeaves','daysInMonth','allStaff','firstDayOfMonth'));
    }
    public function monthlyAttendance(Request $request)
    {
        $selectedMonthYear = $request->input('month_year', now()->format('Y-m'));
        $firstDayOfMonth = Carbon::parse($selectedMonthYear . '-01');
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $staffId = Auth::id();
        // Fetch staff leaves for the selected month and year
        $staffLeaves = Staff_Leave::whereYear('from_date', '=', $firstDayOfMonth->year)
            ->whereMonth('from_date', '=', $firstDayOfMonth->month)
            ->where('staff_id', $staffId)
            ->get();
    
        // Fetch absent staff IDs during the specified time period
        $absentStaffIds = Staff_Leave::where('from_date', '<=', $lastDayOfMonth)
            ->where('to_date', '>=', $firstDayOfMonth)
            ->where('staff_id', $staffId)
            ->pluck('staff_id')
            ->toArray();
    
        // Assuming you have a Staff model and you want to retrieve all staff
        $allStaff = Mst_Staff::join('mst_users', 'mst_users.staff_id', '=', 'mst_staffs.staff_id')
               ->select('mst_staffs.date_of_join','mst_staffs.staff_username','mst_users.user_id')
               ->where('mst_users.user_id', $staffId)
                  ->get();
    
        return view('doctor-attendance.view', compact('allStaff', 'absentStaffIds', 'selectedMonthYear', 'daysInMonth', 'firstDayOfMonth', 'staffLeaves'));
    }
    
    
    
}
