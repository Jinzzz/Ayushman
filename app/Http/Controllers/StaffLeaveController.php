<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff_Leave;
use Illuminate\Support\Facades\Auth;
use App\Models\Mst_Branch;
use App\Models\Mst_Staff;
use App\Models\Mst_Leave_Type;
class StaffLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageTitle = "Admin Leave Request";
        $query = Staff_Leave::query();
        $staffleaves = Staff_Leave::select('staff_leave.*', 'mst_staffs.staff_name as staff_name', 'mst_branches.branch_name')
        ->join('mst_staffs', 'staff_leave.staff_id', '=', 'mst_staffs.staff_id')
        ->join('mst_branches', 'mst_staffs.branch_id', '=', 'mst_branches.branch_id')
        ->orderBy('staff_leave.updated_at', 'desc');
    
    // Apply filters if provided
    if ($request->has('staff_name')) {
        $staffleaves->where('mst_staffs.staff_name', 'LIKE', "%{$request->staff_name}%");
    }
    
    if ($request->has('from_date')) {
        $staffleaves->where('staff_leave.from_date', 'LIKE', "%{$request->from_date}%");
    }
    
    if ($request->has('to_date')) {
        $staffleaves->where('staff_leave.to_date', 'LIKE', "%{$request->to_date}%");
    }

    
    $staffleaves = $staffleaves->get();
    
    // Rest of your code...
    
                       

        return view('staffleave.index', compact('pageTitle', 'staffleaves'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = "Create Leave Request";
        $branches = Mst_Branch::get();
        $leave_types = Mst_Leave_Type::where('is_active', 1)->get();
        return view('staffleave.create', compact('pageTitle','branches','leave_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
  
        $request->validate([
            'branch_id' => 'required',
            'staff_id' => 'required',
            'from_date' => 'required',
            'from_date' => 'required|date|after_or_equal:' . today(),
            'to_date' => 'required|date|after_or_equal:from_date',
            'end_day' => 'required',
            'days' => 'required',
            'leave_type' => 'required',
            'reason' => 'required',
        ], [
            'branch_id.required' => 'The branch field is required.',
            'staff_id.required' => 'The staff field is required.',
            'from_date.required' => 'The from date field is required.',
            'start_day.required' => 'The start day field is required.',
            'to_date.required' => 'The to date field is required.',
            'end_day.required' => 'The end day field is required.',
            'days.required' => 'The days field is required.',
            'leave_type.required' => 'The leave type field is required.',
            'reason.required' => 'The reason field is required.',
        ]);
    
        // Rest of your code...
    
        $lastInsertedId = Staff_Leave::create([
            'branch_id' => $request->branch_id,
            'staff_id' => $request->staff_id,
            'leave_type' => $request->leave_type,
            'days' => $request->days,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'reason' => $request->reason,
            'start_day' => $request->start_day,
            'end_day' => $request->end_day,
        ]);
    
        return redirect()->route('staffleave.index')->with('success', 'Leave Request added successfully');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = "View Leave Request";
        $show = Staff_Leave::select(
            'staff_leave.*',
            'mst_staffs.staff_name as staff_name',
            'mst_branches.branch_name',
            'mst_leave_types.name as leave_type_name'
        )
        ->join('mst_staffs', 'staff_leave.staff_id', '=', 'mst_staffs.staff_id')
        ->join('mst_branches', 'mst_staffs.branch_id', '=', 'mst_branches.branch_id')
        ->leftJoin('mst_leave_types', 'staff_leave.leave_type', '=', 'mst_leave_types.leave_type_id')
        ->where('staff_leave.id', $id)
        ->orderBy('staff_leave.updated_at', 'desc')
        ->first();
    
        return view('staffleave.show', compact('show','pageTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $pageTitle = "Edit Leave Request";
            $leave_request = Staff_Leave::select(
                'staff_leave.*',
                'mst_staffs.staff_name as staff_name',
                'mst_branches.branch_name',
                'mst_leave_types.name as leave_type_name'
            )
            ->join('mst_staffs', 'staff_leave.staff_id', '=', 'mst_staffs.staff_id')
            ->join('mst_branches', 'mst_staffs.branch_id', '=', 'mst_branches.branch_id')
            ->leftJoin('mst_leave_types', 'staff_leave.leave_type', '=', 'mst_leave_types.leave_type_id')
            ->where('staff_leave.id', $id)
            ->orderBy('staff_leave.updated_at', 'desc')
            ->first();
            $leave_types = Mst_Leave_Type::where('is_active', 1)->get();
            return view('staffleave.edit', compact('pageTitle', 'leave_request','leave_types'));
        } catch (QueryException $e) {
            return redirect()->route('staffleave.index')->with('error', 'Something went wrong');
        }
    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            // Add your validation rules here based on your requirements
            'from_date' => 'required|date|after_or_equal:' . today(),
            'to_date' => 'required|date|after_or_equal:from_date',
            'start_day' => 'required',
            'end_day' => 'required',
            'days' => 'required|numeric',
            'leave_type' => 'required',
            'reason' => 'required',
        ]);

        // Find the leave request by ID
        $leaveRequest = Staff_Leave::findOrFail($id);

        // Update the leave request with the new data
        $leaveRequest->update([
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
            'start_day' => $request->input('start_day'),
            'end_day' => $request->input('end_day'),
            'days' => $request->input('days'),
            'leave_type' => $request->input('leave_type'),
            'reason' => $request->input('reason'),
            // Add other fields as needed
        ]);

        // Redirect back to the leave request edit page with a success message
        return redirect()->route('staffleave.index')->with('success', 'Leave request updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leaverequest = Staff_Leave::findOrFail($id);

        // Soft delete the record
        $leaverequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Leave Request deleted successfully',
        ]);
    }

    public function getStaffNames($branchId)
    {
        $staffNames = Mst_Staff::where('branch_id', $branchId)->pluck('staff_name', 'staff_id');

        return response()->json($staffNames);
    }
}
