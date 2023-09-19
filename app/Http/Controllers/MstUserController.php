<?php

namespace App\Http\Controllers;
use App\Models\Mst_User;
use App\Models\Mst_User_Type;
use App\Models\Mst_Branch;
use Illuminate\Http\Request;

class MstUserController extends Controller
{
    public function index()
    {
        $pageTitle = "Users";
        $users = Mst_User::with('userType', 'branch')->latest()->get();
        return view('user.index', compact('pageTitle','users'));
    }

    public function create()
    {
        $pageTitle = "Create User";
        $userTypes = Mst_User_Type::pluck('user_type', 'id'); // Fetch user types from "mst_user_type" table
        $branches = Mst_Branch::pluck('branch_name', 'id'); // Fetch branches from "mst_branches" table

        return view('user.create', compact('pageTitle','userTypes','branches'));
    }

    public function store(Request $request)
   { 
    // Validate the input data

    $validatedData = $request->validate([
        'username' => 'required',
        'password' => 'required',
        'confirm_password' => 'required|same:password',
        'user_email' => 'required|email',
        'user_type_id' => 'required|exists:mst_user_type,id',
        'branch_id' => 'required|exists:mst_branches,id',
        'is_active' =>'required',
    ]);

    $is_active = $request->input('is_active') ? 1 : 0;

    $user = new  Mst_User();
    $user->username = $validatedData['username'];
    $user->password = bcrypt($validatedData['password']);
    $user->user_email = $validatedData['user_email'];
    $user->user_type_id = $validatedData['user_type_id'];
    $user->branch_id = $validatedData['branch_id'];
    $user->is_active = $is_active;
    $user->last_login_time = now();
    $user->created_by = auth()->id();
    $user->last_updated_by = auth()->id();
    $user->save();

  

    return redirect()->route('user.index')->with('success', 'User added successfully');
}


public function edit($id)
{
    $pageTitle = "Edit User";
    $user = Mst_User::find($id);
    $userTypes = Mst_User_Type::pluck('user_type', 'id');
    $branches = Mst_Branch::pluck('branch_name', 'id');

    return view('user.edit', compact('pageTitle','user', 'userTypes', 'branches'));
}

public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'username' => 'required',
        'password' => 'required',
        'confirm_password' => 'required|same:password',
        'user_email' => 'required|email',
        'user_type_id' => 'required|exists:mst_user_type,id',
        'branch_id' => 'required|exists:mst_branches,id',
       
    ]);

    $is_active = $request->input('is_active') ? 1 : 0;

    $user =  Mst_User::findOrFail($id);
    $user->username = $validatedData['username'];
    $user->password = bcrypt($validatedData['password']);
    $user->user_email = $validatedData['user_email'];
    $user->user_type_id = $validatedData['user_type_id'];
    $user->branch_id = $validatedData['branch_id'];
    $user->is_active = $is_active;
    $user->last_login_time = now();
    $user->created_by = auth()->id();
    $user->last_updated_by = auth()->id();
    $user->save();

  

    return redirect()->route('user.index')->with('success', 'User updated successfully');   
}

public function destroy($id)
{
    $user = Mst_User::findOrFail($id);
    $user->delete();

    return redirect()->route('user.index')->with('success', 'User deleted successfully');

}

public function changeStatus(Request $request, $id)
{
    $user = Mst_User::findOrFail($id);

    $user->is_active = !$user->is_active;
    $user->save();

    return redirect()->back()->with('success','Status changed successfully');
}

}
