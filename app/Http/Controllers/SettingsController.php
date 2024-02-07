<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Mst_Staff;
use App\Models\Mst_User;
use Illuminate\Support\Facades\Validator;


class SettingsController extends Controller
{
    public function ProfileIndex()
    {
        return view('profile.index', [
            'admin' => Auth::user(),
            'pageTitle' => 'Profile'
        ]);
    }

    
    public function ChangePassword()
    {
        return view('profile.password', [
            'admin' => Auth::user(),
            'pageTitle' => 'Change Password'
        ]);
    }

    
    public function UpdatePassword(Request $request)
    {

        $user_id = Auth::user()->user_id;
        $user = Mst_User::Find($user_id);
        $validator = Validator::make(
        $request->all(),
        [
            'password'         => 'required|same:password_confirmation',
        ],
        [
            'password.required'        => 'Password required',
        ]
        );
        if (!$validator->fails()) {
            $data = $request->except('_token');
           
        if (Hash::check($request->old_password, $user->password)) {
        $data2 = [
          'password'      => Hash::make($request->password),
        ];
        
        Mst_User::where('user_id', $user_id)->update($data2);
      } else {
        return redirect()->back()->with('errstatus', 'Old password incorrect.');
      }
      return redirect()->back()->with('status', 'Password updated successfully.');
    } else {
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }

}
