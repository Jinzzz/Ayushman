<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mst_Branch;
use App\Models\Mst_TimeSlot;
use App\Models\Mst_User;
use App\Models\Trn_Staff_Leave;
use App\Models\Mst_Doctor;
use App\Models\Mst_Patient;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Master_Value;
use App\Models\Trn_Patient_Family_Member;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helpers\PatientHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DoctorBookingController extends Controller
{
    public function getBranches(){
        $data=array();
        try{
            $branches = Mst_Branch::where('is_active', 1)->get(['branch_id', 'branch_name'])->toArray();
            if($branches){
                $data['status'] = 1;
                $data['message'] = "Data fetched.";
                $data['data'] = $branches;
            }else{
                $data['status'] = 0;
                $data['message'] = "No branches found.";
            }
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

    public function doctorsList(Request $request){
        $data=array();
        try{
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
                if (isset($request->branch_id) && !empty($request->booking_date)) {

                    $doctorONLeave = Trn_Staff_Leave::where('leave_date', $request->booking_date)->pluck('user_id')->toArray();

                    $day_of_week = PatientHelper::getWeekDay($request->booking_date);
                    $weekDayId = Mst_Master_Value::where('master_value', 'LIKE', '%' . $day_of_week . '%')->pluck('master_value_id')->first();
                    $allotted_doctors = Mst_TimeSlot::where('week_day',$weekDayId)->where('is_active', 1)->distinct()->pluck('staff_id')->toArray();

                    $doctorONLeaveCollection = collect($doctorONLeave);
                    $allottedDoctorsCollection = collect($allotted_doctors);
                    $filteredDoctors = $allottedDoctorsCollection->diff($doctorONLeaveCollection);
                    $filteredDoctorsArray = $filteredDoctors->values()->all();

                    $queries = Mst_User::join('mst__doctors', 'mst_users.user_id', '=', 'mst__doctors.user_id')
                   ->join('mst_branches', 'mst__doctors.branch_id', '=', 'mst_branches.branch_id')
                   ->join('mst__designations', 'mst__doctors.designation_id', '=', 'mst__designations.id')
                   ->join('trn_user_profiles', 'mst_users.user_id', '=', 'trn_user_profiles.user_id')
                   ->select('mst_users.user_id as doctor_id', 'mst_users.username as name', 'mst_branches.branch_name as branch_name', 'mst__designations.designation as designation', 'trn_user_profiles.profile_image')
                   ->where('mst_users.user_type_id', 3)
                   ->where('mst__doctors.branch_id', $request->branch_id)
                   ->whereIn('mst_users.user_id', $filteredDoctorsArray);

                    if(isset($request->search_doctor_name)){
                        $queries = $queries->where('mst_users.username', 'like', '%' . $request->search_doctor_name . '%');
                    }

                    if(isset($request->search_branch_name)){
                        $queries = $queries->where('mst_branches.branch_name', 'like', '%' . $request->search_branch_name . '%');
                    }

                    if(isset($request->search_designation_name)){
                        $queries = $queries->where('mst__designations.designation', 'like', '%' . $request->search_designation_name . '%');
                    }
            
                    $doctorsList = $queries->get();
                    foreach ($doctorsList as $doctor) {
                        $doctor->profile_image = 'https://ayushman-patient.hexprojects.in/assets/uploads/doctor_profile/' . $doctor->profile_image;
                    }

                    $data['status'] = 1;
                    $data['message'] = "Data fetched";
                    $data['data'] = $doctorsList;
                    return response($data);
                }else{
                    $data['status'] = 0;
                    $data['message'] = "Please select branch and date";
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

    public function doctorsDetails(Request $request){
        $data=array();
        try{
            $validator = Validator::make(
                $request->all(),
                [
                    'doctor_id' => ['required'],
                ],
                [
                    'doctor_id.required' => 'Doctor required',
                ]
            );
            if (!$validator->fails()) 
            {
                if(isset($request->doctor_id)){
                    $doctorDetails = Mst_User::join('mst__doctors', 'mst_users.user_id', '=', 'mst__doctors.user_id')
                    ->join('mst__designations', 'mst__doctors.designation_id', '=', 'mst__designations.id')
                    ->join('trn_user_profiles', 'mst_users.user_id', '=', 'trn_user_profiles.user_id')
                    ->leftJoin('mst_branches', 'mst__doctors.branch_id', '=', 'mst_branches.branch_id')
                    ->select('mst_users.user_id as doctor_id', 'mst_users.username as name', 'mst_branches.branch_name as branch_name', 'mst__doctors.qualification', 'trn_user_profiles.address', 'trn_user_profiles.profile_image', 'mst__designations.designation as designation')
                    ->where('mst_users.user_type_id', 3)
                    ->where('mst_users.user_id', $request->doctor_id)
                    ->first();

                    if ($doctorDetails) {
                        $doctorDetails->profile_image = 'https://ayushman-patient.hexprojects.in/assets/uploads/doctor_profile/' . $doctorDetails->profile_image;
                        $doctorDetails->description = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
                        $data['status'] = 1;
                        $data['message'] = "Data fetched";
                        $data['data'] = $doctorDetails;
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "No doctor found";
                    }

                    return response($data);

                }else{
                    $data['status'] = 0;
                    $data['message'] = "Please select a doctor";
                    return response($data);
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

    public function doctorsAvailability(Request $request){
        $data=array();
        $time_slots=array();
        try{
            $validator = Validator::make(
                $request->all(),
                [
                    'doctor_id' => ['required'],
                    'branch_id' => ['required'],
                    'booking_date' => ['required'],
                    'reschedule_key' => ['required'],
                ],
                [
                    'doctor_id.required' => 'Doctor required',
                    'branch_id.required' => 'Branch required',
                    'booking_date.required' => 'Booking date required',
                    'reschedule_key.required' => 'Reschedule key required',
                ]
            );
            if (!$validator->fails()) 
            {
                if (isset($request->branch_id) && isset($request->doctor_id) && isset($request->booking_date ) && isset($request->reschedule_key )) {
                    
                    $doctor_details = Mst_User::where('user_id',$request->doctor_id)->first();
                    $doctor_name = $doctor_details->username;

                    if ($request->reschedule_key == 1) {
                        if (!$request->has('booking_id')) {
                            $data['status'] = 0;
                            $data['message'] = "Booking id is required";
                            return response($data);
                        } else {
                            $booking_id = $request->booking_id;
                        }
                    }
                    
                    $day_of_week = PatientHelper::getWeekDay($request->booking_date);
                    $weekDayId = Mst_Master_Value::where('master_value', 'LIKE', '%' . $day_of_week . '%')->pluck('master_value_id')->first();
            
                    $timeSlots = Mst_TimeSlot::where('staff_id', $request->doctor_id)
                        ->where('week_day', $weekDayId)
                        ->where('is_active', 1)
                        ->get();
                    
                    if($timeSlots){
                        $booking_date = PatientHelper::dateFormatDb($request->booking_date);

                        $currentDate = Carbon::now()->format('Y-m-d');
                        $currentTime = Carbon::now()->format('H:i:s');

                        $time_slots = [];
                        foreach ($timeSlots as $timeSlot) {
                        $booked_tokens = Trn_Consultation_Booking::where('booking_date', $booking_date)
                            ->where('time_slot_id', $timeSlot->id)
                            ->whereIn('booking_status_id', [1, 2])
                            ->count();
            
                        $available_slots = $timeSlot->no_tokens - $booked_tokens;

                        if ($available_slots <= 0 || ($timeSlot->time_to <= $currentTime && $request->booking_date == $currentDate)) {
                            $time_slots[] = [
                                'time_slot_id' => $timeSlot->id,
                                'time_from' => Carbon::parse($timeSlot->time_from)->format('h:i A'),
                                'time_to' => Carbon::parse($timeSlot->time_to)->format('h:i A'),
                                'available_slots' => 0,
                            ];
                        } elseif ($request->booking_date == $currentDate && $timeSlot->time_from <= $currentTime && $timeSlot->time_to >= $currentTime) {
                            $slots = PatientHelper::getTimeSlot($timeSlot->avg_time_patient, $timeSlot->time_from, $timeSlot->time_to);
                            $slots = array_slice($slots, $booked_tokens);
            
                            $available_slots = 0;
                            foreach ($slots as $slot) {
                                if ($slot['slot_start_time'] > $currentTime) {
                                    $available_slots++;
                                }
                            }
            
                            $time_slots[] = [
                                'time_slot_id' => $timeSlot->id,
                                'time_from' => Carbon::parse($timeSlot->time_from)->format('h:i A'),
                                'time_to' => Carbon::parse($timeSlot->time_to)->format('h:i A'),
                                'available_slots' => $available_slots,
                            ];
                        } else {
                            $available_slots = ($available_slots < 0) ? 0 : $available_slots;
                            $time_slots[] = [
                                'time_slot_id' => $timeSlot->id,
                                'time_from' => Carbon::parse($timeSlot->time_from)->format('h:i A'),
                                'time_to' => Carbon::parse($timeSlot->time_to)->format('h:i A'),
                                'available_slots' => $available_slots,
                            ];
                        }
                    }
                        $data['status'] = 1;
                        $data['message'] = "Data fetched.";
                        $data['doctor_name'] = $doctor_name;
                        $data['data'] = $time_slots;
                        $data['booking_id'] = $booking_id ?? '';
                        return response($data);
                    }else{
                        $data['status'] = 0;
                        $data['message'] = "No slots available on this date.";
                        $data['data'] = $time_slots;
                    return response($data);
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Please fill mandatory fields";
                    return response($data);
                }
            }else{
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

    public function bookingDetails(Request $request){
        $data=array();
        try{
            $validator = Validator::make(
                $request->all(),
                [
                    'doctor_id' => ['required'],
                    'branch_id' => ['required'],
                    'slot_id' => ['required'],
                    'booking_date' => ['required'],
                    'reschedule_key' => ['required'],
                ],
                [
                    'doctor_id.required' => 'Doctor required',
                    'branch_id.required' => 'Branch required',
                    'slot_id.required' => 'Slot required',
                    'booking_date.required' => 'Booking date required',
                    'reschedule_key.required' => 'Reschedule key required',
                ]
            );
            if (!$validator->fails()) 
            {
                if (isset($request->doctor_id) && isset($request->branch_id) && isset($request->booking_date) && isset($request->slot_id ) && isset($request->reschedule_key )) {
                    $patient_id = Auth::id();
                    if ($request->reschedule_key == 1) {
                        if (!$request->has('booking_id')) {
                            $data['status'] = 0;
                            $data['message'] = "Booking id is required";
                            return response($data);
                        } else {
                            $booking_id = $request->booking_id;
                        }
                    }

                    $family_details=array();

                    $family_details = PatientHelper::getFamilyDetails($patient_id);

                    $available_slots = PatientHelper::recheckAvailability($request->booking_date, $request->slot_id, $request->doctor_id);

                    if($available_slots > 0){
                        $data['status'] = 1;
                        $data['message'] = "Data Fetched";
                        $data['data'] = $family_details;
                        $data['booking_id'] = $booking_id ?? '';
                        return response($data);
                    }else{
                        $data['status'] = 0;
                        $data['message'] = "No slots available";
                    return response($data);
                    }
                }
                else{
                    $data['status'] = 0;
                    $data['message'] = "Please fill mandatory fields";
                    return response($data);
                }
            }else{
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

    public function bookingSummary(Request $request){
        $data=array();
        $doctorDetails=array();
        try{
            $validator = Validator::make(
                $request->all(),
                [
                    'doctor_id' => ['required'],
                    'member_id' => ['required'],
                    'slot_id' => ['required'],
                    'booking_date' => ['required'],
                    'yourself' => ['required'],
                    'reschedule_key' => ['required'],
                ],
                [
                    'doctor_id.required' => 'Doctor required',
                    'member_id.required' => 'Member id required',
                    'slot_id.required' => 'Slot required',
                    'booking_date.required' => 'Booking date required',
                    'yourself.required' => 'Yourself is required',
                    'reschedule_key.required' => 'Reschedule key required',
                ]
            );
            if (!$validator->fails()) 
            {
                if (isset($request->member_id) && isset($request->yourself) && isset($request->slot_id) && isset($request->doctor_id) && isset($request->booking_date ) && isset($request->reschedule_key)) {
                    $patient_id = Auth::id();
                    if ($request->reschedule_key == 1) {
                        if (!$request->has('booking_id')) {
                            $data['status'] = 0;
                            $data['message'] = "Booking id is required";
                            return response($data);
                        } else {
                            $booking_id = $request->booking_id;
                        }
                    }

                    $slotDetails = Mst_TimeSlot::find($request->slot_id);
                    $time_from = date('h:i A', strtotime($slotDetails->time_from));
                    $time_to = date('h:i A', strtotime($slotDetails->time_to));
                    ;
                    $doctor = Mst_User::join('mst__doctors', 'mst_users.user_id', '=', 'mst__doctors.user_id')
                    ->join('mst__designations', 'mst__doctors.designation_id', '=', 'mst__designations.id')
                    ->join('trn_user_profiles', 'mst_users.user_id', '=', 'trn_user_profiles.user_id')
                    ->leftJoin('mst_branches', 'mst__doctors.branch_id', '=', 'mst_branches.branch_id')
                    ->select('mst_users.user_id as doctor_id', 'mst_users.username as name', 'mst_branches.branch_name as branch_name', 'mst__doctors.qualification', 'mst__doctors.consultation_fee', 'trn_user_profiles.profile_image', 'mst__designations.designation as designation')
                    ->where('mst_users.user_type_id', 3)
                    ->where('mst_users.user_id', $request->doctor_id)
                    ->first();

                    $doctorDetails[] = [
                        'doctor_id' => $doctor->doctor_id,
                        'doctor_name' => $doctor->name,
                        'doctor_designatiom' => $doctor->designation,
                        'doctor_branch' => $doctor->branch_name,
                        'doctor_profile_image' => 'https://ayushman-patient.hexprojects.in/assets/uploads/doctor_profile/' . $doctor->profile_image,
                    ];

                    $patientDetails = [];

                    if($request->yourself == 1){
                        $accountHolder = Mst_Patient::where('id',$patient_id)->first();
                        $patientDetails[] = [
                            'id' => $accountHolder->id,
                            'yourself' => 1,
                            'booking_date' => Carbon::parse($request->booking_date)->format('d-m-Y'),
                            'slot' => $time_from .' | '. $time_to,
                            'member_name' => $accountHolder->patient_name       ,
                            'dob' => Carbon::parse($accountHolder->patient_dob)->format('d-m-Y'),
                            'gender' => $accountHolder->patient_gender,
                            'mobile_number' => $accountHolder->patient_mobile,
                            'email_address' => $accountHolder->patient_email,
                        ];
                    }else{
                        $members = Trn_Patient_Family_Member::join('mst_patients','trn_patient_family_member.patient_id','mst_patients.id') 
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
                                'slot' => $time_from .' | '. $time_to,
                                'member_name' => $member->family_member_name,
                                'dob' => Carbon::parse($member->date_of_birth)->format('d-m-Y'),
                                'gender' => $member->gender_name,
                                'mobile_number' => $member->mobile_number,
                                'email_address' => $member->email_address,
                            ];
                    }

                    $paymentDetails[] = [
                        'consultation_fee' => $doctor->consultation_fee,
                        'total_amount' => $doctor->consultation_fee,
                    ];
                    
                    $available_slots = PatientHelper::recheckAvailability($request->booking_date, $request->slot_id, $request->doctor_id);
                    
                    if($available_slots >= 1){
                        $data['status'] = 1;
                        $data['message'] = "Data Fetched";
                        $data['doctor_details'] = $doctorDetails;
                        $data['patient_details'] = $patientDetails;
                        $data['payment_details'] = $paymentDetails;
                        $data['booking_id'] = $booking_id ?? '';
                        return response($data);
                    }else{
                        $data['status'] = 0;
                        $data['message'] = "Sorry, no slots available";
                        return response($data);
                    }

                    
                }else{
                    $data['status'] = 0;
                    $data['message'] = "Please fill mandatory fields";
                    return response($data);
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

    public function bookingConfirmation(Request $request){
        $data=array();
        try{
            $validator = Validator::make(
                $request->all(),
                [
                    'doctor_id' => ['required'],
                    'member_id' => ['required'],
                    'slot_id' => ['required'],
                    'booking_date' => ['required'],
                    'yourself' => ['required'],
                    'reschedule_key' => ['required'],
                ],
                [
                    'doctor_id.required' => 'Doctor required',
                    'member_id.required' => 'Member id required',
                    'slot_id.required' => 'Slot required',
                    'booking_date.required' => 'Booking date required',
                    'yourself.required' => 'Yourself is required',
                    'reschedule_key.required' => 'Reschedule key required',
                ]
            );
            if (!$validator->fails()) 
            {
                if (isset($request->yourself) && isset($request->slot_id) && isset($request->doctor_id) && isset($request->booking_date) && isset($request->reschedule_key)) {
                    $patient_id = Auth::id();
                    if ($request->reschedule_key == 1) {
                        if (!$request->has('booking_id')) {
                            $data['status'] = 0;
                            $data['message'] = "Booking id is required";
                            return response($data);
                        } else {
                            $booking_id = $request->booking_id;
                        }
                    }

                    $slotDetails = Mst_TimeSlot::find($request->slot_id);
                    $time_from = date('h:i A', strtotime($slotDetails->time_from));
                    $time_to = date('h:i A', strtotime($slotDetails->time_to));

                    $doctor = Mst_User::join('mst__doctors', 'mst_users.user_id', '=', 'mst__doctors.user_id')
                    ->leftJoin('mst_branches', 'mst__doctors.branch_id', '=', 'mst_branches.branch_id')
                    ->select('mst_users.user_id as doctor_id','mst_users.username as doctor_name', 'mst_branches.branch_id', 'mst__doctors.consultation_fee')
                    ->where('mst_users.user_type_id', 3)
                    ->where('mst_users.user_id', $request->doctor_id)
                    ->first();

                    $yourself = $request->yourself;
                    $booking_date = PatientHelper::dateFormatDb($request->booking_date);
                    $newRecordData = [
                        'booking_type_id' => 1,
                        'patient_id' => $patient_id,
                        'doctor_id' => $request->doctor_id,
                        'branch_id' => $doctor->branch_id, 
                        'booking_date' => $booking_date,
                        'time_slot_id' => $request->slot_id,
                        'booking_status_id' => 2,
                        'booking_fee' => $doctor->consultation_fee,
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

                    $checkAlreadyBooked =  Trn_Consultation_Booking::where('patient_id',Auth::id())->where('booking_date',$newRecordData['booking_date'])->where('time_slot_id',$newRecordData['time_slot_id'])->where('family_member_id',$newRecordData['family_member_id'])->where('doctor_id',$newRecordData['doctor_id'])->first();
                    // print_r($checkAlreadyBooked);die();
                    if($checkAlreadyBooked){
                        $data['status'] = 0;
                        $data['message'] = "Already booked";
                        return response($data);
                    }
 
                    $available_slots = PatientHelper::recheckAvailability($request->booking_date, $request->slot_id, $request->doctor_id);
                    if($available_slots >= 1){
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
                            'booking_to' => $doctor->doctor_name,
                            'booking_date' => $request->booking_date,
                            'time_slot' => $time_from .' - '. $time_to,
                        ];

                        $data['status'] = 1;
                        $data['message'] = "Booking Confirmed";
                        $data['booking_details'] = $booking_details;
                        return response($data);
                    }else{
                        $data['status'] = 0;
                        $data['message'] = "Sorry, no slots available";
                        return response($data);
                    }
                    
                }
                else{
                    $data['status'] = 0;
                    $data['message'] = "Please fill mandatory fields";
                    return response($data);
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
