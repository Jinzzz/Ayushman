<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sys_Blood_Group;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\PatientHelper;
use App\Models\Trn_Patient_Otp;
use App\Models\Trn_Patient_Device_Tocken;

class PatientAuthController extends Controller
{
    public function patientRegister(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'patient_name'      => 'required',
                    'patient_email'     => 'required|email|unique:mst_patients,patient_email',
                    'patient_mobile'    => ['required', 'regex:/^[0-9]{10}$/','unique:mst_patients,patient_mobile'],
                    'patient_address'      => 'required',
                    'patient_gender'      => 'required',
                    'patient_dob'      => 'required',
                    'password'          => 'required|min:6',
                    'retype_password'   => 'required|min:6|same:password',
                ],
                [
                    'patient_name.required'         => 'Name required',
                    'patient_email.required'        => 'Email address required',
                    'patient_email.email'           => 'Invalid email address',
                    'patient_email.unique'          => 'Email address is already in use',
                    'patient_mobile.required'       => 'Mobile number required',
                    'patient_mobile.regex'          => 'Invalid mobile number',
                    'patient_mobile.unique'         => 'Mobile number is already in use',
                    'patient_address.required'         => 'Address required',
                    'patient_gender.required'         => 'Gender required',
                    'patient_dob.required'         => 'Date of birth required',
                    'password.required'             => 'Password required',
                    'retype_password.required'      => 'Retype password required',
                    'retype_password.same'          => 'Passwords do not match',
                ]
            );

                if (!$validator->fails()) {
                    $patients = Mst_Patient::where('patient_mobile', $request->patient_mobile)
                    ->orWhere('patient_email', $request->patient_email)
                    ->first();

                    if($request->patient_gender){
                        $patient_gender_id = Mst_Master_Value::where('master_value', 'LIKE', '%' . $request->patient_gender . '%')->pluck('id')->first();
                    }

                    $patient_dob = PatientHelper::dateFormatDb($request->patient_dob);

                    if (!$patients) {
                        $lastInsertedId = Mst_Patient::insertGetId([
                        'patient_name'      => $request->patient_name,
                        'patient_email'     => $request->patient_email,
                        'patient_address'   => $request->patient_address,
                        'patient_gender'    => $patient_gender_id,
                        'patient_dob'       => $patient_dob,
                        'patient_mobile'    => $request->patient_mobile,
                        'password'          => Hash::make($request->password),
                        'is_active'         => 1,
                        'patient_blood_group_id' => 68,
                        'available_membership'  => 0,
                        'patient_code'         => rand(50, 100),
                        'created_at'         => Carbon::now(),
                        ]);

                        $verification_otp = rand(100000, 999999);
                        $leadingZeros = str_pad('', 3 - strlen($lastInsertedId), '0', STR_PAD_LEFT);
                        $newPatientCode = 'PAT' . $leadingZeros . $lastInsertedId;

                        $updatePatientCode = Mst_Patient::where('id', $lastInsertedId)->update([
                        'updated_at' => Carbon::now(),
                        'patient_code' => $newPatientCode
                        ]);

                        $otpCreate = Trn_Patient_Otp::create([
                            'patient_id' => $lastInsertedId,
                            'otp' => $verification_otp,
                            'otp_type' => 1,
                            'verified' => 0,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                            'otp_expire_at' => Carbon::now()->addMinutes(10),
                        ]);

                        if (isset($request->device_token) && isset($request->device_type)) {
                            Trn_Patient_Device_Tocken::where('patient_id', $lastInsertedId)->delete();
    
                            $pdt = new Trn_Patient_Device_Tocken;
                            $pdt->patient_id =$lastInsertedId;
                            $pdt->patient_device_token = $request->device_token;
                            $pdt->patient_device_type = $request->device_type;
                            $pdt->created_at = Carbon::now();
                            $pdt->updated_at = Carbon::now();
                            $pdt->save();
                        }
                       
                        $data['status'] = 1;
                        $data['otp']=$verification_otp;
                        $data['otp_type']=1;
                        $data['patient_id']=$lastInsertedId;
                        $data['message'] = "OTP sent successfully.";
                        return response($data);
                    } else {
                       
                        $data['status'] = 0;
                        $data['message'] = "Mobile number or Email address is already in use.";
                        return response($data);
                    }

                } else {
                    $data['status'] = 0;
                    $data['errors'] = $validator->errors();
                    $data['message'] = "Validation errors";
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

    public function patientLogin(Request $request)
    {
        $data=array();
        try
        {
            $mobile = $request->input('patient_mobile');
            $passChk = $request->input('password');
            $validator = Validator::make(
                $request->all(),
                [
                    'patient_mobile'    => ['required', 'regex:/^[0-9]{10}$/'],
                    'password'          => 'required|min:6',
                ],
                [
                    'patient_mobile.required'       => 'Mobile number required',
                    'patient_mobile.regex'          => 'Invalid mobile number',
                    'password.required'             => 'Password required',
                ]
            );

            if (!$validator->fails()) 
            {
                $patient=Mst_Patient::where('patient_mobile',$mobile)->first();
                if(!$patient)
                {
                    $data['status'] = 0;
                    $data['message'] = "Invalid Login Details";
                    return response($data);

                }
                if (Hash::check($passChk, $patient->password)) 
                {
                    $check_array=['patient_mobile' => request('patient_mobile'), 'password' => request('password')];
                    if ($check_array) 
                    {
                        $data['token'] =  $patient->createToken('Patient Token', ['patient']);
                        $data['status'] = 1;
                        $data['message'] = "Login Success";
                        $data['name']=$patient->patient_name;
                        $data['patient_id']=$patient->id;
                        $data['patient_mobile']=$patient->patient_mobile;
                        $data['email_address']=$patient->patient_email;
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
                $data['message'] = "Validation errors";
                return response($data);
            }
        } 
        catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
            
        } 
        catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }

    

    public function otpVerification(Request $request){
        $data=array();
        try
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'otp' => ['required', 'numeric', 'digits:6'],
                    'otp_type' => ['required'],
                    'patient_id' => ['required'],
                ],
                [
                    'otp.required' => 'OTP required',
                    'otp_type.required' => 'OTP required',
                    'otp.numeric' => 'OTP must be numeric',
                    'otp.digits' => 'OTP must be 6 digits',
                    'patient_id.required' => 'Patient id required',
                ]
            );

            if (!$validator->fails()) 
            {
                if(isset($request->patient_id, $request->otp_type, $request->otp)){
                    $patient_id = $request->input('patient_id');
                    $patient=Mst_Patient::find($patient_id);
                    if(!$patient)
                    {
                    $data['status'] = 0;
                    $data['message'] = "User does not exist.";
                    return response($data);
                    }

                    $lastInsertedRow = Trn_Patient_Otp::where('otp_type', $request->otp_type)
                    ->where('patient_id', $patient_id)
                    ->where('otp', $request->otp)
                    ->latest('otp_id')
                    ->first();

                    if ($lastInsertedRow && $lastInsertedRow->otp == $request->otp) 
                    {   
                    if( Carbon::now()->lessThanOrEqualTo($lastInsertedRow->otp_expire_at)){

                        Trn_Patient_Otp::where('otp_id', $lastInsertedRow->otp_id)->update([
                            'updated_at' => Carbon::now(),
                            'verified' => 1,
                            ]);

                            // is_otp_verified

                            if($request->otp_type == 1){
                                Mst_Patient::where('id',$patient_id)->update([
                                    'updated_at' => Carbon::now(),
                                    'is_otp_verified' => 1,
                                    ]);
                            }


                        $data['status'] = 1;
                        $data['message'] = "Registration completed successfully";
                        if($request->otp_type == 2){
                            $data['message'] = "OTP verified successfully";
                        }
                        return response($data);

                    }else{

                        $data['status'] = 0;
                        $data['message'] = "OTP Expired";
                        return response($data);
                    }
                    
                    }else{
                    $data['status'] = 0;
                    $data['message'] = "OTP doesn't match";
                    return response($data);
                    }
                }else{
                    $data['status'] = 0;
                    $data['message'] = "Patient id / Otp type / Otp is required";
                    return response($data);
                }
                
            }
            else
            {
                $data['status'] = 0;
                $data['errors'] = $validator->errors();
                $data['message'] = "Validation errors";
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

    public function reSendOtp(Request $request){
        $data=array();
        try{
            $patient_id = $request->input('patient_id');

            $validator = Validator::make(
                $request->all(),
                [
                    'patient_id' => ['required'],
                ],
                [
                    'patient_id.required' => 'Patient id required',
                ]
            );

            if (!$validator->fails()) 
            {
                $patient=Mst_Patient::where('id',$patient_id)->first();
                if(!$patient)
                {
                    $data['status'] = 0;
                    $data['message'] = "User does not exist.";
                    return response($data);
                }

                $lastInsertedRow = Trn_Patient_Otp::where('patient_id', $request->patient_id)
                    ->latest('otp_id')
                    ->first();

                if($lastInsertedRow){
                    $verification_otp = rand(100000, 999999);
                    $resendOtp = Trn_Patient_Otp::where('otp_id', $lastInsertedRow->otp_id)->update([
                        'updated_at' => Carbon::now(),
                        'otp' => $verification_otp,
                        'verified' => 0,
                        'otp_expire_at' => Carbon::now()->addMinutes(10),
                    ]);

                    if($lastInsertedRow->otp_type == 1){
                        Mst_Patient::where('id',$patient_id)->update([
                            'updated_at' => Carbon::now(),
                            'is_otp_verified' => 0,
                            ]);
                    }
                    
                    $data['status'] = 1;
                    $data['otp']=$verification_otp;
                    $data['otp_type']= $lastInsertedRow->otp_type;
                    $data['patient_id']=$patient_id;
                    $data['message'] = "OTP resent successfully.";
                    return response($data);
                }
            }
            else
            {
                $data['status'] = 0;
                $data['errors'] = $validator->errors();
                $data['message'] = "Validation errors";
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

    public function forgotPassword(Request $request){
        $data=array();
        try
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'patient_mobile'    => ['required', 'regex:/^[0-9]{10}$/'],
                ],
                [
                    'patient_mobile.required'       => 'Mobile number required',
                    'patient_mobile.regex'          => 'Invalid mobile number',
                ]
            );

            if (!$validator->fails()) 
            {
                $patient_mobile = $request->input('patient_mobile');
                $patient=Mst_Patient::where('patient_mobile',$patient_mobile)->first();
                if($patient)
                {
                    $verification_otp = rand(100000, 999999);
                    $otpCreate = Trn_Patient_Otp::create([
                        'patient_id' => $patient->id,
                        'otp' => $verification_otp,
                        'otp_type' => 2,
                        'verified' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'otp_expire_at' => Carbon::now()->addMinutes(10),
                    ]);
                       
                    $data['status'] = 1;
                    $data['otp']=$verification_otp;
                    $data['otp_type']=2;
                    $data['patient_id']=$patient->id;
                    $data['message'] = "Otp sent successfully.";
                    return response($data);
                }else{
                    $data['status'] = 0;
                    $data['message'] = "User doesn't exist.";
                    return response($data);
                }
            }
            else
            {
                $data['status'] = 0;
                $data['errors'] = $validator->errors();
                $data['message'] = "Validation errors";
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

    public function resetPassword(Request $request){
        $data=array();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'patient_id'      => 'required',
                    'password'          => 'required|min:6',
                    'confirm_password'   => 'required|min:6|same:password',
                ],
                [
                    'patient_id.required'             => 'Patient id required',
                    'password.required'             => 'Password required',
                    'confirm_password.required'      => 'Confirm password required',
                    'confirm_password.same'          => 'Passwords do not match',
                ]
            );
            if (!$validator->fails()) 
            {
                $patient=Mst_Patient::where('id',$request->patient_id)->first();
                if($patient)
                {
                    if(!Hash::check($request->password, $patient->password)){
                        $patient->password = Hash::make($request->password);
                        $patient->updated_at = Carbon::now();
                        $patient->save();
                        $data['status'] = 1;
                        $data['message'] = "Password reset sussessfully.";
                        return response($data);
                    }else{
                        $data['status'] = 0;
                        $data['message'] = "Your new password is similar to the current Password. Please try another password.";
                        return response($data);
                    }
                }
                else{
                    $data['status'] = 0;
                    $data['message'] = "User does not exist.";
                    return response($data);
                }
            }
            else
            {
                $data['status'] = 0;
                $data['errors'] = $validator->errors();
                $data['message'] = "Validation errors";
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

    public function updateDetails(Request $request){
        $data=array();
        try{
            $patient_id = Auth::id();
            if($patient_id){

                $currentData = Mst_Patient::where('id', $patient_id)->first();

                if($request->patient_blood_group){
                    $blood_group_id = Mst_Master_Value::where('master_value', 'LIKE', '%' . $request->patient_blood_group . '%')->pluck('id')->first();

                }
                if($request->patient_gender){
                    $patient_gender_id = Mst_Master_Value::where('master_value', 'LIKE', '%' . $request->patient_gender . '%')->pluck('id')->first();
                }

                if($request->patient_dob){
                    $patient_dob = PatientHelper::dateFormatDb($request->patient_dob);
                }

                Mst_Patient::where('id', $patient_id)->update([
                    'patient_name'      => $request->patient_name ?? $currentData->patient_name,
                    'patient_email'     => $request->patient_email ?? $currentData->patient_email,
                    'patient_address'   => $request->patient_address ?? $currentData->patient_address,
                    'patient_gender'    => $patient_gender_id ?? $currentData->patient_gender,
                    'patient_dob'       => $patient_dob ?? $currentData->patient_dob,
                    'patient_blood_group_id' => $blood_group_id ?? $currentData->patient_blood_group_id,
                    'created_at'        => Carbon::now(),
                ]);

                $data['status'] = 1;
                $data['message'] = "Profile updated successfully";
                return response($data);
            }else{
                $data['status'] = 0;
                $data['message'] = "User does not exist";
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

    public function changePassword(Request $request){
        $data=array();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'old_password'      => 'required',
                    'new_password'          => 'required|min:6',
                    'confirm_password'   => 'required|min:6|same:new_password',
                ],
                [
                    'old_password.required'             => 'Old password id required',
                    'new_password.required'             => 'New password required',
                    'confirm_password.required'      => 'Confirm password required',
                    'confirm_password.same'          => 'new passwords and confirm password does not match',
                ]
            );
            if (!$validator->fails()) 
            {
                if (isset($request->old_password) && isset($request->new_password) && isset($request->confirm_password))
                $patient_id = Auth::id();
                $patient=Mst_Patient::where('id',$patient_id)->first();
                if($patient)
                {
                    if(Hash::check($request->old_password, $patient->password)){
                        if(!Hash::check($request->new_password, $patient->password)){
                            $patient->password = Hash::make($request->new_password);
                            $patient->updated_at = Carbon::now();
                            $patient->save();
                            $data['status'] = 1;
                            $data['message'] = "Password changed sussessfully.";
                            return response($data);
                        }else{
                            $data['status'] = 0;
                            $data['message'] = "Your new password is similar to the old Password. Please try another password.";
                            return response($data);
                        }
                    }else{
                        $data['status'] = 0;
                        $data['message'] = "Old password is incorrect.";
                        return response($data);
                    }
                    
                }
                else{
                    $data['status'] = 0;
                    $data['message'] = "User does not exist.";
                    return response($data);
                }
            }
            else
            {
                $data['status'] = 0;
                $data['errors'] = $validator->errors();
                $data['message'] = "Validation errors";
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

    public function logout()
    {
    $user = Auth::user();
    $user->tokens->each(function ($token, $key) {
        $token->delete();
    });

    return response()->json(['message' => 'Logged out successfully']);
    }
}
