<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Branch;
use App\Models\Mst_Patient;
use App\Models\Mst_Staff;
use App\Models\Staff_Leave;
use App\Models\Mst_Staff_Timeslot;
use App\Models\Mst_TimeSlot;
use App\Models\Mst_Patient_Membership_Booking;
use App\Models\Mst_Master_Value;
use App\Models\Trn_Patient_Family_Member;

class BookingController extends Controller
{
    
    public function ConsultationIndex(Request $request)
    {
        return view('booking.consultation.index', [
            'bookings' => Trn_Consultation_Booking::where('booking_type_id',84)->orderBy('created_at','DESC')->get(),
            'pageTitle' => 'Consultation Bookings'
        ]);
    }

    public function ConsultationCreate(Request $request)
    {
        return view('booking.consultation.create', [
            'branches' => Mst_Branch::where('is_active','=',1)->orderBy('branch_name','ASC')->get(),
            'patients' => Mst_Patient::orderBy('patient_name','ASC')->get(),
            'paymentType' => Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id'),
            'pageTitle' => 'Add Consultation Booking'
        ]);
    }

    
    public function getDoctors(Request $request)
    {
        $branchId = $request->input('branch_id');
        $staffs = Mst_Staff::where('branch_id', $branchId)
            ->where('staff_type', 20) // doctors
            ->select('staff_id', 'staff_name',)
            ->get();
        return response()->json($staffs);
    }

    public function getBookingFee(Request $request)
    {
        $staffId = $request->input('staff_id');
        $bookingDate = $request->input('booking_date');
        //check if the staff is leave 
        $staffLeave = Staff_Leave::where('staff_id', $staffId)
        ->whereDate('from_date', '<=', $bookingDate)
        ->whereDate('to_date', '>=', $bookingDate)
        ->exists();
        if ($staffLeave) {
            return response()->json(['error' => 'Doctor is not available on the selected date']);
        }

        $staff = Mst_Staff::find($staffId);
        $bookingFee = $staff->staff_booking_fee;

        $weekday = Carbon::parse($bookingDate)->format('l');
        $timeslots = Mst_Staff_Timeslot::where('staff_id', $staffId)
        ->whereHas('weekDay', function ($query) use ($weekday) {
             $query->where('master_id', 3)
                  ->where('master_value', $weekday);
        })
        ->pluck('timeslot');

        $timeslotInfo = Mst_TimeSlot::whereIn('id', $timeslots)
        ->select('slot_name', 'time_from', 'time_to')
        ->get();

        return response()->json([
            'booking_fee' => $bookingFee,
            'timeslots' => $timeslotInfo
        ]);
    }

    
    public function getMembershipDetails(Request $request)
    {
        $patientId = $request->input('patient_id');
        $membership = Mst_Patient_Membership_Booking::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->with('membershipPackage')
            ->first();

        return response()->json([
            'membership' => [
                'package_title' => $membership->membershipPackage->package_title ?? null,
                'start_date' => $membership->start_date ?? null,
                'expiry_date' => $membership->membership_expiry_date ?? null,
            ]
        ]);
    }

    public function getMembershipAndBookingFee(Request $request)
    {
        $patientId = $request->input('patient_id');
        $staffId = $request->input('staff_id');
        
        $membership = $this->getpatientInfo($patientId);
        $payableAmount = $membership ? 0.00 : Mst_Staff::find($staffId)->staff_booking_fee ?? 0.00;

        $familyMembers = Trn_Patient_Family_Member::where('patient_id', $patientId)
                        ->orderBy('created_at', 'DESC')
                        ->get();

        return response()->json(['payable_amount' => $payableAmount, 'family_members' => $familyMembers]);
    }

    private function getpatientInfo($patientId)
    {
        return Mst_Patient_Membership_Booking::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->with('membershipPackage')
            ->first();
    }


}
