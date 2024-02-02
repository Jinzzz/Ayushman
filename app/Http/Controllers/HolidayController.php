<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;
use App\Models\Mst_Leave_Type;
use App\Models\Mst_Master_Value;
use App\Models\HolidayMapping;
use DB;
use Illuminate\Support\Facades\Log;
class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageTitle = "Holidays";
        $query = Holiday::select('holidays.*', 'mst_leave_types.name as leave_type_name')
            ->leftJoin('mst_leave_types', 'holidays.leave_type', '=', 'mst_leave_types.leave_type_id')
            ->whereNull('holidays.deleted_at'); // Exclude records where deleted_at is not null
        
        // Apply filters if provided
        if ($request->has('holiday_name')) {
            $query->where('holidays.holiday_name', 'LIKE', "%{$request->holiday_name}%");
        }
        
        if ($request->has('from_date')) {
            $query->where('holidays.from_date', 'LIKE', "%{$request->from_date}%");
        }
        
        if ($request->has('to_date')) {
            $query->where('holidays.to_date', 'LIKE', "%{$request->to_date}%");
        }
        
        if ($request->has('year')) {
            $query->where('holidays.year', 'LIKE', "%{$request->year}%");
        }
        
        $holidays = $query->orderBy('holidays.updated_at', 'desc')->get();
        
        return view('holidays.index', compact('pageTitle', 'holidays'));
        
}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = "Create Holiday";
        $holidays = Holiday::get();
        $leave_types = Mst_Leave_Type::where('is_active', 1)->get();
        return view('holidays.create', compact('pageTitle','holidays','leave_types'));
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
            'holiday_name' => 'required',
            'from_date' => 'required|date|date_format:Y-m-d',
            'to_date' => 'required|date|date_format:Y-m-d|after_or_equal:from_date',
            'year' => 'required|numeric|digits:4',
            'leave_type' => 'required', 
            'company' => 'required',
        ], [
            'holiday_name.required' => 'The holiday name field is required.',
            'from_date.required' => 'The from date field is required.',
            'to_date.required' => 'The to date field is required.',
            'leave_type.required' => 'The leave type field is required.',
            'year.required' => 'The year field is required.',
            'company.required' => 'The company field is required.',
        ]);
    
        // Check if a record with the same data already exists
        $existingRecord = Holiday::where('holiday_name', $request->holiday_name)
                                 ->where('year', $request->year)
                                 ->where('leave_type', $request->leave_type)
                                 ->where('from_date', $request->from_date)
                                 ->where('to_date', $request->to_date)
                                 ->first();
    
        if ($existingRecord) {
            return redirect()->route('holidays.index')->with('error', 'Already exists.');
        }
    
        // Rest of your code...
    
        $lastInsertedId = Holiday::create([
            'holiday_name' => $request->holiday_name,
            'leave_type' => $request->leave_type,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'company' => $request->company,
            'year' => $request->year,
        ]);
    
        return redirect()->route('holidays.index')->with('success', 'Holiday added successfully');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
            $holiday = DB::table('holidays')
                ->select('holidays.*', 'mst_leave_types.name as leave_type_name')
                ->leftJoin('mst_leave_types', 'holidays.leave_type', '=', 'mst_leave_types.leave_type_id')
                ->where('holidays.id', $id)
                ->first();
    
            $leave_types = Mst_Leave_Type::where('is_active', 1)->get();
    
            return view('holidays.edit', compact('pageTitle', 'holiday', 'leave_types'));
        } catch (QueryException $e) {
            return redirect()->route('holidays.index')->with('error', 'Something went wrong');
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
            'holiday_name' => 'required',
            'from_date' => 'required|date|date_format:Y-m-d|',
            'to_date' => 'required|date|after_or_equal:from_date',
            'year' => 'required|numeric|digits:4',
            'leave_type' => 'required',
        ]);

        $holiday = Holiday::findOrFail($id);

        // Update 
        $holiday->update([
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
            'holiday_name' => $request->input('holiday_name'),
            'year' => $request->input('year'),
            'leave_type' => $request->input('leave_type'),
        
        ]);
        return redirect()->route('holidays.index')->with('success', 'Holiday updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        return response()->json([
            'success' => true,
            'message' => 'Holiday deleted successfully',
        ]);
    }

    public function staffHolidayMapping($id)
    {

        $pageTitle = " Assign Holiday";
        $query = Holiday::query();
        $holiday = DB::table('holidays')
                ->select('holidays.*', 'mst_leave_types.name as leave_type_name')
                ->leftJoin('mst_leave_types', 'holidays.leave_type', '=', 'mst_leave_types.leave_type_id')
                ->where('holidays.id', $id)
                ->first();
                $departments = DB::table('holiday_mappings')
                ->join('mst_master_values', 'holiday_mappings.department', '=', 'mst_master_values.id')
                ->join('holidays', 'holiday_mappings.holiday_id', '=', 'holidays.id')
                ->whereNull('holiday_mappings.deleted_at')
                ->select('holiday_mappings.id', 'holidays.holiday_name', 'mst_master_values.master_value as department_name', 'holiday_mappings.department')
                ->orderBy('holiday_mappings.created_at', 'desc') // Order by creation timestamp in descending order
                ->get();
                       

            $staff_types = Mst_Master_Value::where('master_id', 4)->get();
        return view('holidays.staff-mapping', compact('pageTitle', 'holiday','staff_types','departments'));
    }

    public function storeHolidayMapping(Request $request, $id)
    {
        $request->validate([
            'holiday_id' => 'required',
            'department' => 'required|array|min:1',
        ], [
            'department.required' => 'At least one department must be selected.',
            'department.array' => 'The selected departments must be in an array.',
        ]);
    
        $existingDepartments = [];
    
        foreach ($request->department as $selectedDepartment) {
            // Check if the record already exists
            $existingRecord = HolidayMapping::where('holiday_id', $id)
                ->where('department', $selectedDepartment)
                ->first();
    
            // If the record exists, add to the list
            if ($existingRecord) {
                $existingDepartments[] = $selectedDepartment;
            } else {
                // Record doesn't exist, create a new one
                HolidayMapping::create([
                    'holiday_id' => $id,
                    'department' => $selectedDepartment,
                ]);
            }
        }
    
        if (count($existingDepartments) > 0) {
            // Display a message for existing departments
            $warningMessage = 'Departments already exist';
            return redirect()
                ->route('holidays.index')
                ->with('error', $warningMessage)
                ->withInput();
        }
    
        return redirect()
            ->route('holidays.index')
            ->with('success', 'Departments added successfully');
    }
    

    public function destroyMapping($id)
    {
        $holiday = HolidayMapping::findOrFail($id);
        $holiday->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Department Link deleted successfully',
        ]);
    }    
    
    
}

