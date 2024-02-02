<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mst_Branch;
use App\Models\Mst_Supplier;
use App\Models\Mst_Master_Value;
use App\Models\Mst_User;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminProfileController extends Controller
{
    public function getGender()
    {
        $data = [];

        try {

            $genders = Mst_Master_Value::where('master_id', 17)->get(['id', 'master_value as name'])->toArray();

            if ($genders) {
                $data['status'] = 1;
                $data['message'] = "Data fetched.";
                $data['data'] = $genders;
            } else {
                $data['status'] = 0;
                $data['message'] = "Gender not detected.";
            }

            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
    public function getBloodGroup()
    {
        $data = [];

        try {

            $bloodGroups = Mst_Master_Value::where('master_id', 19)->get(['id', 'master_value as name'])->toArray();

            if ($bloodGroups) {
                $data['status'] = 1;
                $data['message'] = "Data fetched.";
                $data['data'] = $bloodGroups;
            } else {
                $data['status'] = 0;
                $data['message'] = "Blood group not detected.";
            }

            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
    public function getBranches()
    {
        $data = array();
        try {
            $branches = Mst_Branch::where('is_active', 1)->get(['branch_id', 'branch_name'])->toArray();
            if ($branches) {
                $data['status'] = 1;
                $data['message'] = "Data fetched.";
                $data['data'] = $branches;
            } else {
                $data['status'] = 0;
                $data['message'] = "No branches found.";
            }
            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
     public function getPatientBookingTypes()
    {
        $data = array();
        try {
            $bookingTypes = Mst_Master_Value::where('master_id', 21)->get(['id', 'master_value as booking_type'])->toArray();

            if ($bookingTypes) {
                $data['status'] = 1;
                $data['message'] = "Data fetched.";
                $data['data'] = $bookingTypes;
            } else {
                $data['status'] = 0;
                $data['message'] = "Booking type not detected.";
            }
            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
    public function getAdminDetails()
    {
        $data=array();
        try
        {
            $admin=Mst_User::find(Auth::id())->first(['username','full_name','admin_phone_number','email','gender','blood_group','date_of_birth','address','profile_image']);
            $data['status']=1;
            $data['data']=$admin;
            $data['image']='assets/uploads/admin_profile/images/'.$admin->profile_picture;
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
     public function getSuppliers()
    {
        $data = array();
        try {
            $suppliers = Mst_Supplier::get(['supplier_id', 'supplier_name'])->toArray();

            if ($suppliers) {
                $data['status'] = 1;
                $data['message'] = "Data fetched.";
                $data['data'] = $suppliers;
            } else {
                $data['status'] = 0;
                $data['message'] = "Booking type not detected.";
            }
            return response($data);
        } catch (\Exception $e) {
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

                    $user = Mst_User::find(Auth::id());

                    if (Hash::check($request->old_password, $user->password)) {
                        $data20 = [
                            'password'      => Hash::make($request->password),
                        ];
                        Mst_User::where('user_id',Auth::id())->update($data20);

                       
                        $data['status'] = 1;
                        $data['message'] = "Password updated successfully.";
                        return response($data);
                    } else {
                       
                        $data['status'] = 3;
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
    public function updateProfile(Request $request)
    {
        $data=array();
        try{
        $validator = Validator::make(
            $request->all(),
            [
                'email'          => 'required|email',
                'date_of_birth' => 'required|date',
                'address'=>'required',
                'blood_group_id'=>'required',
                'gender_id'=>'required',
                'address'=>'required',
                'phone_number'=>'required',
               // 'profile_image'=>'sometimes|required|max:50'


            ],
            [
                'blood_group_id.required'=>'Blood Group is required',
                'gender_id.required'=>'Gender is required',
                //'profile_image.max'=>'Profile imag should not be exceeded 50KB'

            ]
        );
        $validator->sometimes('profile_image', 'required', function ($request) {
            return isset($request->profile_image);
        });
        if (!$validator->fails()) {
            $user=Mst_User::where('user_id',Auth::id())->first(['user_id','username','full_name','admin_phone_number','email','gender','blood_group','date_of_birth','address']);
            $user->username=$request->username;
            $user->full_name=$request->full_name;
            $user->email=$request->email;
            $user->date_of_birth=$request->date_of_birth;
            $user->blood_group=$request->blood_group_id;
            $user->admin_phone_number=$request->phone_number;
            $user->address=$request->address;
            $user->gender=$request->gender_id;
            $user->update();
            $data['status']=1;
            $data['user']=$user;
            $data['image_path']='assets/uploads/admin_profile/images';
            $data['message'] = "Profile updated successfully.";
            return response($data);

        }
        else
        {
            $data['status'] = 0;
            $data['errors'] = $validator->errors();
            $data['message'] = "failed";
            return response($data);

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
    public function updateImage(Request $request)
    {
        $data=array();
        try{
        $validator = Validator::make(
            $request->all(),
            [
               
                'profile_image'=>'sometimes|required|max:1024'


            ],
            [
                'profile_image.max'=>'Profile image should not be exceeded 1 MB'

            ]
        );
        $validator->sometimes('profile_image', 'required', function ($request) {
            return isset($request->profile_image);
        });
        if (!$validator->fails()) {
            $user=Mst_User::find(Auth::id());
           
            if ($request->hasFile('profile_image')) {

                $filePro = $request->file('profile_image');
                $filenamePro = $filePro->getClientOriginalName();
                $filePro->move('assets/uploads/admin_profile/images', $filenamePro);
                $user->profile_image=$filenamePro;
            }
            $user->update();
            $data['status']=1;
            $data['image']='assets/uploads/admin_profile/images/'.$user->profile_image;
            $data['message'] = "Profile image updated successfully.";
            return response($data);

        }
        else
        {
            $data['status'] = 0;
            $data['errors'] = $validator->errors();
            $data['message'] = "failed";
            return response($data);

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
