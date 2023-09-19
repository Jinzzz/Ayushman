<?php

namespace App\Http\Controllers;
use App\Models\Mst_Branch;
use Illuminate\Http\Request;

class MstBranchController extends Controller
{
   
public function index(Request $request)
{
    $pageTitle = "Branches";
    $query = Mst_Branch::query();

    // Apply filters if provided
    if ($request->has('branch_code')) {
        $query->where('branch_code', 'LIKE', "%{$request->branch_code}%");
    }

    if ($request->has('branch_name')) {
        $query->where('branch_name', 'LIKE', "%{$request->branch_name}%");
    }

    $branches = $query->orderBy('updated_at', 'desc')->get();
    return view('branches.index', compact('pageTitle', 'branches'));
}

    public function create()
    {
        $pageTitle = "Create Branch";
        return view('branches.create',compact('pageTitle'));
    }

    public function store(Request $request)
{
    $request->validate([
        'branch_name' => 'required|unique:mst_branches',
        'branch_address' => 'required',
        // 'branch_contact_number' => 'sometimes|numeric|digits:10',
        // 'branch_email' => 'sometimes|email',
        // 'branch_admin_name' => 'required',
        // 'branch_admin_contact_number' => 'sometimes|numeric|digits:10',
        'is_active' => 'required',
    ]);

    $is_active = $request->input('is_active') ? 1 : 0;

    $lastInsertedId = Mst_Branch::insertGetId([
    'branch_code' => rand(50, 100),
    'branch_name' => $request->branch_name,
    'branch_address' => $request->branch_address,
    'branch_contact_number' => $request->branch_contact_number,
    'branch_email' => $request->branch_email,
    'branch_admin_name' => $request->branch_admin_name,
    'branch_admin_contact_number' => $request->branch_admin_contact_number,
    'created_by' => auth()->id(),
    'is_active' =>  $is_active ,
  ]);

  $leadingZeros = str_pad('', 3 - strlen($lastInsertedId), '0', STR_PAD_LEFT);
  $branchCode = 'BC' . $leadingZeros . $lastInsertedId;

  Mst_Branch::where('branch_id', $lastInsertedId)->update([
      'branch_code' => $branchCode
  ]);
   
    return redirect()->route('branches')->with('success', 'Branch added successfully');
}


public function edit($id)
{
    $pageTitle = "Edit Branch";
    $branch = Mst_Branch::findOrFail($id);
    return view('branches.edit', compact('pageTitle','branch'));
}

public function show($id)
{
    $pageTitle = "View branch details";
    $show = Mst_Branch::findOrFail($id);
    return view('branches.show',compact('pageTitle','show'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'branch_name' => 'required',
        'branch_address' => 'required',
        // 'branch_contact_number' => 'required|numeric|digits:10',
        // 'branch_email' => 'required|email',
        // 'branch_admin_name' => 'required',
        // 'branch_admin_contact_number' => 'required|numeric|digits:10',
       
    ]);

    $is_active = $request->input('is_active') ? 1 : 0;

    $update = Mst_Branch::find($id);
    $update->update([
        'branch_name' => $request->branch_name,
        'branch_address' => $request->branch_address,
        'branch_contact_number' => $request->branch_contact_number,
        'branch_email' => $request->branch_email,
        'branch_admin_name' => $request->branch_admin_name,
        'branch_admin_contact_number' => $request->branch_admin_contact_number,
        'is_active' =>  $is_active ,
    ]);

    return redirect()->route('branches')->with('success', 'Branch updated successfully');
}

public function destroy($id)
{
    $branch = Mst_Branch::findOrFail($id);
    $branch->delete();

    return redirect()->route('branches')->with('success', 'Branch deleted successfully');
}

public function changeStatus(Request $request, $id)
{
    $branch = Mst_Branch::findOrFail($id);

    $branch->is_active = !$branch->is_active;
    $branch->save();

    return redirect()->back()->with('success','Status changed successfully');
}



}
