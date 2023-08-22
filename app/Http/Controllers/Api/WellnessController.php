<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Mst_Wellness;
use App\Models\Mst_Branch;
use App\Models\Mst_Patient;
use App\Models\Mst_Master_Value;
use App\Models\Trn_Patient_Family_Member;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Patient_Membership_Booking;
use App\Models\Mst_Membership_Package_Wellness;
use App\Models\Trn_Patient_Wellness_Sessions;
use Carbon\Carbon;
use App\Helpers\PatientHelper;

class WellnessController extends Controller
{
    public function wellnessSearchList(Request $request){
        $data=array();
        try
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'branch_id' => ['required'],
                    'booking_date' => ['required'],
                ],
                [
                    'branch_id.required' => 'Branch required',
                    'booking_date.required' => 'Booking date required',
                ]
            );

            if (!$validator->fails()) 
            {
                if(isset($request->booking_date) && isset($request->branch_id)){

                    $patient_id = Auth::id();
                    $all_wellness = Mst_Wellness::where('is_active', 1)->where('branch_id', $request->branch_id)->get();
                    $wellness_list = [];

                   $allWellnessIds = [];

                    $checkMembership = Mst_Patient::where('id',$patient_id)->value('available_membership');
                    if($checkMembership == 1){
                        $membership = Mst_Patient_Membership_Booking::where('patient_id', Auth::id())->latest()->first();
                        // array of all wellness ids included in that membership package.

                        if(!empty($membership)){
                            $allWellnessIds = Mst_Membership_Package_Wellness::where('package_id', $membership->membership_package_id)
                            ->where('is_active', 1)
                            ->pluck('wellness_id')
                            ->toArray();

                            // $commaSeparatedWellnesses = implode(',', $allWellnessIds);
                        }
                    }

                    if (!$all_wellness->isEmpty()) {
                    foreach ($all_wellness as $wellness) {
                        $is_included = 0;
                            if (in_array($wellness->wellness_id, $allWellnessIds)) {
                                $checkWellness = Mst_Membership_Package_Wellness::where('package_id',$membership->membership_package_id)
                                ->where('wellness_id',$wellness->wellness_id)
                                ->where('is_active',1)
                                ->first();
                                
                            if (!empty($checkWellness)) {
                                $bookedCountWellness = Trn_Patient_Wellness_Sessions::where('membership_patient_id',$membership->membership_package_id)
                                ->where('wellness_id',$wellness->wellness_id)
                                ->where('created_at','>=',$membership->created_at)
                                ->where('created_at','<=',$membership->membership_expiry_date)
                                ->where('status',1)
                                ->count();

                                if($bookedCountWellness < $checkWellness->maximum_usage_limit){
                                    $is_included = 1;
                                }
                            }
                            }

                        $wellness_list[] = [
                        'id' => $wellness->wellness_id,
                        'wellness_name' => $wellness->wellness_name,
                        'wellness_cost' => $wellness->wellness_cost,
                        'is_included' => $is_included,
                        ];
                    }


                    $data['status'] = 1;
                    $data['message'] = "Data fetched";
                    $data['booking_date'] = $request->booking_date;
                    $data['branch_id'] = $request->branch_id;
                    $data['data'] = $wellness_list;
                    return response()->json($data);
                } 
                else {
                    $data['status'] = 0;
                    $data['message'] = "No wellness found";
                    return response()->json($data);
                }
                }
                else{
                    $data['status'] = 0;
                    $data['message'] = "Please fill mandatory fields";
                    return response()->json($data);
                }
            }
            else{
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

    public function wellnessDetails(Request $request){
        $data=array();
        try
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'wellness_id' => ['required'],
                    'booking_date' => ['required'],
                    'branch_id' => ['required'],
                ],
                [
                    'wellness_id.required' => 'Wellness required',
                    'booking_date.required' => 'Booking date required',
                    'branch_id.required' => 'Branch required',
                ]
            );

            if (!$validator->fails()) 
            {
                if(isset($request->booking_date) && isset($request->wellness_id) && isset($request->branch_id)){
                    
                    $wellness = Mst_Wellness::where('wellness_id',$request->wellness_id)->where('branch_id', $request->branch_id)->where('is_active', 1)->first();
                    $branch_name = Mst_Branch::where('branch_id', $request->branch_id)->where('is_active', 1)->value('branch_name');
                     
                    $wellness_details = [];
                    if (!empty($wellness)) {
                        $wellness_details[] = [
                            'id' => $wellness->wellness_id,
                            'wellness_name' => $wellness->wellness_name,
                            'wellness_description' => $wellness->wellness_description,
                            'wellness_cost' => $wellness->wellness_cost,
                            'wellness_inclusions' => $wellness->wellness_inclusions,
                            'wellness_terms_conditions' => $wellness->wellness_terms_conditions,
                        ];
                        
                        
                        $data['status'] = 1;
                        $data['message'] = "Data fetched";
                        $data['branch_id'] = $request->branch_id;
                        $data['branch_name'] = $branch_name;
                        $data['booking_date'] = $request->booking_date;
                        $data['data'] = $wellness_details;
                        return response()->json($data);
                    }
                    else{
                        $data['status'] = 0;
                        $data['message'] = "No details found";
                        return response()->json($data);
                    }
                }
                else{
                    $data['status'] = 0;
                    $data['message'] = "Please fill mandatory fields";
                    return response()->json($data);
                }
            }
            else{
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

    public function wellnessSummary(Request $request){
        $data=array();
        try
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'branch_id' => ['required'],
                    'booking_date' => ['required'],
                    'wellness_id' => ['required'],
                    'yourself' => ['required'],
                ],
                [
                    'branch_id.required' => 'Branch required',
                    'booking_date.required' => 'Booking date required',
                    'wellness_id.required' => 'Wellness required',
                    'yourself.required' => 'Yourself required',
                ]
            );

            if (!$validator->fails()) 
            {
                if(isset($request->branch_id) && isset($request->wellness_id) && isset($request->yourself) && isset($request->booking_date)){
                    $patient_id = Auth::id();
                    $branch_name = Mst_Branch::where('branch_id', $request->branch_id)->where('is_active', 1)->value('branch_name');
                    $wellness = Mst_Wellness::where('wellness_id', $request->wellness_id)->where('is_active', 1)->where('branch_id', $request->branch_id)->first();
                    $patientDetails = [];

                    if($request->yourself == 1){
                        $accountHolder = Mst_Patient::where('id',$patient_id)->first();
                        $patient_gender_name = Mst_Master_Value::where('id',$accountHolder->patient_gender)->value('master_value');
                        $patientDetails[] = [
                            'id' => $accountHolder->id,
                            'yourself' => 1,
                            'booking_date' => Carbon::parse($request->booking_date)->format('d-m-Y'),
                            'member_name' => $accountHolder->patient_name       ,
                            'dob' => Carbon::parse($accountHolder->patient_dob)->format('d-m-Y'),
                            'gender' => $patient_gender_name,
                            'mobile_number' => $accountHolder->patient_mobile,
                            'email_address' => $accountHolder->patient_email,
                        ];
                    }
                    else{
                        if(isset($request->member_id)){
                            
                            $member = Trn_Patient_Family_Member::join('mst_patients','trn_patient_family_member.patient_id','mst_patients.id') 
                            ->join('mst_master_values','trn_patient_family_member.gender_id','mst_master_values.id')
                            ->select('trn_patient_family_member.id','trn_patient_family_member.mobile_number','trn_patient_family_member.email_address','trn_patient_family_member.family_member_name','mst_master_values.master_value as gender_name','trn_patient_family_member.date_of_birth')
                            ->where('trn_patient_family_member.patient_id',$patient_id)
                            ->where('trn_patient_family_member.id',$request->member_id)
                            ->where('trn_patient_family_member.is_active',1)
                            ->first();
                            
                            $patientDetails[] = [
                                'id' => $member->id,
                                'yourself' => 0,
                                'booking_date' => Carbon::parse($request->booking_date)->format('d-m-Y'),
                                'member_name' => $member->family_member_name,
                                'dob' => Carbon::parse($member->date_of_birth)->format('d-m-Y'),
                                'gender' => $member->gender_name,
                                'mobile_number' => $member->mobile_number,
                                'email_address' => $member->email_address,
                            ];
                        }
                        else{
                            $data['status'] = 0;
                            $data['message'] = "Please provide member_id";
                            return response()->json($data);
                        }
                        
                    }

                    $paymentDetails[] = [
                        'consultation_fee' => $wellness->wellness_cost,
                        'total_amount' => $wellness->wellness_cost,
                    ];

                    $data['status'] = 1;
                    $data['message'] = "Data Fetched";
                    $data['branch_name'] = $branch_name;
                    $data['package_name'] = $wellness->wellness_name;
                    $data['patient_details'] = $patientDetails;
                    $data['payment_details'] = $paymentDetails;
                    // $data['booking_id'] = $booking_id ?? '';
                    return response($data);

                }
                else{
                    $data['status'] = 0;
                    $data['message'] = "Please fill mandatory fields";
                    return response()->json($data);
                }
            }
            else{
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

    public function wellnessConfirmation(Request $request){
        $data=array();
        try
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'wellness_id' => ['required'],
                    'yourself' => ['required'],
                    'reschedule_key' => ['required'],
                    'branch_id' => ['required'],
                    'booking_date' => ['required'],
                    'reschedule_key' => ['required'],
                ],
                [
                    'branch_id.required' => 'Branch required',
                    'booking_date.required' => 'Booking date required',
                    'wellness_id.required' => 'Wellness required',
                    'yourself.required' => 'Yourself required',
                    'reschedule_key.required' => 'Reschedule key required',
                    'reschedule_key.required' => 'Reschedule key required',
                ]
            );

            if (!$validator->fails()) 
            {
                if(isset($request->booking_date)){
                    $patient_id = Auth::id();
                    $wellness = Mst_Wellness::where('wellness_id', $request->wellness_id)->where('is_active', 1)->where('branch_id', $request->branch_id)->first();
                    if ($request->reschedule_key == 1) {
                        if (!$request->has('booking_id')) {
                            $data['status'] = 0;
                            $data['message'] = "Booking id is required";
                            return response($data);
                        } else {
                            $booking_id = $request->booking_id;
                        }
                    }

                    $yourself = $request->yourself;
                    $booking_date = PatientHelper::dateFormatDb($request->booking_date);
                    $newRecordData = [
                        'booking_type_id' => 85,
                        'wellness_id' => $request->wellness_id,
                        'patient_id' => $patient_id,
                        'branch_id' => $request->branch_id, 
                        'booking_date' => $booking_date,
                        'booking_status_id' => 88,
                        'booking_fee' => $wellness->wellness_cost,
                        'is_for_family_member' => 0,
                        'family_member_id' => 0,
                        'created_at' => Carbon::now(),
                    ];

                    if($yourself == 0){
                        if (isset($request->member_id)){
                            $familyMemberData = [
                                'is_for_family_member' => 1,
                                'family_member_id' => $request->member_id,
                            ];
                            $newRecordData = $familyMemberData + $newRecordData;

                            $bookedMemberDetails = Trn_Patient_Family_Member::where('id', $request->member_id)->first();
                        }else{
                            $data['status'] = 0;
                            $data['message'] = "Member is required";
                            return response($data);
                        }
                    }

                    $checkAlreadyBooked =  Trn_Consultation_Booking::where('patient_id',Auth::id())->where('booking_date',$newRecordData['booking_date'])->where('wellness_id',$newRecordData['wellness_id'])->where('family_member_id',$newRecordData['family_member_id'])->first();
                    if($checkAlreadyBooked){
                        $data['status'] = 0;
                        $data['message'] = "Already booked";
                        return response($data);
                    }


                    if(isset($booking_id)){
                        // Update existing data
                        $bookingDetails = Trn_Consultation_Booking::where('id', $booking_id)->first();
                        if($bookingDetails->booking_status_id == 89 || ($bookingDetails->booking_status_id == 90 && $bookingDetails->booking_date < Carbon::now())){
                        $createdRecord = Trn_Consultation_Booking::create($newRecordData);
                        $lastInsertedId = $createdRecord->id;
                        $leadingZeros = str_pad('', 3 - strlen($lastInsertedId), '0', STR_PAD_LEFT);
                        $bookingRefNo = 'BRN' . $leadingZeros . $lastInsertedId;
                        $updateConsultation = Trn_Consultation_Booking::where('id', $lastInsertedId)->update([
                        'updated_at' => Carbon::now(),
                        'booking_reference_number' => $bookingRefNo
                        ]);
                        }else{
                            $updateRecord = Trn_Consultation_Booking::where('id', $booking_id)->update($newRecordData);
                            $bookingRefNo = $bookingDetails->booking_reference_number;
                            $lastInsertedId = intval($booking_id);
                        }
                    }
                    else{
                       // Create new data 
                       $createdRecord = Trn_Consultation_Booking::create($newRecordData);
                       $lastInsertedId = $createdRecord->id;
                       $leadingZeros = str_pad('', 3 - strlen($lastInsertedId), '0', STR_PAD_LEFT);
                       $bookingRefNo = 'BRN' . $leadingZeros . $lastInsertedId;
                       $updateConsultation = Trn_Consultation_Booking::where('id', $lastInsertedId)->update([
                       'updated_at' => Carbon::now(),
                       'booking_reference_number' => $bookingRefNo
                       ]);
                        
                    }

                    $booking_details = [];

                    $booking_details[] = [
                        'booking_id' => $lastInsertedId,
                        'booking_referance_number' => $bookingRefNo,
                        'booking_for' => $wellness->wellness_name,
                        'booking_date' => $request->booking_date,
                    ];

                    $data['status'] = 1;
                    $data['message'] = "Booking Confirmed";
                    $data['booking_details'] = $booking_details;
                    return response($data);

                }
                else{
                    $data['status'] = 0;
                    $data['message'] = "Please fill mandatory fields";
                    return response()->json($data);
                }
            }
            else{
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
}
