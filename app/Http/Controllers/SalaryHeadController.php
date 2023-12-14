<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Salary_Head_Type;
use App\Models\Salary_Head_Master;
use App\Models\Salary_Package;
class SalaryHeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
       $pageTitle = "List Salary Head";
       $branch = Salary_Head_Type::get();
        $masters = Salary_Head_Master::join('salary_head_types', 'salary_head_masters.salary_head_type', '=', 'salary_head_types.id')
                                     ->select('salary_head_masters.*', 'salary_head_types.salary_head_type')
                                     ->orderBy('salary_head_masters.updated_at', 'desc');
                                
        // Apply filters if provided
        if ($request->has('salary_head_name')) {
            $masters->where('salary_head_masters.salary_head_name', 'LIKE', "%{$request->salary_head_name}%");
        }
        
        if ($request->has('salary_head_type')) {
            $masters->where('salary_head_masters.salary_head_type', 'LIKE', "%{$request->salary_head_type}%");
        }
        
   
   // Now fetch the data
   $masters = $masters->get();

       return view('salarys.index', compact('pageTitle', 'branch','masters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = "Create Salary Head";
        $branch = Salary_Head_Type::get();
        return view('salarys.create', compact('pageTitle','branch'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        // Validate the request data
        $request->validate([
            'salary_head_name' => 'required|string|max:255',
            'salary_head_type' => 'required|exists:salary_head_types,id',
            'status' => 'required', // Assuming the checkbox value is "on" or "off"
            'remark' => 'nullable|string',
        ]);
    
        // Map the checkbox value to an integer (1 for "on", 0 for "off")
        $is_status = $request->input('status') === 'on' ? 1 : 0;
    
        // Find the existing Salary_Head_Master
        $salaryHead = Salary_Head_Master::where([
            'salary_head_name' => $request->input('salary_head_name'),
            'status' => $is_status,
        ])->first();
    
        // If it already exists, show a message and redirect
        if ($salaryHead) {
            return redirect()->route('salarys.index')->with('error', 'Salary Head already exists.');
        }
    
        // If not, create a new one
        $newSalaryHead = Salary_Head_Master::create([
            'salary_head_name' => $request->input('salary_head_name'),
            'status' => $is_status,
            'remark' => $request->input('remark'),
            'company' => 'Ayushman',
            'salary_head_type' => $request->input('salary_head_type'),
        ]);
    
        // Redirect to a specific route or page after successful creation
        return redirect()->route('salarys.index')->with('success', 'Salary head created successfully.');
    }
    
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = "View Salary Head";
        $show = Salary_Head_Master::join('salary_head_types', 'salary_head_masters.salary_head_type', '=', 'salary_head_types.id')

        ->select('salary_head_masters.*', 'salary_head_types.salary_head_type')
        ->where('salary_head_masters.id', $id)
        ->first();
   
    
        return view('salarys.show', compact('show','pageTitle'));
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
            $pageTitle = "Edit Salary Head";
            $masters = Salary_Head_Master::join('salary_head_types', 'salary_head_masters.salary_head_type', '=', 'salary_head_types.id')
                        ->select('salary_head_masters.*', 'salary_head_types.salary_head_type')
                        ->where('salary_head_masters.id', $id)
                        ->first();
                 
            $branchs = Salary_Head_Type::get();
            return view('salarys.edit', compact('pageTitle', 'masters','branchs','id'));
        } catch (QueryException $e) {
            return redirect()->route('salarys.index')->with('error', 'Something went wrong');
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
        // Validate the incoming request data
        $request->validate([
            'salary_head_name' => 'required|string|max:255',
            'salary_head_type' => 'required|exists:salary_head_types,id', // Assuming it's a foreign key
            'status' => 'nullable',
            'remark' => 'nullable|string',
            'company' => 'required|string|max:255',
        ]);
    
        // Determine the status value based on the 'status' input
        $is_status = $request->input('status') ? 1 : 0;
    
        // Find the existing Salary_Head_Master model instance
        $salary_head = Salary_Head_Master::findOrFail($id);
    
        // Update the model attributes with the new values from the request
        $salary_head->update([
            'salary_head_name' => $request->input('salary_head_name'),
            'salary_head_type' => $request->input('salary_head_type'),
            'status' => $is_status,
            'remark' => $request->input('remark'),
            'company' => $request->input('company'),
        ]);
    
        // Redirect to a specific route or page after successful update
        return redirect()->route('salarys.index')->with('success', 'Salary head updated successfully');
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       

            $salary_head = Salary_Head_Master::findOrFail($id);
            $salary_head->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Salary Head Deleted Successfully',
            ]);
        } 
        

    
}
