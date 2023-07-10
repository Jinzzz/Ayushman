<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Mst_LeaveType;
use App\Models\Trn_StaffLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    public function viewApplyLeave(Request $request)
    {
       $pageTitle='Apply Leave';
       $leave_types= Mst_LeaveType::where('leave_type_status',1)->get();
       return view('elements.leave.apply-leave',compact('leave_types','pageTitle'));

    }
    public function submitLeave(Request $request)
    {
        try{
            //dd($request->leave_type);
            $validator = Validator::make(
                $request->all(),
                [
                    'leave_type'          => 'required',
                    'leave_duration'          => 'required',
                    'leave_date' => 'required|date',
                    'leave_reason'=>'required',
                   
    
    
                ],
                [
                    'leave_type.required'=>'Leave type is required',
                    'leave_duration.required'=>'Duration is required',
                    'leave_date.required'=>'Leave date is required',
                    'leave_reason.required'=>'Leave reason is required',
                   
    
                ]
            );
           
            if (!$validator->fails()) {
                $leave_exists=Trn_StaffLeave::where('leave_date',$request->leave_date)->where('user_id',Auth::id())->exists();
                if($leave_exists)
                {
                    return redirect()->back()->with('error','Already applied a leave for '.date('d-M-y',strtotime($request->leave_date)))->withInput();

                }
                else{
                $leave=new Trn_StaffLeave();
                $leave->leave_type_id=$request->leave_type;
                $leave->user_id=Auth::id();
                $leave->leave_duration=$request->leave_duration;
                $leave->leave_reason=$request->leave_reason;
                $leave->branch_id=Auth::user()->branch_id;
                $leave->leave_date=$request->leave_date;
                $leave->leave_status=1;
                $leave->created_by=Auth::id();
                $leave->save();
                
                return redirect()->back()->with('status','Leave Applied successfully');
                }

    
            }
            else
            {
                return redirect()->back()->withErrors($validator->errors())->withInput();
    
            }
        }
        catch (\Exception $e) {
                $response = ['status' => '0', 'message' => $e->getMessage()];
                return response($response);
            } catch (\Throwable $e) {
                $response = ['status' => '0', 'message' => $e->getMessage()];
                return response($response);
            }

    }
    public function leaveHistory(Request $request)
    {
        $pageTitle="Leave History";
        $dateFrom=null;
        $dateTo=null;
        $leave_types= Mst_LeaveType::where('leave_type_status',1)->get();
        $leaves=Trn_StaffLeave::orderBy('leave_id','DESC')->where('user_id',Auth::id());
        if($request->leave_type_id)
        {
            $leaves=$leaves->where('leave_type_id',$request->leave_type_id);
        }
        if($request->leave_status_id)
        {
            $leaves=$leaves->where('leave_status',$request->leave_status_id);
        }
        if($request->from_date)
        {
            $dateFrom=$request->from_date;
            $leaves=$leaves->whereDate('leave_date','>=',$request->from_date);
        }
        if($request->to_date)
        {
            $dateTo=$request->to_date;
            $leaves=$leaves->whereDate('leave_date','<=',$request->to_date);
        }
        $leaves=$leaves->get();

        return view('elements.leave.leave-history',compact('pageTitle','leaves','leave_types','dateFrom','dateTo'));

    }
}
