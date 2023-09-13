<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function loginCustomer(Request $request)
    {
        //return 1;
        $data=array();
        try
        {
            $email = $request->input('email_address');
            $passChk = $request->input('password');
            $vaildate_array= [
                'email_address' => 'required|email',
                'password' => 'required',
            ];
            $vaildate_rules= [
                'email_address.required' => "Email is required",
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
                $user=User::where('email',$email)->first();
                if(!$user)
                {
                    $data['status'] = 0;
                    $data['message'] = "Invalid Login Details";
                    return response($data);

                }
                if (Hash::check($passChk, $user->password)) 
                {
                    $check_array=['email' => request('email_address'), 'password' => request('password')];
                    if (Auth::attempt($check_array)) 
                    {
                        $data['token'] =  $user->createToken('authToken')->accessToken;
                        $data['status'] = 1;
                        $data['message'] = "Login Success";
                        $data['name']=$user->name;
                        $data['email_address']=$user->email;
                        return response($data);

                    }
                    else
                    {
                        $data['status'] = 0;
                        $data['message'] = "Invalid Login Details";
                        return response($data);

                    }

                }
                else
                {
                    $data['status'] = 0;
                    $data['message'] = "Invalid Login Details";
                    return response($data);
                }

            }
            else
            {
                $data['status'] = 0;
                $data['errors'] = $validator->errors();
                $data['message'] = "Login Failed";
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
