<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Mst_User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MstAuthController extends Controller
{
    // Display the login form
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    public function showPharmacyLoginForm()
    {
        return view('auth.pharmacy.login');
    }
    
    public function showReceptionistLoginForm()
    {
        return view('auth.receptionist.login');
    }

    public function Receptionistlogin(Request $request)
    {
        $credentials = $request->only('username', 'password');


        if (Auth::guard('mst_users_guard')->attempt($credentials)) {
             $user = Auth::guard('mst_users_guard')->user();
                $user->last_login_time = now();
                $user->save();
            if ($user->user_type_id == 18) {
                return redirect()->intended('/reception-home');
            } else {
                Auth::guard('mst_users_guard')->logout();
            return back()->withInput()->withErrors(['login' => 'Invalid Receptionist User Credentials']);
            }
        } else {
            // Authentication failed
            return back()->withInput()->withErrors(['login' => 'Invalid credentials']);
        }
    }


    public function Pharmacylogin(Request $request)
    {
        $credentials = $request->only('username', 'password');


        if (Auth::guard('mst_users_guard')->attempt($credentials)) {
             $user = Auth::guard('mst_users_guard')->user();
                $user->last_login_time = now();
                $user->save();
            if ($user->user_type_id == 96) {
                return redirect()->intended('/pharmacy-home');
            } else {
                Auth::guard('mst_users_guard')->logout();
            return back()->withInput()->withErrors(['login' => 'Invalid Pharmacy User Credentials']);
            }
        } else {
            // Authentication failed
            return back()->withInput()->withErrors(['login' => 'Invalid credentials']);
        }
    }
    
    

    // Process the login form
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');


        if (Auth::guard('mst_users_guard')->attempt($credentials)) {
             $user = Auth::guard('mst_users_guard')->user();
                $user->last_login_time = now();
                $user->save();
            if ($user->user_type_id == 1) {
                return redirect()->intended('/home');
            } else {
                Auth::guard('mst_users_guard')->logout();
            return back()->withInput()->withErrors(['login' => 'Invalid Admin User Credentials']);
            }
        } else {
            // Authentication failed
            return back()->withInput()->withErrors(['login' => 'Invalid credentials']);
        }
    }

    // Log the user out
    public function logout()
    {
       
        $user = Auth::guard('mst_users_guard')->user();
       
       if ($user && $user->user_type_id == 1) {
            Auth::guard('mst_users_guard')->logout();
            return redirect('/login');
        } elseif ($user && $user->user_type_id == 96) {
            Auth::guard('mst_users_guard')->logout();
            return redirect('/pharmacy-login');
        } elseif ($user && $user->user_type_id == 18) {
            Auth::guard('mst_users_guard')->logout();
            return redirect('/receptionist-login');
        } else {
            return redirect('/login');
        }
    }

    public function verificationRequest()
    {
        return view('auth.email_verification');
    }

    
    public function verifyEmail(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'user_email_address'     => 'required|email',
                ],
                [
                    'user_email_address.required'        => 'Email address required',
                    'user_email_address.email'           => 'Invalid email address',
                ]
            );

            if (!$validator->fails()) {
                $user = Mst_User::where('user_email', $request->user_email_address)->where('is_active', 1)->first();
                if (!$user) {
                    return redirect()->route('verification.request')->with('verification', 'User does not exist.');
                }
                return view('auth.reset_password');
            } else {
                $messages = $validator->errors();
                return redirect()->route('verification.request')->with('error', $messages);
            }
        } catch (\Exception $e) {
            return redirect()->route('verification.request')->with('status', $e->getMessage());
        } catch (\Throwable $e) {
            return redirect()->route('verification.request')->with('status', $e->getMessage());
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'user_email_address'    => 'required|email',
                    'password'              => 'required|min:6',
                    'confirm_password'      => 'required|min:6|same:password',
                ],
                [
                    'user_email_address.required'   => 'Email address required',
                    'user_email_address.email'      => 'Invalid email address',
                    'password.required'             => 'Password required',
                    'confirm_password.required'     => 'Confirm password required',
                    'confirm_password.same'         => 'Passwords do not match',
                ]
            );

            if (!$validator->fails()) {
                $user = Mst_User::where('user_email', $request->user_email_address)->where('is_active', 1)->first();
                if (!$user) {
                    return view('auth.reset_password')->with('verification', 'User does not exist.');
                }

                if (!Hash::check($request->password, $user->password)) {
                    $user->password = Hash::make($request->password);
                    $user->updated_at = Carbon::now();
                    $user->save();
                    return redirect()->route('mst_login')->with('success', 'Password reset sussessfully. Please login');
                } else {
                    return view('auth.reset_password')->with('oldPsw', 'Your new password is similar to the current Password. Please try another password.');
                }
            } else {
                $messages = $validator->errors();
                return view('auth.reset_password')->with('error', $messages);
            }
        } catch (\Exception $e) {
            return view('auth.reset_password')->with('login', $e->getMessage());
        } catch (\Throwable $e) {
            return view('auth.reset_password')->with('login', $e->getMessage());
        }
    }
}
