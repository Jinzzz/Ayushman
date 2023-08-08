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
    public function homePage(){
        $data=array();
        $upcoming_bookings = array();
        try
        {
            $patient=Mst_Patient::where('id',Auth::id())->first();
            if(!$patient)
            {
                $data['status'] = 0;
                $data['message'] = "User does not exist.";
                return response($data);
            }
            
            $membership_status = "";
            $membership_name = "";
            $validity = "";
            
            if ($patient->available_membership == 1) {
                $membership = Mst_Patient_Membership_Booking::where('patient_id', Auth::id())
                    ->latest()
                    ->first();
            
                if ($membership) {
                    $membership_status = 1;
            
                    $membership_name = Mst_Membership_Package::where('membership_package_id', $membership->membership_package_id)
                        ->where('is_active', 1)
                        ->value('package_title');
                    $membership_name = $membership_name ?: "";
            
                    if (isset($membership->membership_expiry_date)) {
                        $validity = Carbon::parse($membership->membership_expiry_date)->format('d-m-Y');
                    }
                }
            }
            

            $currentDate = date('Y-m-d');
            $currentTime = date('H:i:s');

            $consultationDetails = Trn_Consultation_Booking::where('patient_id', Auth::id())
            ->whereIn('booking_status_id', [1, 2])
            ->join('mst_staffs', 'trn_consultation_bookings.doctor_id', '=', 'mst_staffs.staff_id')
            ->join('sys_booking_types', 'trn_consultation_bookings.booking_type_id', '=', 'sys_booking_types.booking_type_id')
            ->join('mst_timeslots', 'trn_consultation_bookings.time_slot_id', '=', 'mst_timeslots.id')
            ->select('mst_staffs.staff_name', 'trn_consultation_bookings.booking_date', 'sys_booking_types.booking_type_name', 'mst_timeslots.time_from')
            ->where(function ($query) use ($currentDate, $currentTime) {
                $query->where('trn_consultation_bookings.booking_date', '>=', $currentDate)
                    ->orWhere(function ($query) use ($currentDate, $currentTime) {
                        $query->where('trn_consultation_bookings.booking_date', '=', $currentDate)
                            ->where('mst_timeslots.time_to', '>', $currentTime);
                    });
            })
            ->get();
            
            
            foreach ($consultationDetails as $consultation) {
                $booking_date = Carbon::parse($consultation->booking_date)->format('d-m-Y');
                $time_from = Carbon::parse($consultation->time_from)->format('h:i A');

                $upcoming_bookings[] = [
                'doctor_name' => $consultation->staff_name,
                'booking_date' => $booking_date,
                'booking_type' => $consultation->booking_type_name,
                'time_from' => $time_from,
                ];
            }

            $data['status'] = 1;
            $data['message'] = "Data fetched";
            $data['data'] = array(
                'patient_name' => $patient->patient_name,
                'membership_status' => $membership_status,
                'membership_name' => $membership_name,
                'validity' => $validity,
                'upcoming_bookings' => $upcoming_bookings
            );
            return response()->json($data);
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
