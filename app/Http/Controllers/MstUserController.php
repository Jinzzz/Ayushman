<?php

namespace App\Http\Controllers;

use App\Models\Mst_User;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Branch;
use Illuminate\Database\QueryException;
use App\Models\Mst_Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MstUserController extends Controller
{
    // Note: This feature allows staff members to have multiple roles. For instance, 
    // if a staff member serves as both a pharmacist and an accountant, this screen is used to add the second role.
    public function index()
    {
        try {
            $pageTitle = "Users";
            $userTypes = Mst_Master_Value::where('master_id', 4)->pluck('master_value', 'id');
            $staff = Mst_Staff::pluck('staff_name', 'staff_id');
            $users = Mst_User::latest()->get();
            return view('user.index', compact('pageTitle', 'users', 'userTypes', 'staff'));
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function create()
    {
        try {

            $pageTitle = "Create User";
            $userTypes = Mst_Master_Value::where('master_id', 4)->pluck('master_value', 'id');
            $staffs = Mst_Staff::pluck('staff_name', 'staff_id');

            return view('user.create', compact('pageTitle', 'userTypes', 'staffs'));
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function store(Request $request)
    {
        // Note: if a staff member serves as both a pharmacist and an accountant, this screen is used to add the second role.
        try {
            // Validate the input data
            $validator = Validator::make(
                $request->all(),
                [
                    'username' => 'required',
                    'password' => 'required',
                    'confirm_password' => 'required|same:password',
                    'user_email' => 'required|email',
                    'user_type_id' => 'required',
                    'is_active' => 'required',
                ],
                [
                    'username.required' => 'Username is required.',
                    'password.required' => 'Password is required.',
                    'confirm_password.required' => 'Confirm password is required.',
                    'confirm_password.same' => 'Confirm password must match the password.',
                    'user_email.required' => 'User email is required.',
                    'user_email.email' => 'User email must be a valid email address.',
                    'user_type_id.required' => 'User type ID is required.',
                    'is_active.required' => 'Is active field is required.',
                ]

            );

            if (!$validator->fails()) {
                $is_active = $request->input('is_active') ? 1 : 0;
                $usernameExists = Mst_User::where('username', $request->username)->exists();
                if ($usernameExists) {
                    return redirect()->route('user.index')->with('error', 'Failed to create. This username is already taken.');
                }
                $user = new  Mst_User();
                $user->username = $request->input('username');
                $user->password = Hash::make($request->input('password'));
                $user->email = $request->input('user_email');
                $user->user_type_id = $request->input('user_type_id');
                $user->staff_id = $request->input('staff_id');
                $user->is_active = $is_active;
                $user->last_login_time = Carbon::now();
                $user->created_by = 1;
                $user->last_updated_by = 1;
                $user->save();
                return redirect()->route('user.index')->with('success', 'User added successfully');
            } else {
                $messages = $validator->errors();
                return redirect()->route('user.create')->with('errors', $messages);
            }
        } catch (QueryException $e) {
            dd($e->getMessage());
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }


    public function edit($id)
    {
        try {
            $pageTitle = "Edit User";
            $user = Mst_User::find($id);
            $userTypes = Mst_Master_Value::where('master_id', 4)->pluck('master_value', 'id');
            $staff = Mst_Staff::pluck('staff_name', 'staff_id');
            return view('user.edit', compact('pageTitle', 'user', 'userTypes', 'staff'));
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'username' => 'required',
                // 'password' => 'required',
                // 'confirm_password' => 'required|same:password',
                'user_email' => 'required|email',
                'user_type_id' => 'required',
                'staff_id' => 'required',

            ]);
            $is_active = $request->input('is_active') ? 1 : 0;

            $user =  Mst_User::findOrFail($id);
            $user->username = $request->input('username');
            $user->password = Hash::make($request->input('password'));
            $user->user_email = $request->input('user_email');
            $user->user_type_id = $request->input('user_type_id');
            $user->staff_id = $request->input('staff_id');
            $user->is_active = $is_active;
            $user->last_login_time = now();
            $user->created_by = 1;
            $user->last_updated_by = 1;
            $user->save();
            return redirect()->route('user.index')->with('success', 'User updated successfully');
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function destroy($id)
    {
        try {
            $user = Mst_User::findOrFail($id);
            $user->delete();
            return 1;
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $user = Mst_User::findOrFail($id);
            $user->is_active = !$user->is_active;
            $user->save();
            return 1;
        } catch (QueryException $e) {
            return redirect()->route('home')->with('error', 'Something went wrong');
        }
    }
}
