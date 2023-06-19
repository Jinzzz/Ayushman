<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminProfileController extends Controller
{
    public function getAdminDetails()
    {
        $data=array();
        try
        {
            $admin=User::find(Auth::id())->first();
            $data['status']=1;
            $data['data']=$admin;
            $data['message']="User Data Fetched";
            return response($data);

        }
        catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
            
        } catch (\Throwable $e) {
                $response = ['status' => '0', 'message' => $e->getMessage()];
                return response($response);
            }


    }
    public function updatePassword(Request $request)
    {
        $data = array();

        try {
           
            
                $validator = Validator::make(
                    $request->all(),
                    [
                        'old_password'          => 'required',
                        'password' => 'required|min:6|confirmed',

                    ],
                    [
                        'old_password.required'        => 'Old password required',
                        'password.required'        => 'Password required',
                        'password.confirmed'        => 'Passwords not matching',
                    ]
                );

                if (!$validator->fails()) {

                    $user = User::find(Auth::id());

                    if (Hash::check($request->old_password, $user->password)) {
                        $data20 = [
                            'password'      => Hash::make($request->password),
                        ];
                        User::where('id',Auth::id())->update($data20);

                       
                        $data['status'] = 1;
                        $data['message'] = "Password updated successfully.";
                        return response($data);
                    } else {
                       
                        $data['status'] = 0;
                        $data['message'] = "Old password incorrect.";
                        return response($data);
                    }
                } else {
                    $data['status'] = 0;
                    $data['errors'] = $validator->errors();
                    $data['message'] = "failed";
                    return response($data);
                    
                }
           
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
}
