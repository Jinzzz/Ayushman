<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Mst_User;
use App\Models\Sys_Blood_Group;
use App\Models\Sys_Gender;
use App\Models\Trn_UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function viewChangePassword()
    {
        $pageTitle="Change Password";
        return view('elements.profile.change-password',compact('pageTitle'));
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
                        'password.required'        => 'New Password required',
                        'password.confirmed'        => 'Passwords not matching',
                    ]
                );

                if (!$validator->fails()) {

                    $user = Mst_User::find(Auth::id());

                    if (Hash::check($request->old_password, $user->password)) {
                        $data20 = [
                            'password'      => Hash::make($request->password),
                        ];
                        Mst_user::where('id',Auth::id())->update($data20);

                       
                        return redirect()->back()->with('status','Password updated successfully');
                    } else {
                        return redirect()->back()->with('error','Old password entered is wrong');
                    }
                } else {
                    return redirect()->back()->withErrors($validator->errors())->withInput();
                    
                }
           
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
    public function viewProfile()
    {
        $pageTitle="View Profile";
        $doctor_user=Mst_User::with('profile')->find(Auth::id());
        $genders=Sys_Gender::all();
        $blood_groups=Sys_Blood_Group::all();
        //dd($doctor_user->profile->gender->gender_name);
        return view('elements.profile.view-profile',compact('pageTitle','genders','blood_groups','doctor_user'));

    }
    public function updateProfile(Request $request)
    {
        $data=array();
        try{
        $validator = Validator::make(
            $request->all(),
            [
                'user_name'          => 'required',
                'user_email'          => 'required|email',
                'date_of_birth' => 'required|date',
                'user_address'=>'required',
                'blood_group_id'=>'required',
                'gender_id'=>'required',
                'user_address'=>'required',
                'user_profile_image'=>'sometimes|required|max:50'


            ],
            [
                'user_name.required'=>'Doctor Name is required',
                'user_email.required'=>'Doctor Email is required',
                'user_address.required'=>'Address is required',
                'blood_group_id.required'=>'Blood Group is required',
                'gender_id.required'=>'Gender is required',
                'user_profile_image.max'=>'Profile image should not be exceeded 50KB'

            ]
        );
        $validator->sometimes('user_profile_image', 'required', function ($request) {
            return isset($request->user_profile_image);
        });
        if (!$validator->fails()) {
            $user = Mst_User::with('profile')->find(Auth::id());
            $user->user_email = $request->user_email;
            $user->username = $request->user_name;
            $userProfile = $user->profile; // Get the profile separately
            
            if (!$userProfile) {
                $userProfile = new Trn_UserProfile();
            }
            
            $userProfile->date_of_birth = $request->date_of_birth;
            $userProfile->blood_group_id = $request->blood_group_id;
            $userProfile->address = $request->user_address;
            $userProfile->gender_id = $request->gender_id;
            
            if ($request->hasFile('user_profile_image')) {
                $filePro = $request->file('user_profile_image');
                $filenamePro = $filePro->getClientOriginalName();
                $filePro->move('assets/uploads/doctor_profile/images', $filenamePro);
                $userProfile->profile_image = $filenamePro;
            }
            $user->update();
            
            $user->profile()->save($userProfile);
            
            return redirect()->back()->with('status', 'Profile updated successfully');

        }
        else
        {
            return redirect()->back()->withErrors($validator->errors())->withInput();

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
