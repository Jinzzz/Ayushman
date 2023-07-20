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
use App\Models\Trn_Patient_Family_Member;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helpers\PatientHelper;
use Illuminate\Support\Collection;

class DoctorBookingController extends Controller
{
    public function getBranches(){
        $branches = Mst_Branch::where('is_active', 1)->get(['id', 'branch_name'])->toArray();

        $data['status'] = 1;
        $data['message'] = "Data fetched.";
        $data['data'] = $branches;
        return response($data);
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

                    $allotted_doctors = Mst_TimeSlot::where('time_slot_name', $day_of_week)->distinct()->pluck('doctor_id')->toArray();

                    $doctorONLeaveCollection = collect($doctorONLeave);
                    $allottedDoctorsCollection = collect($allotted_doctors);
                    $filteredDoctors = $allottedDoctorsCollection->diff($doctorONLeaveCollection);
                    $filteredDoctorsArray = $filteredDoctors->values()->all();

                    $queries = Mst_User::join('mst_branches', 'mst_users.branch_id', '=', 'mst_branches.id')
                                ->join('mst__doctors', 'mst_users.id', '=', 'mst__doctors.user_id')
                                ->join('mst__designations', 'mst__doctors.designation_id', '=', 'mst__designations.id')
                                ->select('mst_users.id as doctor_id', 'mst_users.username as name', 'mst_branches.branch_name as branch_name', 'mst__designations.designation as designation')
                                ->where('mst_users.user_type_id', 3)
                                ->where('mst_users.branch_id', $request->branch_id)
                                ->whereIn('mst_users.id', $filteredDoctorsArray);

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
                    $doctorDetails = Mst_User::join('mst_branches', 'mst_users.branch_id', '=', 'mst_branches.id')
                    ->join('mst__doctors', 'mst_users.id', '=', 'mst__doctors.user_id')
                    ->join('mst__designations', 'mst__doctors.designation_id', '=', 'mst__designations.id')
                    ->join('trn_user_profiles', 'mst_users.id', '=', 'trn_user_profiles.user_id')
                    ->select('mst_users.id as doctor_id', 'mst_users.username as name', 'mst_branches.branch_name as branch_name', 'mst__doctors.qualification', 'trn_user_profiles.address', 'trn_user_profiles.profile_image', 'mst__designations.designation as designation')
                    ->where('mst_users.user_type_id', 3)
                    ->where('mst_users.id', $request->doctor_id)
                    ->first();

                    if ($doctorDetails) {
                        $doctorDetails->profile_image = 'http://127.0.0.1:8000/public/assets/uploads/doctor_profile/' . $doctorDetails->profile_image;
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
                ],
                [
                    'doctor_id.required' => 'Doctor required',
                    'branch_id.required' => 'Branch required',
                    'booking_date.required' => 'Booking date required',
                ]
            );
            if (!$validator->fails()) 
            {
                if (isset($request->branch_id) && isset($request->doctor_id) && isset($request->booking_date)) {
                    $day_of_week = PatientHelper::getWeekDay($request->booking_date);
            
                    $timeSlots = Mst_TimeSlot::where('doctor_id', $request->doctor_id)
                        ->where('time_slot_name', $day_of_week)
                        ->where('is_active', 1)
                        ->get();
                    
                    $booking_date = PatientHelper::dateFormatDb($request->booking_date);

                    $currentDate = Carbon::now()->format('Y-m-d');
                    $currentTime = Carbon::now()->format('H:i:s');
            
                    $time_slots = [];
                    foreach ($timeSlots as $timeSlot) {
                        $booked_tokens = Trn_Consultation_Booking::where('booking_date', $booking_date)
                            ->where('time_slot_id', $timeSlot->id)
                            ->whereIn('booking_status_id', [1, 2])
                            ->count();
            
                        $available_slots = $timeSlot->no_tockens - $booked_tokens;
            
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
                    $data['data'] = $time_slots;
                    return response($data);
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
                    'patient_id' => ['required'],
                    'branch_id' => ['required'],
                    'slot_id' => ['required'],
                    'booking_date' => ['required'],
                ],
                [
                    'doctor_id.required' => 'Doctor required',
                    'patient_id.required' => 'Patient required',
                    'branch_id.required' => 'Branch required',
                    'slot_id.required' => 'Slot required',
                    'booking_date.required' => 'Booking date required',
                ]
            );
            if (!$validator->fails()) 
            {
                if (isset($request->doctor_id) && isset($request->patient_id) && isset($request->branch_id ) && isset($request->booking_date) && isset($request->slot_id )) {
                    $patient_id = $request->patient_id;
                    $accountHolder = Mst_Patient::where('id',$patient_id)->first();
                    $members = Trn_Patient_Family_Member::join('mst_patients','trn_patient_family_member.patient_id','mst_patients.id') 
                    ->join('sys_gender','trn_patient_family_member.gender_id','sys_gender.id')
                    ->join('sys_relationships','trn_patient_family_member.relationship_id','sys_relationships.id')
                    ->select('trn_patient_family_member.id','trn_patient_family_member.family_member_name','trn_patient_family_member.email_address','trn_patient_family_member.mobile_number','sys_gender.gender_name','trn_patient_family_member.date_of_birth','sys_relationships.relationship')
                    ->where('trn_patient_family_member.patient_id',$patient_id)
                    ->where('trn_patient_family_member.is_active',1)
                    ->get();
    
                    $currentYear = Carbon::now()->year;
                    $carbonDate = Carbon::parse($accountHolder->patient_dob);
                    $year = $carbonDate->year;
    
                    $family_members[] = [
                        'member_id' => $accountHolder->id,
                        'member_name' => $accountHolder->patient_name,
                        'relationship' => "Yourself",
                        'age' => $currentYear - $year,
                        'dob' => Carbon::parse($accountHolder->patient_dob)->format('d-m-Y'),
                        'gender' => $accountHolder->patient_gender,
                        'mobile_number' => $accountHolder->patient_mobile,
                        'email_address' => $accountHolder->patient_email,
                    ];
    
                    foreach ($members as $member){
                        $carbonDate = Carbon::parse($member->date_of_birth);
                        $year = $carbonDate->year;
    
                        $family_members[] = [
                            'member_id' => $member->id,
                            'member_name' => $member->family_member_name,
                            'relationship' => $member->relationship,
                            'age' => $currentYear - $year,
                            'dob' => Carbon::parse($member->date_of_birth)->format('d-m-Y'),
                            'gender' => $member->gender_name,
                            'mobile_number' => $member->mobile_number,
                            'email_address' => $member->email_address,
                        ];
                    }
    
                    $available_slots = PatientHelper::recheckAvailability($request->booking_date, $request->slot_id, $request->doctor_id);
    
                    if($available_slots > 0){
                        $data['status'] = 1;
                        $data['message'] = "Data Fetched";
                        $data['data'] = $family_members;
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
                    'patient_id' => ['required'],
                    'doctor_id' => ['required'],
                    'member_id' => ['required'],
                    'slot_id' => ['required'],
                    'booking_date' => ['required'],
                    'yourself' => ['required'],
                ],
                [
                    'patient_id.required' => 'Patient id required',
                    'doctor_id.required' => 'Doctor required',
                    'member_id.required' => 'Member id required',
                    'slot_id.required' => 'Slot required',
                    'booking_date.required' => 'Booking date required',
                    'yourself.required' => 'Yourself is required',
                ]
            );
            if (!$validator->fails()) 
            {
                if (isset($request->member_id) && isset($request->yourself) && isset($request->slot_id) && isset($request->patient_id) && isset($request->doctor_id) && isset($request->booking_date)) {
                    
                    $slotDetails = Mst_TimeSlot::find($request->slot_id);
                    $time_from = date('h:i A', strtotime($slotDetails->time_from));
                    $time_to = date('h:i A', strtotime($slotDetails->time_to));
                    ;
                    $doctor = Mst_User::join('mst_branches', 'mst_users.branch_id', '=', 'mst_branches.id')
                    ->join('mst__doctors', 'mst_users.id', '=', 'mst__doctors.user_id')
                    ->join('mst__designations', 'mst__doctors.designation_id', '=', 'mst__designations.id')
                    ->join('trn_user_profiles', 'mst_users.id', '=', 'trn_user_profiles.user_id')
                    ->select('mst_users.id as doctor_id', 'mst_users.username as name', 'mst_branches.branch_name as branch_name', 'mst__doctors.qualification', 'mst__doctors.consultation_fee', 'trn_user_profiles.profile_image', 'mst__designations.designation as designation')
                    ->where('mst_users.user_type_id', 3)
                    ->where('mst_users.id', $request->doctor_id)
                    ->first();

                    $doctorDetails[] = [
                        'doctor_id' => $doctor->doctor_id,
                        'doctor_name' => $doctor->name,
                        'doctor_designatiom' => $doctor->designation,
                        'doctor_branch' => $doctor->branch_name,
                        'doctor_profile_image' => 'http://127.0.0.1:8000/public/assets/uploads/doctor_profile/' . $doctor->profile_image,
                    ];

                    $patientDetails = [];

                    if($request->yourself == 1){
                        $accountHolder = Mst_Patient::where('id',$request->patient_id)->first();
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
                            ->where('trn_patient_family_member.patient_id',$request->patient_id)
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
                    'patient_id' => ['required'],
                    'doctor_id' => ['required'],
                    'member_id' => ['required'],
                    'slot_id' => ['required'],
                    'booking_date' => ['required'],
                    'yourself' => ['required'],
                ],
                [
                    'patient_id.required' => 'Patient id required',
                    'doctor_id.required' => 'Doctor required',
                    'member_id.required' => 'Member id required',
                    'slot_id.required' => 'Slot required',
                    'booking_date.required' => 'Booking date required',
                    'yourself.required' => 'Yourself is required',
                ]
            );
            if (!$validator->fails()) 
            {
                if (isset($request->yourself) && isset($request->slot_id) && isset($request->patient_id) && isset($request->doctor_id) && isset($request->booking_date)) {
                    
                    $slotDetails = Mst_TimeSlot::find($request->slot_id);
                    $time_from = date('h:i A', strtotime($slotDetails->time_from));
                    $time_to = date('h:i A', strtotime($slotDetails->time_to));

                    $doctor = Mst_User::join('mst__doctors', 'mst_users.id', '=', 'mst__doctors.user_id')
                    ->select('mst_users.id as doctor_id', 'mst_users.branch_id', 'mst__doctors.consultation_fee')
                    ->where('mst_users.user_type_id', 3)
                    ->where('mst_users.id', $request->doctor_id)
                    ->first();

                    $yourself = $request->yourself;
                    $newRecordData = [
                        'booking_type_id' => 1,
                        'patient_id' => $request->patient_id,
                        'doctor_id' => $request->doctor_id,
                        'branch_id' => $doctor->branch_id, 
                        'booking_date' => $request->booking_date,
                        'time_slot_id' => $request->slot_id,
                        'booking_status_id' => 2,
                        'booking_fee' => $doctor->consultation_fee,
                        'created_at' => Carbon::now(),
                        
                    ];

                    if(!$yourself == 1){
                        if (isset($request->member_id)){
                            $newRecordData = [
                                'is_for_family_member' => 1,
                                'family_member_id' => $request->member_id,
                            ];
                            $booked_for = Trn_Patient_Family_Member::where('id', $request->member_id)->pluck('family_member_name');
                        }else{
                            $data['status'] = 0;
                            $data['message'] = "Member is required";
                            return response($data);
                        }
                        
                    }else{
                        $booked_for = Mst_User::where('id', $request->patient_id)->pluck('username');
                    }
 
                    $available_slots = PatientHelper::recheckAvailability($request->booking_date, $request->slot_id, $request->doctor_id);

                    if($available_slots >= 1){
                        $createdRecord = Trn_Consultation_Booking::create($newRecordData);
                        $lastInsertedId = $createdRecord->id;
                        $leadingZeros = str_pad('', 3 - strlen($lastInsertedId), '0', STR_PAD_LEFT);
                        $bookingRefNo = 'BRN' . $leadingZeros . $lastInsertedId;
                        $updateConsultation = Trn_Consultation_Booking::where('id', $lastInsertedId)->update([
                        'updated_at' => Carbon::now(),
                        'booking_reference_number' => $bookingRefNo
                        ]);
                        $data['status'] = 1;
                        $data['message'] = "Data Fetched";
                        $data['booking_referance_number'] = $bookingRefNo;
                        $data['booking_for'] = $booked_for;
                        $data['booking_date'] = $request->booking_date;
                        $data['time_slot'] = $time_from .' - '. $time_to;
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
