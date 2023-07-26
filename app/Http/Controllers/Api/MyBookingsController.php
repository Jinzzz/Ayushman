<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Trn_Consultation_Booking;
use App\Models\Trn_Patient_Family_Member;
use App\Models\Mst_Patient;
use App\Models\Mst_Wellness;
use App\Models\Mst_Therapy;
use Carbon\Carbon;
use App\Helpers\PatientHelper;
use Illuminate\Support\Facades\Validator;

class MyBookingsController extends Controller
{
    public function myBookings(){
        $data=array();
        try{
            $patient_id = Auth::id();
            if($patient_id){
                $currentDate = date('Y-m-d');
                $currentTime = date('H:i:s');

                $all_bookings = [];

                $all_bookings = Trn_Consultation_Booking::where('patient_id', $patient_id)
                    ->whereIn('trn_consultation_bookings.booking_status_id', [1, 2])
                    ->join('mst_users', 'trn_consultation_bookings.doctor_id', '=', 'mst_users.id')
                    ->join('sys_booking_types', 'trn_consultation_bookings.booking_type_id', '=', 'sys_booking_types.booking_type_id')
                    ->join('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
                    ->join('mst_branches', 'trn_consultation_bookings.branch_id', '=', 'mst_branches.id')
                    ->join('sys_booking_status', 'trn_consultation_bookings.booking_status_id', '=', 'sys_booking_status.id')
                    ->where('trn_consultation_bookings.booking_date', '>=', $currentDate)
                    ->where('mst_timeslots.time_from', '>=', $currentTime)
                    ->select(
                        'mst_users.username as doctor_name',
                        'sys_booking_status.status_name',
                        'mst_branches.branch_name',
                        'trn_consultation_bookings.booking_date',
                        'trn_consultation_bookings.id',
                        'trn_consultation_bookings.wellness_id',
                        'trn_consultation_bookings.therapy_id',
                        'trn_consultation_bookings.booking_reference_number',
                        'trn_consultation_bookings.is_for_family_member',
                        'trn_consultation_bookings.booking_type_id',
                        'sys_booking_types.booking_type_name',
                        'mst_timeslots.time_from',
                        'mst_timeslots.time_to'
                    )
                    ->get();
                                
                if ($all_bookings->isNotEmpty()) {
                    foreach ($all_bookings as $booking) {
                        // If booking_type_id = 1, then title is the name of the doctor
                        $title = $booking->doctor_name;
                
                        if ($booking->is_for_family_member == 1) {
                            $patient = Trn_Patient_Family_Member::find($booking->family_member_id);
                            $patient_name = $patient->family_member_name;
                        } else {
                            $patient = Mst_Patient::find($patient_id);
                            $patient_name = $patient->patient_name;
                        }
                
                        if ($booking->booking_type_id == 2) {
                            $wellness = Mst_Wellness::find($booking->wellness_id);
                            $title = $wellness->wellness_name;
                        }
                
                        if ($booking->booking_type_id == 3) {
                            $therapy = Mst_Therapy::find($booking->therapy_id);
                            $title = $therapy->therapy_name;
                        }
                
                        $booking_date = PatientHelper::dateFormatUser($booking->booking_date);
                        $time_from = Carbon::parse($booking->time_from)->format('h:i a');
                        $time_to = Carbon::parse($booking->time_to)->format('h:i a');
                
                        $my_bookings[] = [
                            'booking_id' => $booking->id,
                            'booking_reference_number' => $booking->booking_reference_number,
                            'booking_status' => $booking->status_name,
                            'title' => $title,
                            'booking_date' => $booking_date,
                            'timeslot' => $time_from . '-' . $time_to,
                            'branch_name' => $booking->branch_name,
                            'booking_type' => $booking->booking_type_name,
                            'booked_for' => $patient_name,
                        ];
                    }
                
                    $data['status'] = 1;
                    $data['message'] = "Data fetched";
                    $data['data'] = $my_bookings;
                    return response($data);
                } else {
                    $data['status'] = 0;
                    $data['message'] = "No bookings";
                    return response($data);
                }
                

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

    public function myBookingDetails(Request $request){
        $data=array();
        try{
            $validator = Validator::make(
                $request->all(),
                [
                    'booking_id' => ['required'],
                ],
                [
                    'booking_id.required' => 'Booking refernce Id required',
                ]
            );
            if (!$validator->fails()) 
            {
                if(isset($request->booking_id)){
                    $patient_id = Auth::id();
                    if($patient_id){
                        $booking_details = Trn_Consultation_Booking::where('trn_consultation_bookings.id', $request->booking_id)
                        ->where('patient_id', $patient_id)
                        ->join('mst_users', 'trn_consultation_bookings.doctor_id', '=', 'mst_users.id')
                        ->join('sys_booking_types', 'trn_consultation_bookings.booking_type_id', '=', 'sys_booking_types.booking_type_id')
                        ->join('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
                        ->join('mst_branches', 'trn_consultation_bookings.branch_id', '=', 'mst_branches.id')
                        ->leftJoin('mst__doctors', 'trn_consultation_bookings.doctor_id', '=', 'mst__doctors.user_id')
                        ->leftJoin('mst__designations', 'mst__doctors.designation_id', '=', 'mst__designations.id') 
                        ->join('sys_booking_status', 'trn_consultation_bookings.booking_status_id', '=', 'sys_booking_status.id')
                        ->select(
                            'mst_users.username as doctor_name',
                            'sys_booking_status.status_name',
                            'mst_branches.branch_name',
                            'mst_branches.id as branch_id',
                            'trn_consultation_bookings.booking_date',
                            'trn_consultation_bookings.id',
                            'trn_consultation_bookings.wellness_id',
                            'trn_consultation_bookings.therapy_id',
                            'trn_consultation_bookings.booking_reference_number',
                            'trn_consultation_bookings.is_for_family_member',
                            'trn_consultation_bookings.booking_type_id',
                            'sys_booking_types.booking_type_name',
                            'mst_timeslots.time_from',
                            'mst_timeslots.time_to',
                            'mst__doctors.consultation_fee',
                            'mst__doctors.user_id as doctor_id',
                            'mst__designations.designation' 
                        )->first();

                        $doctor_details = [];
                        $other_booking_details = [];

                        if($booking_details){

                            if ($booking_details->is_for_family_member == 1) {
                                $patient = Trn_Patient_Family_Member::find($booking_details->family_member_id);
                                $patient_name = $patient->family_member_name;
                            } else {
                                $patient = Mst_Patient::find($patient_id);
                                $patient_name = $patient->patient_name;
                            }

                            $booking_date = PatientHelper::dateFormatUser($booking_details->booking_date);
                            $time_from = Carbon::parse($booking_details->time_from)->format('h:i a');
                            $time_to = Carbon::parse($booking_details->time_to)->format('h:i a');

                            $doctor_details[] = [
                                'doctor_id' => $booking_details->doctor_id,
                                'branch_id' => $booking_details->branch_id,
                                'doctor_name' => $booking_details->doctor_name,
                                'designation' => $booking_details->designation,
                                'branch_name' => $booking_details->branch_name,
                            ];
                
                            $other_booking_details[] = [
                                'booking_id' => $booking_details->id,
                                'booking_reference_number' => $booking_details->booking_reference_number,
                                'booking_status' => $booking_details->status_name,
                                'booking_fee' => $booking_details->consultation_fee,
                                'booking_date' => $booking_date,
                                'timeslot' => $time_from . '-' . $time_to,
                                'booked_for' =>$patient_name,
                            ];


                            $data['status'] = 1;
                            $data['message'] = "Data fetched";
                            $data['doctor_details'] = $doctor_details;
                            $data['other_booking_details'] = $other_booking_details;
                            return response($data);
                        }else{
                            $data['status'] = 0;
                            $data['message'] = "No booking details";
                            return response($data);
                        }

                    }
                    else{
                    $data['status'] = 0;
                    $data['message'] = "User does not exist";
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

    public function cancelBooking(Request $request){
        $data=array();
        try{
            $validator = Validator::make(
                $request->all(),
                [
                    'booking_id' => ['required'],
                ],
                [
                    'booking_id.required' => 'Booking refernce Id required',
                ]
            );
            if (!$validator->fails()) 
            {
                if(isset($request->booking_id)){
                    $cancelBooking = Trn_Consultation_Booking::where('id', $request->booking_id)->update([
                        'updated_at' => Carbon::now(),
                        'booking_status_id' => 4
                        ]);
                        $data['status'] = 1;
                        $data['message'] = "Booking cancelled successfuly";
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
}
