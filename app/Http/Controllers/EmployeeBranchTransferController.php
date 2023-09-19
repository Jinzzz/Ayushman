<?php

namespace App\Http\Controllers;

use App\Models\Mst_Branch;
use App\Models\Mst_Staff;
use App\Models\Mst_Staff_Transfer_Log;
use Illuminate\Http\Request;

class EmployeeBranchTransferController extends Controller
{
    public function index()
    {
        $pageTitle = "Employee branch transfer";
        $branch = Mst_Branch::pluck('branch_name','branch_id');
        $employees = Mst_Staff::pluck('staff_name','staff_id');
        return view('staffbranchTransfer.index',compact('pageTitle','branch','employees'));
    }
    public function getEmployees($branchId)
    {
        $employees = Mst_Staff::where('branch_id', $branchId)->get();
        return response()->json($employees);
    }


   public function store(Request $request)
{
     // Validate the request data
     $request->validate([
        'from_branch' => 'required|exists:mst_branch,branch_id',
        'to_branch' => 'required|exists:mst_branch,branch_id',
        'selected_staff' => 'required|array', // Assuming 'selected_staff' is the name of the checkbox input
        'selected_staff.*' => 'exists:mst_staff,staff_id',
    ]);

    $fromBranchId = $request->input('from_branch');
    $toBranchId = $request->input('to_branch');
    $selectedStaff = $request->input('selected_staff');

    // Loop through selected staff and store transfer records
    foreach ($selectedStaff as $staffId) {
        // Create a new staff transfer log entry
        Mst_Staff_Transfer_Log::create([
            'staff_id' => $staffId,
            'from_branch_id' => $fromBranchId,
            'to_branch_id' => $toBranchId,
        ]);

       //update the staff's branch in the 'mst_staff' table
        Mst_Staff::where('staff_id', $staffId)->update(['branch_id' => $toBranchId]);
    }

    return redirect()->route('transfer.index')->with('success', 'Employees transferred successfully.');
}

}
