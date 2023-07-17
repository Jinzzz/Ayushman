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
                if (isset($request->booking_date) && !empty($request->booking_date)) {

                    $doctorONLeave = Trn_Staff_Leave::where('leave_date', $request->booking_date)->pluck('user_id')->toArray();

                    $queries = Mst_User::join('mst_branches', 'mst_users.branch_id', '=', 'mst_branches.id')
                                ->join('mst__doctors', 'mst_users.id', '=', 'mst__doctors.user_id')
                                ->join('mst__designations', 'mst__doctors.designation_id', '=', 'mst__designations.id')
                                ->select('mst_users.id as doctor_id', 'mst_users.username as name', 'mst_branches.branch_name as branch_name', 'mst__designations.designation as designation')
                                ->where('mst_users.user_type_id', 3)
                                ->where('mst_users.branch_id', $request->branch_id)
                                ->whereNotIn('mst_users.id', $doctorONLeave);

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
                    $booking_date = $request->input('booking_date');
                    $carbon_date = Carbon::parse($booking_date);
                    $day_of_week = $carbon_date->format('l');

                    $doctor = Mst_Doctor::where('user_id',$request->doctor_id)->first();
                    $availableTimeslots = $doctor->available_timeslots; 
                    $timeSlotIds = json_decode($availableTimeslots);

                    $timeSlots = Mst_TimeSlot::whereIn('id', $timeSlotIds)->where('time_slot_name',$day_of_week)->where('is_active', 1)->get();

                    foreach ($timeSlots as $timeSlot) {
                        $booked_tokens = Trn_Consultation_Booking::where('booking_date', $booking_date)
                                        ->where('time_slot_id', $timeSlot->id)
                                        ->whereIn('booking_status_id', [1, 2])
                                        ->count();

                        if ($booked_tokens < $timeSlot->no_tockens) {  

                            $currentDate = Carbon::now()->format('Y-m-d');
                            $currentTime = Carbon::now()->format('H:i:s'); 
                            $available_slots = $timeSlot->no_tockens - $booked_tokens;
                           

                            if ($available_slots <= 0 || ($timeSlot->time_to <= $currentTime && $request->booking_date == $currentDate)) {
                                $time_slots[] = [
                                    'time_slot_id' => $timeSlot->id,
                                    'time_from' => Carbon::parse($timeSlot->time_from)->format('h:i A'),
                                    'time_to' => Carbon::parse($timeSlot->time_to)->format('h:i A'),
                                    'available_slots' => 0,
                                ];
                            }
                            elseif ($request->booking_date == $currentDate && $timeSlot->time_from <= $currentTime && $timeSlot->time_to >= $currentTime){
                                $slots = self::getTimeSlot($timeSlot->avg_time_patient,$timeSlot->time_from,$timeSlot->time_to);
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
                            }
                            else{
                                $available_slots = ($available_slots < 0) ? 0 : $available_slots;
                                $time_slots[] = [
                                'time_slot_id' => $timeSlot->id,
                                'time_from' => Carbon::parse($timeSlot->time_from)->format('h:i A'),
                                'time_to' => Carbon::parse($timeSlot->time_to)->format('h:i A'),
                                'available_slots' => $available_slots,
                                ];
                            }
                        }else{
                            $time_slots[] = [
                                'time_slot_id' => $timeSlot->id,
                                'time_from' => Carbon::parse($timeSlot->time_from)->format('h:i A'),
                                'time_to' => Carbon::parse($timeSlot->time_to)->format('h:i A'),
                                'available_slots' => 0,
                            ];   
                        }
                                        
                    }

                    $data['status'] = 1;
                    $data['message'] = "Data fetched.";
                    $data['data'] = $time_slots;
                    return response($data);

                }else{
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

    

    // to get timeslots
    public static function getTimeSlot($interval, $start_time, $end_time)
    {
        $start = new \DateTime($start_time);
        $end = new \DateTime($end_time);
        $startTime = $start->format('H:i');
        $endTime = $end->format('H:i');
        $i=0;
        $time = [];
        while(strtotime($startTime) <= strtotime($endTime)){
            $start = $startTime;
            $end = date('H:i',strtotime('+'.$interval.' minutes',strtotime($startTime)));
            $startTime = date('H:i',strtotime('+'.$interval.' minutes',strtotime($startTime)));
            $i++;
            if(strtotime($startTime) <= strtotime($endTime)){
                $time[$i]['slot_start_time'] = $start;
                $time[$i]['slot_end_time'] = $end;
            }
        }
        return $time;
    }

}
