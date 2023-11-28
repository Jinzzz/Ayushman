<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Salary_Head_Type;
use App\Models\Salary_Head_Master;
use App\Models\Salary_Package;
class SalaryPackageController extends Controller
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
        $masters = Salary_Head_Master::join('salary_head_types', 'salary_head_masters.salary_head_type', '=', 'salary_head_types.id')->select('salary_head_masters.*', 'salary_head_types.salary_head_type')
            ->orderBy('salary_head_masters.updated_at', 'desc')
            ->get();

        $packages = Salary_Package::orderBy('updated_at', 'desc');

        // Apply filters if provided
        if ($request->has('package_name'))
        {
            $packages->where('package_name', 'LIKE', "%{$request->package_name}%");
        }
        if ($request->has('package_amount_type'))
        {
            $packages->where('package_amount_type', 'LIKE', "%{$request->package_amount_type}%");
        }
        $packages = $packages->get();

        return view('packages.index', compact('pageTitle', 'branch', 'masters', 'packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = "Create Salary Package";
        $branch = Salary_Head_Type::get();
        $heads = Salary_Head_Master::where('status', 1)->get();
        return view('packages.create', compact('pageTitle', 'branch', 'heads'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        // Validation rules - all fields are required
        $this->validate($request, ['package_name' => 'required|string|max:255', 'company_name' => 'required|string|max:255', 'status' => 'required', // Adjust based on your status field type
        'salary_head_type_id' => 'required|numeric', // Adjust based on your salary_head_type field type
        'package_amount_value' => 'required|numeric', 'package_amount_type' => 'required', 'salary_head_id' => 'required', 'remark' => 'nullable|string',
        // Add other required fields...
        ]);
        $status = $request->input('status') === 'on' ? 1 : 0;
        // Check for duplicate entry based on package_name and company_name
        $duplicatePackage = Salary_Package::where('package_name', $request->input('package_name'))
            ->where('company_name', $request->input('company_name'))
            ->where('salary_head_id', $request->input('salary_head_id'))
            ->where('salary_head_type_id', $request->input('salary_head_type_id'))
            ->where('package_amount_type', $request->input('package_amount_type'))
            ->where('package_amount_value', $request->input('package_amount_value'))
            ->where('status', $status)->where('remark', $request->input('remark'))
            ->first();

        if ($duplicatePackage)
        {
            return redirect()->route('packages.create')
                ->withInput($request->all())
                ->withErrors(['duplicate_entry' => 'Package already exists.']);
        }

        // Create new package if no duplicate entry
        $package = new Salary_Package;
        $package->package_name = $request->input('package_name');
        $package->company_name = $request->input('company_name');
        $package->salary_head_id = $request->input('salary_head_id');
        $package->salary_head_type_id = $request->input('salary_head_type_id');
        $package->status = $status;
        $package->package_amount_type = $request->input('package_amount_type');
        $package->package_amount_value = $request->input('package_amount_value');
        $package->remark = $request->input('remark');
        // Set other required fields...
        // Save the package
        $package->save();

        return redirect()
            ->route('packages.index')
            ->with('success', 'Package added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = "View Package";
        $show = Salary_Package::join('salary_head_masters', 'salary_head_masters.id', '=', 'salary_packages.salary_head_id')->join('salary_head_types', 'salary_head_masters.salary_head_type', '=', 'salary_head_types.id')
            ->select('salary_packages.*', 'salary_head_masters.salary_head_name', 'salary_head_types.*')
            ->where('salary_packages.id', $id)->first();

        return view('packages.show', compact('show', 'pageTitle'));
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
            $pageTitle = "Edit Salary Package";

            // Retrieve the salary package data with related salary head information
            $packages = Salary_Package::join('salary_head_masters', 'salary_head_masters.id', '=', 'salary_packages.salary_head_id')->join('salary_head_types', 'salary_head_masters.salary_head_type', '=', 'salary_head_types.id')
                ->select('salary_packages.*', 'salary_head_masters.salary_head_name', 'salary_head_types.*')
                ->where('salary_packages.id', $id)->first();

            // Retrieve all active salary head masters
            $heads = Salary_Head_Master::where('status', 1)->get();

            // Retrieve all salary head types
            $branchs = Salary_Head_Type::get();

            // Pass the data to the view
            return view('packages.edit', compact('pageTitle', 'packages', 'branchs', 'id', 'heads'));
        }
        catch(QueryException $e)
        {
            return redirect()->route('packages.index')
                ->with('error', 'Something went wrong');
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
        $request->validate(['package_name' => 'required|string|max:255',
        'status' => 'required', 
        'salary_head_type_id' => 'required',
        'remark' => 'nullable|string', 
        'salary_head_id' => 'required',
        'package_amount_type' => 'required',
        'package_amount_value' => 'required', ]);

        $is_status = $request->input('status') ? 1 : 0;
        $salary_head = Salary_Package::findOrFail($id);
        // Create a new record in the database
        $salary_head->update(['package_name' => $request->input('package_name') ,
        'salary_head_type_id' => $request->input('selected_salary_head_type_id') ,
        'status' => $is_status, 'remark' => $request->input('remark') ,
        'package_amount_value' => $request->input('package_amount_value') ,
        'package_amount_type' => $request->input('package_amount_type') ,
        'salary_head_id' => $request->input('salary_head_id'), 
            ]);

        // Redirect to a specific route or page after successful creation
        return redirect()
            ->route('packages.index')
            ->with('success', 'Salary Package updated successfully');
    }

    /** 
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $package = Salary_Package::findOrFail($id);

        // Soft delete the record
        $package->delete();

        return response()
            ->json(['success' => true, 'message' => 'Package deleted successfully', ]);
    }
    public function getSalaryHeadTypes($id)
    {
        $salaryHeadTypes = Salary_Head_Master::join('salary_head_types', 'salary_head_masters.salary_head_type', '=', 'salary_head_types.id')->where('salary_head_masters.id', $id)->pluck('salary_head_types.salary_head_type', 'salary_head_masters.id');
    
        return response()
            ->json($salaryHeadTypes);
    }

    public function getSalaryHeadType($id)
    {
        // Your existing logic to retrieve salary_head_type
        $salaryHead = Salary_Head_Master::select('salary_head_types.*')
                      ->join('salary_head_types', 'salary_head_masters.salary_head_type', '=', 'salary_head_types.id')
                      ->where('salary_head_masters.id', $id)
                      ->first();
    
        // Check if the record exists
        if (!$salaryHead) {
            return response()->json(['error' => 'Salary head not found'], 404);
        }
    
        $salaryHeadType = $salaryHead->salary_head_type;
        $salaryHeadTypeId = $salaryHead->id;
    
        // Return the data as JSON with both id and salary_head_type
        return response()->json([
            'id' => $id,
            'salary_head_type' => $salaryHeadType,
            'salaryHeadTypeId' => $salaryHeadTypeId,
        ]);
    }
    
    
          
}

