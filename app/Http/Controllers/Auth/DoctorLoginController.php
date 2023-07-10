<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mst_User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DoctorLoginController extends Controller
{
    public function doctorLogin(Request $request)
    {
        $data=array();
        try
        {
            $email = $request->input('email');
            $passChk = $request->input('password');
            $vaildate_array= [
                'email' => 'required|email',
                'password' => 'required',
            ];
            $vaildate_rules= [
                'email.required' => "Email is required",
                'password.required' => "Password is required",
            ];
            $validator = Validator::make(
                $request->all(),
               $vaildate_array,
                $vaildate_rules
            );
            // dd($validator);
            if (!$validator->fails()) 
            {
                $user=Mst_User::where('user_email',$email)->where('user_type_id',3)->first();
                if(!$user)
                {
                    return redirect()->back()->with('error','Invalid Login Details');

                }
                if (Hash::check($passChk, $user->password)) 
                {
                    $check_array=['user_email' => request('email'), 'password' => request('password')];
                    if (Auth::attempt($check_array)) 
                    {
                        $user->last_login_time=Carbon::now();
                        $user->update();
                        return redirect()->to('/doctor-home')->with('success','Logged in sucessfully');

                    }
                    else
                    {
                        return redirect()->back()->with('error','Invalid Login Details');

                    }

                }
                else
                {
                    return redirect()->back()->with('error','Invalid Login Details');
                }

            }
            else
            {
                return redirect()->back()->with('error','Invalid Login Details');
            }


             

        
        } 
        catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
            
        } catch (\Throwable $e) {
                $response = ['status' => '0', 'message' => $e->getMessage()];
                return response($response);
            }

    }
}
