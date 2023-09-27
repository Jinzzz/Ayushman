<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PatientHelper;
use Illuminate\Support\Facades\Validator;
use App\Models\Trn_Patient_Family_Member;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Patient;
use App\Models\Trn_Family_Member_Otp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FamilyController extends Controller
{
    public function myFamily()
    {
        $data = array();
        try {
            $patient_id = Auth::id();
            $family_details = array();

            $family_details = PatientHelper::getFamilyDetails($patient_id);
            if ($family_details) {
                $data['status'] = 1;
                $data['message'] = "Data fetched";
                $data['data'] = $family_details;
            } else {
                $data['status'] = 0;
                $data['message'] = "User does not exist";
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

    public function addMember(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'member_name'      => 'required',
                    'member_email'     => 'required|email',
                    'member_mobile'    => ['required', 'regex:/^[0-9]{10}$/'],
                    'member_address'      => 'required',
                    'member_gender'      => 'required',
                    'member_dob'      => 'required',
                    'member_blood_group'      => 'required',
                    'relationship'      => 'required',

                ],
                [
                    'member_name.required'         => 'Name required',
                    'member_email.required'        => 'Email address required',
                    'member_email.email'           => 'Invalid email address',
                    'member_mobile.required'       => 'Mobile number required',
                    'member_mobile.regex'          => 'Invalid mobile number',
                    'member_address.required'         => 'Address required',
                    'member_gender.required'         => 'Gender required',
                    'member_dob.required'         => 'Date of birth required',
                    'member_blood_group.required'         => 'Blood Group required',
                    'relationship.required'         => 'Relationship required',
                ]
            );

            if (!$validator->fails()) {
                if (isset($request->member_name) && isset($request->member_email) && isset($request->member_mobile) && isset($request->member_address) && isset($request->member_gender) && isset($request->member_dob) && isset($request->member_blood_group) && isset($request->relationship)) {
                    $patient_id = Auth::id();
                    $member_dob = PatientHelper::dateFormatDb($request->member_dob);

                    $member_gender_id = $request->member_gender;
                    $blood_group_id =  $request->member_blood_group;
                    $relationship_id = $request->relationship;

                    $addFamilyMember = Trn_Patient_Family_Member::create([
                        'patient_id' => $patient_id,
                        'family_member_name' => $request->member_name,
                        'mobile_number' => $request->member_mobile,
                        'email_address' => $request->member_email,
                        'gender_id' => $member_gender_id,
                        'blood_group_id' => $blood_group_id,
                        'date_of_birth' => $member_dob,
                        'relationship_id' => $relationship_id,
                        'address' => $request->member_address,
                        'created_by' => $patient_id,
                        'is_active' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    $verification_otp = rand(100000, 999999);

                    $otpCreate = Trn_Family_Member_Otp::create([
                        'patient_id' => $patient_id,
                        'family_member_id' => $addFamilyMember->id,
                        'otp' => $verification_otp,
                        'verified' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'otp_expire_at' => Carbon::now()->addMinutes(10),
                    ]);

                    $data['status'] = 1;
                    $data['otp'] = $verification_otp;
                    $data['patient_id'] = $patient_id;
                    $data['family_member_id'] = $addFamilyMember->id;
                    $data['message'] = "Plese enter the otp that send to your registered mobile number.";
                    return response($data);
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Please fill mandatory fields";
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

    public function otpVerification(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'otp' => ['required', 'numeric', 'digits:6'],
                    'family_member_id' => ['required'],
                ],
                [
                    'otp.required' => 'OTP required',
                    'otp.numeric' => 'OTP must be numeric',
                    'otp.digits' => 'OTP must be 6 digits',
                    'family_member_id.required' => 'Family member required',
                ]
            );

            if (!$validator->fails()) {
                if (isset($request->family_member_id) && isset($request->otp)) {
                    $patient_id = Auth::id();
                    $patient = Mst_Patient::where('id', $patient_id)->first();
                    if (!$patient) {
                        $data['status'] = 0;
                        $data['message'] = "User does not exist.";
                        return response($data);
                    }

                    $member = Trn_Patient_Family_Member::where('id', $request->family_member_id)->where('patient_id', $patient_id)->first();
                    if (!$member) {
                        $data['status'] = 0;
                        $data['message'] = "Member does not exist.";
                        return response($data);
                    }

                    $lastInsertedRow = Trn_Family_Member_Otp::where('family_member_id', $request->family_member_id)
                        ->where('patient_id', $patient_id)
                        ->where('otp', $request->otp)
                        ->latest('id')
                        ->first();

                    if ($lastInsertedRow && $lastInsertedRow->otp == $request->otp) {
                        if (Carbon::now()->lessThanOrEqualTo($lastInsertedRow->otp_expire_at)) {

                            Trn_Family_Member_Otp::where('id', $lastInsertedRow->id)->update([
                                'updated_at' => Carbon::now(),
                                'verified' => 1,
                            ]);

                            $data['status'] = 1;
                            $data['message'] = "OTP Verified successfully";
                            return response($data);
                        } else {

                            $data['status'] = 0;
                            $data['message'] = "OTP Expired";
                            return response($data);
                        }
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "OTP doesn't match";
                        return response($data);
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Patient id / Otp type / Otp is required";
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

    public function reSendOtp(Request $request)
    {
        $data = array();
        try {
            $patient_id = Auth::id();
            $validator = Validator::make(
                $request->all(),
                [
                    'family_member_id' => ['required'],
                ],
                [
                    'family_member_id.required' => 'Family member required',
                ]
            );

            if (!$validator->fails()) {
                $patient = Mst_Patient::where('id', $patient_id)->first();
                if (!$patient) {
                    $data['status'] = 0;
                    $data['message'] = "User does not exist.";
                    return response($data);
                }

                $member = Trn_Patient_Family_Member::where('id', $request->family_member_id)->where('patient_id', $patient_id)->first();
                if (!$member) {
                    $data['status'] = 0;
                    $data['message'] = "Member does not exist.";
                    return response($data);
                }

                $lastInsertedRow = Trn_Family_Member_Otp::where('patient_id', $patient_id)
                    ->where('family_member_id', $request->family_member_id)
                    ->latest('id')
                    ->first();

                if ($lastInsertedRow) {
                    $verification_otp = rand(100000, 999999);
                    $resendOtp = Trn_Family_Member_Otp::where('id', $lastInsertedRow->id)->update([
                        'updated_at' => Carbon::now(),
                        'otp' => $verification_otp,
                        'verified' => 0,
                        'otp_expire_at' => Carbon::now()->addMinutes(10),
                    ]);

                    $data['status'] = 1;
                    $data['otp'] = $verification_otp;
                    $data['patient_id'] = $patient_id;
                    $data['family_member_id'] = $request->family_member_id;
                    $data['message'] = "OTP resent successfully.";
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

    public function editFamilyMember(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'family_member_id' => ['required'],
                ],
                [
                    'family_member_id.required' => 'Family member id is required',
                ]
            );
            if (!$validator->fails()) {
                if (isset($request->family_member_id)) {

                    $patient_id = Auth::id();
                    $family_member_id = $request->family_member_id;
                    $family_member_details = array();
                    // Fetching that family member's details 
                    $member = Trn_Patient_Family_Member::join('mst_patients', 'trn_patient_family_member.patient_id', 'mst_patients.id')
                        ->join('mst_master_values as gender', 'trn_patient_family_member.gender_id', '=', 'gender.id')
                        ->join('mst_master_values as blood_group', 'trn_patient_family_member.blood_group_id', '=', 'blood_group.id')
                        ->join('mst_master_values as relationship', 'trn_patient_family_member.relationship_id', '=', 'relationship.id')
                        ->select('trn_patient_family_member.id', 'trn_patient_family_member.family_member_name', 'trn_patient_family_member.email_address', 'trn_patient_family_member.mobile_number', 'gender.master_value as gender_name','blood_group.master_value as blood_group', 'trn_patient_family_member.date_of_birth','trn_patient_family_member.address', 'relationship.master_value as relationship')
                        ->where('trn_patient_family_member.id', $family_member_id)
                        ->where('trn_patient_family_member.patient_id', $patient_id)
                        ->where('trn_patient_family_member.is_active', 1)
                        ->first();

                    if ($member) {
                        $carbonDate = Carbon::parse($member->date_of_birth);
                        $year = $carbonDate->year;
                        $currentYear = Carbon::now()->year;

                        $family_member_details[] = [
                            'member_id' => $patient_id,
                            'family_member_id' => $family_member_id,
                            'member_name' => $member->family_member_name,
                            'relationship' => $member->relationship,
                            'mobile_number' => $member->mobile_number,
                            'email_address' => $member->email_address,
                            'age' => $currentYear - $year,
                            'dob' => Carbon::parse($member->date_of_birth)->format('d-m-Y'),
                            'gender' => $member->gender_name,
                            'blood_group' => $member->blood_group,
                            'address' => $member->address,
                        ];

                        $data['status'] = 1;
                        $data['message'] = "Data fetched";
                        $data['data'] = $family_member_details;
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "User does not exist";
                    }
                    return response($data);
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Please provide a valid family member id.";
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

    // update family member details 
    public function updateFamilyMember(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'member_name'      => 'required',
                    'member_email'     => 'required|email',
                    'member_mobile'    => ['required', 'regex:/^[0-9]{10}$/'],
                    'member_address'      => 'required',
                    'member_gender'      => 'required',
                    'member_dob'      => 'required',
                    'member_blood_group'      => 'required',
                    'relationship'      => 'required',
                    'family_member_id'      => 'required',
                ],
                [
                    'member_name.required'         => 'Name required',
                    'member_email.required'        => 'Email address required',
                    'member_email.email'           => 'Invalid email address',
                    'member_mobile.required'       => 'Mobile number required',
                    'member_mobile.regex'          => 'Invalid mobile number',
                    'member_address.required'         => 'Address required',
                    'member_gender.required'         => 'Gender required',
                    'member_dob.required'         => 'Date of birth required',
                    'member_blood_group.required'         => 'Blood Group required',
                    'relationship.required'         => 'Relationship required',
                    'family_member_id.required' => 'Family member id is required',
                ]
            );

            if (!$validator->fails()) {
                if (isset($request->family_member_id) && isset($request->member_name) && isset($request->member_email) && isset($request->member_mobile) && isset($request->member_address) && isset($request->member_gender) && isset($request->member_dob) && isset($request->member_blood_group) && isset($request->relationship)) {
                    $patient_id = Auth::id();
                    $member_dob = PatientHelper::dateFormatDb($request->member_dob);

                    $member_gender_id = $request->member_gender;
                    $blood_group_id =  $request->member_blood_group;
                    $relationship_id = $request->relationship;

                    $addFamilyMember = Trn_Patient_Family_Member::where('id',$request->family_member_id)->update([
                        'patient_id' => $patient_id,
                        'family_member_name' => $request->member_name,
                        'mobile_number' => $request->member_mobile,
                        'email_address' => $request->member_email,
                        'gender_id' => $member_gender_id,
                        'blood_group_id' => $blood_group_id,
                        'date_of_birth' => $member_dob,
                        'relationship_id' => $relationship_id,
                        'address' => $request->member_address,
                        'created_by' => $patient_id,
                        'is_active' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    $data['status'] = 1;
                    $data['message'] = "Family member details updated successfully.";
                    return response($data);
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Please fill mandatory fields";
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

    // Delete family member 
    public function deleteFamilyMember(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'family_member_id' => ['required'],
                ],
                [
                    'family_member_id.required' => 'Family member id is required',
                ]
            );
            if (!$validator->fails()) {
                if (isset($request->family_member_id)) {
                    $id = $request->family_member_id;
                    $trn_family_member = Trn_Patient_Family_Member::findOrFail($id);
                    $trn_family_member->is_active = 0;
                    $trn_family_member->save();
                    $trn_family_member->delete();

                    $data['status'] = 1;
                    $data['message'] = "Deleted successfully";       
                    return response($data);
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Please provide a valid family member id.";
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
}
