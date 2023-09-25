<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mst_Patient;
use App\Models\Mst_Membership;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_User;
use App\Models\Booking_Availability;
use App\Models\Sys_Booking_Type;
use App\Models\Mst_TimeSlot;
use App\Models\Mst_Membership_Package;
use App\Models\Mst_Patient_Membership_Booking;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function homePage()
    {
        $data = array();
        $upcoming_bookings = array();
        try {
            $patient = Mst_Patient::where('id', Auth::id())->first();
            if (!$patient) {
                $data['status'] = 0;
                $data['message'] = "User does not exist.";
                return response($data);
            }

            $membership_status = 0;
            $membership_name = "";
            $validity = "";

            $membership_details = [];

            if ($patient->available_membership == 1) {
                $membership = Mst_Patient_Membership_Booking::where('patient_id', Auth::id())
                    ->where('membership_expiry_date', '>=', Carbon::now())
                    ->where('is_active', 1)
                    ->first();

                if (!empty($membership)) {
                    $membership_status = 1;

                    $membership_name = Mst_Membership_Package::where('membership_package_id', $membership->membership_package_id)->value('package_title');

                    if (isset($membership->membership_expiry_date)) {
                        $validity = Carbon::parse($membership->membership_expiry_date)->format('d-m-Y');
                    }
                    
                    $membership_details[] = [
                        'membership_name' => $membership_name ?: "",
                        'validity' => $validity,
                    ];
                }
            }

            
            $currentDate = date('Y-m-d');
            $currentTime = date('H:i:s');

            $consultationDetails = Trn_Consultation_Booking::where('patient_id', Auth::id())
            ->whereIn('booking_status_id', [87, 88])
            ->join('mst_staffs', 'trn_consultation_bookings.doctor_id', '=', 'mst_staffs.staff_id')
            ->join('mst_master_values', 'trn_consultation_bookings.booking_type_id', '=', 'mst_master_values.id')
            ->join('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
            ->select('mst_staffs.staff_name', 'trn_consultation_bookings.booking_date', 'mst_master_values.master_value', 'mst_timeslots.time_from')
            ->where(function ($query) use ($currentDate, $currentTime) {
                $query->where('trn_consultation_bookings.booking_date', '>=', $currentDate)
                    ->orWhere(function ($query) use ($currentDate, $currentTime) {
                        $query->where('trn_consultation_bookings.booking_date', '=', $currentDate)
                            ->where('mst_timeslots.time_to', '>', $currentTime);
                    });
            })
            ->orderBy('trn_consultation_bookings.booking_date', 'asc') // Order by booking_date in ascending order
            ->get();


            foreach ($consultationDetails as $consultation) {
                $booking_date = Carbon::parse($consultation->booking_date)->format('d-m-Y');
                $time_from = Carbon::parse($consultation->time_from)->format('h:i A');

                $upcoming_bookings[] = [
                    'doctor_name' => $consultation->staff_name,
                    'booking_date' => $booking_date,
                    'booking_type' => $consultation->master_value,
                    'time_from' => $time_from,
                ];
            }

            $data['status'] = 1;
            $data['message'] = "Data fetched";
            $data['data'] = array(
                'patient_name' => $patient->patient_name,
                'patient_email' => $patient->patient_email,
                'membership_status' => $membership_status,
                'membership_details' => $membership_details,
                'upcoming_bookings' => $upcoming_bookings
            );
            return response()->json($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
}
