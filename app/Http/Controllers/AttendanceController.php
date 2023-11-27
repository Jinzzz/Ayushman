<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff_Leave;
use App\Models\Mst_Staff;
use Carbon\Carbon;
class AttendanceController extends Controller
{
    public function viewAttendance()
    {
        $pageTitle = "Attendance View";
        $selectedMonthYear = now()->format('Y-m');
        $staffLeaves = Staff_Leave::whereYear('from_date', '=', now()->year)
        ->whereMonth('from_date', '=', now()->month)
        ->get();
        $allStaff = Mst_Staff::all();
        $firstDayOfMonth = Carbon::parse($selectedMonthYear . '-01');
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        
        return view('attendance.view', compact('pageTitle', 'selectedMonthYear','staffLeaves','daysInMonth','allStaff','firstDayOfMonth'));
    }
    public function monthlyAttendance(Request $request)
    {
        $selectedMonthYear = $request->input('month_year', now()->format('Y-m'));
        $firstDayOfMonth = Carbon::parse($selectedMonthYear . '-01');
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
        $daysInMonth = $firstDayOfMonth->daysInMonth;
    
        // Fetch absent staff IDs during the specified time period
        $absentStaffIds = Staff_Leave::where('from_date', '<=', $lastDayOfMonth)
            ->where('to_date', '>=', $firstDayOfMonth)
            ->pluck('staff_id')
            ->toArray();
    
        // Assuming you have a Staff model and you want to retrieve all staff
        $allStaff = Mst_Staff::all();
    
        return view('attendance.view', compact('allStaff', 'absentStaffIds', 'selectedMonthYear', 'daysInMonth'));
    }
    
    
    
    
}
