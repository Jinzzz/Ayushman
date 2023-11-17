<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffLeaveController extends Controller
{
   public function staffLeaveCalendar(Request $request)
    {
        $data = array();
        $pageNumber = $request->input('page', 1);
        $data['status'] = 1;
        $data['message']='Data fetched';
        $staff_leaves=DB::table('trn_staff_leaves')
                    ->leftjoin('mst_staffs','mst_staffs.staff_id', '=', 'trn_staff_leaves.user_id')
                    ->leftjoin('mst_master_values as leave_type','leave_type.id', '=', 'trn_staff_leaves.leave_type_id')          
                    ->select('mst_staffs.staff_name','leave_type.master_value','trn_staff_leaves.leave_duration','trn_staff_leaves.leave_date');
        if(isset($request->leave_date))  
        {
          $staff_leaves=$staff_leaves->whereDate('trn_staff_leaves.leave_date',$request->leave_date);
        }
        else
        {
          $staff_leaves=$staff_leaves->whereMonth('trn_staff_leaves.leave_date',Carbon::now()->month);
        }
        $total_records = $staff_leaves->count(); // Get total record count before pagination

        $staff_leaves=$staff_leaves->paginate(30, ['*'], 'page', $pageNumber);
        $staff_leaves=$staff_leaves->items();
        
        foreach($staff_leaves as $staff_leave)
        {
            $leave_duration=DB::table('mst_master_values')->where('id',$staff_leave->leave_duration)->first()->master_value;
            $staff_leave->leave_duration=$leave_duration;

        }
        $data['staff_leaves']=$staff_leaves;
        $data['total']=$total_records;
        $data['per_page_count']=30;
        
       
        return response($data);
       
    }
    public function leaveCalendarCount(Request $request)
{
        $data=array();
        $month = $request->input('month');
        $year = $request->input('year');

        // Check if both month and year are provided
        if (!$month || !$year) {
            return response()->json(['status'=>0,'message' => 'Both month and year are required'], 400);
        }

        // Validate the month (1 to 12) and year (4 digits)
        if (!checkdate($month, 1, $year) || !preg_match('/^\d{4}$/', $year)) {
            return response()->json(['status'=>0,'message' => 'Invalid month or year format'], 400);
        }

        // Calculate the number of days in the given month and year
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // Generate the list of dates
        $dates = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dates[] = Carbon::createFromDate($year, $month, $day)->toDateString();
        }
        $calendar=[];
        $i=0;
        foreach($dates as $date)
        {
          $calendar[$i]['date']=$date;
          $calendar[$i]['count']=DB::table('trn_staff_leaves')->whereDate('leave_date',$date)->count();
          $i++;
        }
        $data['status']=1;
        $data['message']="Fetched";
        $data['dateList']=$calendar;
        return response($data);
}

    
}
