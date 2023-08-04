<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Mst_Wellness;
use App\Models\Mst_Branch;
use App\Models\Mst_Patient;
use App\Models\Trn_Patient_Family_Member;
use App\Models\Trn_Consultation_Booking;
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
                    $all_wellness = Mst_Wellness::where('is_active', 1)->where('branch_id', $request->branch_id)->get();
                    $wellness_list = [];

                if (!$all_wellness->isEmpty()) {

                    foreach ($all_wellness as $wellness) {
                        $wellness_list[] = [
                        'id' => $wellness->id,
                        'wellness_name' => $wellness->wellness_name,
                        'wellness_cost' => $wellness->wellness_cost,
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
                    
                    $wellness = Mst_Wellness::where('id',$request->wellness_id)->where('branch_id', $request->branch_id)->where('is_active', 1)->first();
                    $branch_name = Mst_Branch::where('branch_id', $request->branch_id)->where('is_active', 1)->value('branch_name');
                     
                    $wellness_details = [];
                    if (!empty($wellness)) {
                        $wellness_details[] = [
                            'id' => $wellness->id,
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
                    $wellness = Mst_Wellness::where('id', $request->wellness_id)->where('is_active', 1)->where('branch_id', $request->branch_id)->first();
                    $patientDetails = [];

                    if($request->yourself == 1){
                        $accountHolder = Mst_Patient::where('id',$patient_id)->first();
                        $patientDetails[] = [
                            'id' => $accountHolder->id,
                            'yourself' => 1,
                            'booking_date' => Carbon::parse($request->booking_date)->format('d-m-Y'),
                            'member_name' => $accountHolder->patient_name       ,
                            'dob' => Carbon::parse($accountHolder->patient_dob)->format('d-m-Y'),
                            'gender' => $accountHolder->patient_gender,
                            'mobile_number' => $accountHolder->patient_mobile,
                            'email_address' => $accountHolder->patient_email,
                        ];
                    }
                    else{
                        if(isset($request->member_id)){
                            $member = Trn_Patient_Family_Member::join('mst_patients','trn_patient_family_member.patient_id','mst_patients.id') 
                            ->join('sys_gender','trn_patient_family_member.gender_id','sys_gender.id')
                            ->join('sys_relationships','trn_patient_family_member.relationship_id','sys_relationships.id')
                            ->select('trn_patient_family_member.id','trn_patient_family_member.mobile_number','trn_patient_family_member.email_address','trn_patient_family_member.family_member_name','sys_gender.gender_name','trn_patient_family_member.date_of_birth','sys_relationships.relationship')
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
                    $data['booking_id'] = $booking_id ?? '';
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
                    $wellness = Mst_Wellness::where('id', $request->wellness_id)->where('is_active', 1)->where('branch_id', $request->branch_id)->first();
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
                        'booking_type_id' => 2,
                        'wellness_id' => $request->wellness_id,
                        'patient_id' => $patient_id,
                        'branch_id' => $request->branch_id, 
                        'booking_date' => $booking_date,
                        'booking_status_id' => 2,
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
                        if($bookingDetails->booking_status_id == 3){
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
                            $lastInsertedId = $booking_id;
                        }
                    }else{
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
