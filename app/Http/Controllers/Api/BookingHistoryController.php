<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Trn_Consultation_Booking;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Mst_Patient;
use App\Models\Mst_Wellness;
use App\Models\Mst_Therapy;
use App\Helpers\PatientHelper;
use App\Models\Trn_Patient_Family_Member;

class BookingHistoryController extends Controller
{
    public function myBookingHistory()
    {
        $data = array();
        try {
            // Get the authenticated patient's ID
            $patient_id = Auth::id();
            // Check if the patient exists
            if ($patient_id) {
                // Get the current date and time
                $currentDate = date('Y-m-d');
                $currentTime = date('H:i:s');
                // Cancel pending wellness and therapy bookings
                $pending_to_cancel = Trn_Consultation_Booking::whereIn('booking_type_id', ['85', '86'])
                    ->whereIn('booking_status_id', ['87', '88'])
                    ->where('trn_consultation_bookings.booking_date', '<', $currentDate)
                    ->update([
                        'updated_at' => Carbon::now(),
                        'booking_status_id' => 90
                    ]);
                // Cancel pending consultation bookings

                $pending_to_cancel = Trn_Consultation_Booking::where('booking_type_id', 84)
                    ->whereIn('booking_status_id', ['87', '88'])
                    ->join('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
                    ->where(function ($query) use ($currentDate) {
                        $query->where('trn_consultation_bookings.booking_date', '<', $currentDate)
                            ->orWhere(function ($query) use ($currentDate) {
                                $query->where('trn_consultation_bookings.booking_date', '=', $currentDate)
                                    ->where('mst_timeslots.time_from', '<', Carbon::now()->format('H:i:s'));
                            });
                    })
                    ->update([
                        'updated_at' => Carbon::now(),
                        'booking_status_id' => 90
                    ]);
                // Initialize array to store booking details
                $my_bookings = [];

                // Get pending consultation bookings
                $all_bookings_consultation = Trn_Consultation_Booking::where('patient_id', $patient_id)
                    ->where('booking_type_id', 84)
                    ->join('mst_staffs', 'trn_consultation_bookings.doctor_id', '=', 'mst_staffs.staff_id')
                    ->join('mst_master_values as booking_type', 'trn_consultation_bookings.booking_type_id', '=', 'booking_type.id')
                    ->join('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
                    ->join('mst_branches', 'trn_consultation_bookings.branch_id', '=', 'mst_branches.branch_id')
                    ->join('mst_master_values as booking_status', 'trn_consultation_bookings.booking_status_id', '=', 'booking_status.id')
                    ->where(function ($query) use ($currentDate, $currentTime) {
                        $query->where('trn_consultation_bookings.booking_date', '<', $currentDate)
                            ->orWhere(function ($query) use ($currentDate, $currentTime) {
                                $query->where('trn_consultation_bookings.booking_date', '=', $currentDate)
                                    ->where('mst_timeslots.time_from', '<', $currentTime);
                            });
                    })
                    ->select(
                        'mst_staffs.staff_name as doctor_name',
                        'booking_status.master_value as booking_status_name',
                        'mst_branches.branch_name',
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
                        'mst_timeslots.time_to'
                    )
                    ->get();

                // Process and add consultation bookings to the result array
                if ($all_bookings_consultation->isNotEmpty()) {
                    foreach ($all_bookings_consultation as $booking) {
                        // If booking_type_id = 84, then title is the name of the doctor
                        $title = $booking->doctor_name;

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

                        if ($booking->booking_type_id == 85) {
                            $wellness = Mst_Wellness::find($booking->wellness_id);
                            $title = $wellness->wellness_name;
                        }

                        if ($booking->booking_type_id == 86) {
                            $therapy = Mst_Therapy::find($booking->therapy_id);
                            $title = $therapy->therapy_name;
                        }

                        $booking_date = PatientHelper::dateFormatUser($booking->booking_date);
                        $time_from = Carbon::parse($booking->time_from)->format('h:i a');
                        $time_to = Carbon::parse($booking->time_to)->format('h:i a');

                        $my_bookings[] = [
                            'booking_id' => $booking->id,
                            'booking_reference_number' => $booking->booking_reference_number,
                            'booking_status' => $booking->booking_status_name,
                            'title' => $title,
                            'booking_date' => $booking_date,
                            'timeslot' => $time_from . '-' . $time_to,
                            'branch_name' => $booking->branch_name,
                            'booking_type' => $bookingType,
                            'booked_for' => $patient_name,
                        ];
                    }
                }

                // Get pending wellness bookings
                $all_bookings_wellness = Trn_Consultation_Booking::where('patient_id', $patient_id)
                    ->where('booking_type_id', 85)
                    ->join('mst_master_values as booking_type', 'trn_consultation_bookings.booking_type_id', '=', 'booking_type.id')
                    ->join('mst_branches', 'trn_consultation_bookings.branch_id', '=', 'mst_branches.branch_id')
                    ->join('mst_master_values as booking_status', 'trn_consultation_bookings.booking_status_id', '=', 'booking_status.id')
                    ->where('trn_consultation_bookings.booking_date', '<', $currentDate)
                    ->select(
                        'booking_status.master_value as booking_status_name',
                        'mst_branches.branch_name',
                        'trn_consultation_bookings.booking_date',
                        'trn_consultation_bookings.id',
                        'trn_consultation_bookings.wellness_id',
                        'trn_consultation_bookings.therapy_id',
                        'trn_consultation_bookings.booking_reference_number',
                        'trn_consultation_bookings.is_for_family_member',
                        'trn_consultation_bookings.booking_type_id',
                        'trn_consultation_bookings.family_member_id',
                        'booking_type.master_value as booking_type_name'
                    )
                    ->get();


                // Process and add wellness bookings to the result array
                if ($all_bookings_wellness->isNotEmpty()) {
                    foreach ($all_bookings_wellness as $booking) {
                        // If booking_type_id = 85, then title is the booking_type_name
                        $title = $booking->booking_type_name;

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

                        if ($booking->booking_type_id == 85) {
                            $wellness = Mst_Wellness::find($booking->wellness_id);
                            $title = $wellness->wellness_name;
                        }

                        if ($booking->booking_type_id == 86) {
                            $therapy = Mst_Therapy::find($booking->therapy_id);
                            $title = $therapy->therapy_name;
                        }

                        $booking_date = PatientHelper::dateFormatUser($booking->booking_date);
                        // $time_from = Carbon::parse($booking->time_from)->format('h:i a');
                        // $time_to = Carbon::parse($booking->time_to)->format('h:i a');

                        $my_bookings[] = [
                            'booking_id' => $booking->id,
                            'booking_reference_number' => $booking->booking_reference_number,
                            'booking_status' => $booking->booking_status_name,
                            'title' => $title,
                            'booking_date' => $booking_date,
                            'timeslot' => "",
                            'branch_name' => $booking->branch_name,
                            'booking_type' => $bookingType,
                            'booked_for' => $patient_name,
                        ];
                    }
                }
                // Prepare the success response
                $data['status'] = 1;
                $data['message'] = "Data fetched";
                $data['data'] = $my_bookings;
                return response($data);
            } else {
                // Patient does not exist
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

    public function bookingHistoryDetails(Request $request)
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
                    // Get the authenticated patient's ID
                    $patient_id = Auth::id();
                    // Check if the patient exists
                    if ($patient_id) {
                        // Fetch booking details for the specified booking_id and patient_id
                        $booking_details = Trn_Consultation_Booking::where('trn_consultation_bookings.id', $request->booking_id)
                            ->where('patient_id', $patient_id)
                            ->join('mst_staffs', 'trn_consultation_bookings.doctor_id', '=', 'mst_staffs.staff_id')
                            ->join('mst_master_values as booking_type', 'trn_consultation_bookings.booking_type_id', '=', 'booking_type.id')
                            ->join('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
                            ->join('mst_branches', 'trn_consultation_bookings.branch_id', '=', 'mst_branches.branch_id')
                            ->leftJoin('mst_master_values as qualification', 'mst_staffs.staff_qualification', '=', 'qualification.id')
                            ->join('mst_master_values as booking_status', 'trn_consultation_bookings.booking_status_id', '=', 'booking_status.id')
                            ->select(
                                'mst_staffs.staff_name as doctor_name',
                                'booking_status.master_value as status_name',
                                'mst_branches.branch_name',
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
                                'mst_staffs.staff_id as doctor_id',
                                'qualification.master_value as qualification'
                            )->first();

                        // Initialize arrays to store doctor and other booking details
                        $doctor_details = [];
                        $other_booking_details = [];

                        // Check if booking details are fetched
                        if ($booking_details) {
                            // Determine the patient's name based on whether it's for a family member or not
                            if ($booking_details->is_for_family_member == 1) {
                                $patient = Trn_Patient_Family_Member::find($booking_details->family_member_id);
                                $patient_name = $patient->family_member_name;
                            } else {
                                $patient = Mst_Patient::find($patient_id);
                                $patient_name = $patient->patient_name;
                            }
                            // Format date and time
                            $booking_date = PatientHelper::dateFormatUser($booking_details->booking_date);
                            $time_from = Carbon::parse($booking_details->time_from)->format('h:i a');
                            $time_to = Carbon::parse($booking_details->time_to)->format('h:i a');
                            // Populate doctor details array
                            $doctor_details[] = [
                                'doctor_id' => $booking_details->doctor_id,
                                'branch_id' => $booking_details->branch_id,
                                'doctor_name' => $booking_details->doctor_name,
                                'qualification' => $booking_details->qualification,
                                'branch_name' => $booking_details->branch_name,
                            ];
                            // Populate other booking details array
                            $other_booking_details[] = [
                                'booking_id' => $booking_details->id,
                                'booking_reference_number' => $booking_details->booking_reference_number,
                                'booking_status' => $booking_details->status_name,
                                'booking_fee' => "500",
                                'booking_date' => $booking_date,
                                'timeslot' => $time_from . '-' . $time_to,
                                'booked_for' => $patient_name,
                            ];

                            // Prepare success response
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
                        // User does not exist
                        $data['status'] = 0;
                        $data['message'] = "User does not exist";
                        return response($data);
                    }
                } else {
                    // Booking ID is not provided in the request
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
