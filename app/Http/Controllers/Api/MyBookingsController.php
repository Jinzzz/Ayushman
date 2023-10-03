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
use App\Models\Mst_Staff;
use App\Models\Trn_Patient_Device_Tocken;
use Carbon\Carbon;
use App\Helpers\PatientHelper;
use App\Helpers\DeviceTockenHelper;
use Illuminate\Support\Facades\Validator;
use DB;

class MyBookingsController extends Controller
{
    public function myBookings()
    {
        $data = array();
        try {
            $patient_id = Auth::id();
            // return $patient_id;
            if ($patient_id) {
                $currentDate = date('Y-m-d');

                $all_bookings = [];
                // Retrieve all consultation bookings for the given patient
                $all_bookings = Trn_Consultation_Booking::where('patient_id', $patient_id)
                    ->whereIn('trn_consultation_bookings.booking_status_id', [87, 88])
                    ->join('mst_staffs', 'trn_consultation_bookings.doctor_id', '=', 'mst_staffs.staff_id')
                    ->join('mst_master_values as booking_type_master', 'trn_consultation_bookings.booking_type_id', '=', 'booking_type_master.id')
                    ->join('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
                    ->join('mst_branches', 'trn_consultation_bookings.branch_id', '=', 'mst_branches.branch_id')
                    ->join('mst_master_values as booking_status_master', 'trn_consultation_bookings.booking_status_id', '=', 'booking_status_master.id')
                    ->where('trn_consultation_bookings.booking_date', '>=', $currentDate)
                    ->select(
                        'mst_staffs.staff_name as doctor_name',
                        'booking_status_master.master_value as booking_status',
                        'mst_branches.branch_name',
                        'trn_consultation_bookings.booking_date',
                        'trn_consultation_bookings.id',
                        'trn_consultation_bookings.wellness_id',
                        'trn_consultation_bookings.therapy_id',
                        'trn_consultation_bookings.booking_reference_number',
                        'trn_consultation_bookings.is_for_family_member',
                        'trn_consultation_bookings.booking_type_id',
                        'trn_consultation_bookings.family_member_id',
                        'booking_type_master.master_value as booking_type',
                        'mst_timeslots.time_from',
                        'mst_timeslots.time_to'
                    )
                    ->get();

                if ($all_bookings->isNotEmpty()) {
                    // Loop through all consultation bookings and prepare data for display
                    foreach ($all_bookings as $booking) {
                        // If booking_type_id = 84, then title is the name of the doctor
                        $title = $booking->doctor_name;

                        // Check if the booking is for a family member
                        if ($booking->is_for_family_member == 1) {
                            $patient = Trn_Patient_Family_Member::find($booking->family_member_id);
                            $patient_name = $patient->family_member_name;
                        } else {
                            $patient = Mst_Patient::find($patient_id);
                            $patient_name = $patient->patient_name;
                        }

                        if ($booking->booking_type_id == 84) {
                           $bookingType = 0;
                        }

                        if ($booking->booking_type_id == 85) {
                            $bookingType = 1;
                        }

                        // Check booking type and set title accordingly
                        if ($booking->booking_type_id == 85) {
                            $wellness = Mst_Wellness::find($booking->wellness_id);
                            $title = $wellness->wellness_name;
                        }

                        if ($booking->booking_type_id == 86) {
                            $therapy = Mst_Therapy::find($booking->therapy_id);
                            $title = $therapy->therapy_name;
                        }

                        // Format date and time for display
                        $booking_date = PatientHelper::dateFormatUser($booking->booking_date);
                        $time_from = Carbon::parse($booking->time_from)->format('h:i a');
                        $time_to = Carbon::parse($booking->time_to)->format('h:i a');

                        // Create an array with booking details
                        $my_bookings[] = [
                            'booking_id' => $booking->id,
                            'booking_reference_number' => $booking->booking_reference_number,
                            'booking_status' => $booking->booking_status,
                            'title' => $title,
                            'booking_date' => $booking_date,
                            'timeslot' => $time_from . '-' . $time_to,
                            'branch_name' => $booking->branch_name,
                            'booking_type' => $bookingType,
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
            } else {
                $data['status'] = 0;
                $data['message'] = "User does not exist";
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

    public function myBookingDetails(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'booking_id' => ['required'],
                ],
                [
                    'booking_id.required' => 'Booking refernce Id required',
                ]
            );
            if (!$validator->fails()) {
                // Check if booking_id is set in the request
                if (isset($request->booking_id)) {
                    // Get the patient ID from the authenticated user
                    $patient_id = Auth::id();
                    // Check if the patient ID exists
                    if ($patient_id) {
                        // Retrieve booking details for the specified booking_id and patient_id
                        $booking_details = Trn_Consultation_Booking::where('trn_consultation_bookings.id', $request->booking_id)
                            ->where('patient_id', $patient_id)
                            ->join('mst_staffs', 'trn_consultation_bookings.doctor_id', '=', 'mst_staffs.staff_id')
                            ->join('mst_master_values as booking_type', 'trn_consultation_bookings.booking_type_id', '=', 'booking_type.id')
                            ->join('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
                            ->join('mst_branches', 'trn_consultation_bookings.branch_id', '=', 'mst_branches.branch_id')
                            ->join('mst_master_values as qualification', 'mst_staffs.staff_qualification', '=', 'qualification.id')
                            ->join('mst_master_values as booking_status', 'trn_consultation_bookings.booking_status_id', '=', 'booking_status.id')
                            ->select(
                                'mst_staffs.staff_name as doctor_name',
                                'booking_status.master_value as status_name',
                                'mst_branches.branch_name',
                                'mst_branches.latitude',
                                'mst_branches.longitude',
                                'mst_branches.branch_id as branch_id',
                                'trn_consultation_bookings.booking_date',
                                'trn_consultation_bookings.id',
                                'trn_consultation_bookings.wellness_id',
                                'trn_consultation_bookings.therapy_id',
                                'trn_consultation_bookings.booking_reference_number',
                                'trn_consultation_bookings.is_for_family_member',
                                'trn_consultation_bookings.booking_type_id',
                                'trn_consultation_bookings.family_member_id',
                                'booking_type.master_value as booking_type_name',
                                'mst_timeslots.time_from',
                                'mst_timeslots.time_to',
                                'mst_staffs.staff_booking_fee',
                                'mst_staffs.staff_id as doctor_id',
                                'qualification.master_value as staff_qualification'
                            )
                            ->first();


                        $doctor_details = [];
                        $other_booking_details = [];
                        // Check if booking details exist
                        if ($booking_details) {
                            // Determine the patient's name based on whether the booking is for a family member
                            if ($booking_details->is_for_family_member == 1) {
                                $patient = Trn_Patient_Family_Member::find($booking_details->family_member_id);
                                $patient_name = $patient->family_member_name;
                            } else {
                                $patient = Mst_Patient::find($patient_id);
                                $patient_name = $patient->patient_name;
                            }
                            // Format date and time for display
                            $booking_date = PatientHelper::dateFormatUser($booking_details->booking_date);
                            $time_from = Carbon::parse($booking_details->time_from)->format('h:i a');
                            $time_to = Carbon::parse($booking_details->time_to)->format('h:i a');
                            // Populate doctor_details array with relevant information
                            $doctor_details[] = [
                                'doctor_id' => $booking_details->doctor_id,
                                'branch_id' => $booking_details->branch_id,
                                'doctor_name' => $booking_details->doctor_name,
                                'qualification' => $booking_details->staff_qualification,
                                'branch_name' => $booking_details->branch_name,
                                'latitude' => $booking_details->latitude ?? 0,
                                'longitude' => $booking_details->longitude ?? 0,
                            ];

                            // Populate other_booking_details array with relevant information
                            $other_booking_details[] = [
                                'booking_id' => $booking_details->id,
                                'booking_reference_number' => $booking_details->booking_reference_number,
                                'booking_status' => $booking_details->status_name,
                                'booking_fee' => $booking_details->staff_booking_fee,
                                'booking_date' => $booking_date,
                                'timeslot' => $time_from . '-' . $time_to,
                                'booked_for' => $patient_name,
                            ];

                            // Prepare the response data
                            $data['status'] = 1;
                            $data['message'] = "Data fetched";
                            $data['doctor_details'] = $doctor_details;
                            $data['other_booking_details'] = $other_booking_details;
                            return response($data);
                        } else {
                            // No booking details found
                            $data['status'] = 0;
                            $data['message'] = "No booking details";
                            return response($data);
                        }
                    } else {
                        $data['status'] = 0;
                        $data['message'] = "User does not exist";
                        return response($data);
                    }
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

    public function cancelBooking(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'booking_id' => ['required'],
                ],
                [
                    'booking_id.required' => 'Booking refernce Id required',
                ]
            );
            if (!$validator->fails()) {
                // Check if booking_id is set in the request
                if (isset($request->booking_id)) {
                    // Update the booking status to indicate cancellation
                    $cancelBooking = Trn_Consultation_Booking::where('id', $request->booking_id)->update([
                        'updated_at' => Carbon::now(),
                        'booking_status_id' => 90
                    ]);

                    // Retrieve the updated booking details
                    $updatedBooking = Trn_Consultation_Booking::find($request->booking_id);

                    // Get the name of the doctor associated with the booking
                    $doctor_name = Mst_Staff::where('staff_id', $updatedBooking->doctor_id)->value('staff_name');

                    // Retrieve the device tokens associated with the patient
                    $patientDevice = Trn_Patient_Device_Tocken::where('patient_id', Auth::id())->get();
                    // Iterate through each device token to send cancellation notification
                    foreach ($patientDevice as $pdt) {
                        // Notification details
                        $title = 'Booking Cancelled';
                        $body = 'Your booking for ' . $doctor_name . ' on ' . $updatedBooking->booking_date . ' has been cancelled! . Please check and confirm';
                        $clickAction = "PatientBookingCancelling";
                        $type = "cancel";
                        // Send notification to the patient's device
                        $data['response'] =  DeviceTockenHelper::patientNotification($pdt->patient_device_token, $title, $body, $clickAction, $type);
                    }
                    // Prepare the success response
                    $data['status'] = 1;
                    $data['message'] = "Booking cancelled successfully";
                    return response($data);
                } else {
                    // Incomplete request, mandatory fields are missing
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
}
