<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mst_Staff;
use App\Models\EmployeeAvailableLeave;

class AvailableLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageTitle = "Employee Available Leave";
        $employees = Mst_Staff::get();
    
        $query = EmployeeAvailableLeave::select('mst_staffs.staff_name', 'employee_available_leaves.*')
            ->leftJoin('mst_staffs', 'employee_available_leaves.staff_id', '=', 'mst_staffs.staff_id');
    
        // Apply filters if provided
        if ($request->has('staff_name')) {
            $query->where('mst_staffs.staff_name', 'LIKE', "%{$request->staff_name}%");
        }
    
        $availableleaves = $query->orderBy('employee_available_leaves.updated_at', 'desc')->get();
        
        return view('availableleaves.index', compact('pageTitle', 'availableleaves', 'employees'));
    }
     

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = "Create Employee Leave";
        $employees = Mst_Staff::get();
 
        return view('availableleaves.create', compact('pageTitle','employees'));
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
                'staff_id' => 'required',
                'remark' => 'required',
                'total_leaves' => 'required|numeric',
            ]);
    
            // Check staff ID already exists
            $existingRecord = EmployeeAvailableLeave::where('staff_id', $request->input('staff_id'))->first();
            if ($existingRecord) {
               $message = 'Employee leave Already Exist!';
            } else {
                $availableLeave = new EmployeeAvailableLeave([
                    'staff_id' => $request->input('staff_id'),
                    'remark' => $request->input('remark'),
                    'total_leaves' => $request->input('total_leaves'),
                ]);
                $availableLeave->save();
                $message = 'Employee leave added successfully!';
            }

            return redirect()->route('availableleaves.index')->with('success', $message);
        }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = "View Employee Leave Details";
        $show = EmployeeAvailableLeave::select('mst_staffs.*', 'employee_available_leaves.*')
                                        ->leftJoin('mst_staffs', 'employee_available_leaves.staff_id', '=', 'mst_staffs.staff_id')
                                        ->where('employee_available_leaves.id', $id)->first();

        return view('availableleaves.show', compact('show', 'pageTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        try
        {
            $pageTitle = "Edit Employee Available Leave";
            $availableleaves = EmployeeAvailableLeave::select('mst_staffs.*', 'employee_available_leaves.*')
                      ->leftJoin('mst_staffs', 'employee_available_leaves.staff_id', '=', 'mst_staffs.staff_id')
                      ->where('employee_available_leaves.id', $id)->first();
            $employees = Mst_Staff::get();
            return view('availableleaves.edit', compact('pageTitle', 'availableleaves', 'employees', 'id'));
        }
        catch(QueryException $e)
        {
            return redirect()->route('availableleaves.index')->with('error', 'Something went wrong');
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
        // Validate the request data
        $request->validate([
            'total_leaves' => 'required|numeric',
            'remark' => 'required',
            'staff_id' => 'required',
        ]);

        try {
            $availableLeave = EmployeeAvailableLeave::where('staff_id', $id)->firstOrFail();
            $availableLeave->total_leaves = $request->input('total_leaves');
            $availableLeave->remark = $request->input('remark');
            $availableLeave->save();

            return redirect()->route('availableleaves.index')->with('success', 'Available leave updated successfully');
        } 
        catch (\Exception $e) {

            return redirect()->back()->with('error', 'Error updating available leave: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
