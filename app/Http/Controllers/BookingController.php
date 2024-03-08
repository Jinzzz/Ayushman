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
use App\Models\Mst_Wellness;
use App\Models\Mst_Wellness_Therapyrooms;
use App\Models\Mst_Therapy_Therapyrooms;
use App\Models\Mst_Therapy_Room_Slot;
use App\Models\Mst_Therapy;
use App\Models\Trn_Booking_Therapy_detail;

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
        $currentDate = Carbon::now();
        $membership = Mst_Patient_Membership_Booking::where('patient_id', $patientId)
            ->where('membership_expiry_date', '>', $currentDate)
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
        $currentDate = Carbon::now();
        return Mst_Patient_Membership_Booking::where('patient_id', $patientId)
            ->where('membership_expiry_date', '>', $currentDate)
            ->orderBy('created_at', 'desc')
            ->with('membershipPackage')
            ->first();
    }

    
    public function WellnessIndex(Request $request)
    {
        return view('booking.wellness.index', [
            'bookings' => Trn_Consultation_Booking::where('booking_type_id',85)->orderBy('created_at','DESC')->get(),
            'pageTitle' => 'Wellness Bookings'
        ]);
    }

    
    public function WellnessCreate(Request $request)
    {
        return view('booking.wellness.create', [
            'branches' => Mst_Branch::where('is_active','=',1)->orderBy('branch_name','ASC')->get(),
            'patients' => Mst_Patient::orderBy('patient_name','ASC')->get(),
            'paymentType' => Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id'),
            'pageTitle' => 'Add Wellness Booking'
        ]);
    }

    public function getWellnessList(Request $request)
    {
        $branchId = $request->input('branch_id');
        $wellness = Mst_Wellness::where('is_active', 1)
            ->select('wellness_id', 'wellness_name')
            ->get();
        return response()->json($wellness);
    }

    public function wellnessFee(Request $request)
    {
        $wellnessID = $request->input('wellness_id');
        $bookingDate = $request->input('booking_date');      

        $Wellness = Mst_Wellness::find($wellnessID);
        $wellnessCost = $Wellness->wellness_cost;
        $wellnessOffer = $Wellness->offer_price;
        $bookingFee = ($wellnessOffer !== null && $wellnessOffer < $wellnessCost) ? $wellnessOffer : $wellnessCost;

        $therapyRooms = Mst_Wellness_Therapyrooms::where('wellness_id', $wellnessID)
                    ->pluck('therapy_room_id');
        $timeslots = Mst_Therapy_Room_Slot::whereIn('therapy_room_id', $therapyRooms)
                    ->with(['slot'])
                    ->get();
        $timeslotInfo = [];
        foreach ($timeslots as $slots) {
            $timeslotInfo[] = [
                'therapy_room_name' => $slots->therapyRoom->room_name,
                'time_from' => $slots->slot->time_from,
                'time_to' => $slots->slot->time_to,
            ];
        }
        //wellness Info
        $wellnessName = $Wellness->wellness_name;
        $wellnessDuration = $Wellness->wellness_duration;
        $wellnessDescription = $Wellness->wellness_description;
        return response()->json([
            'booking_fee' => $bookingFee,
            'timeslots' => $timeslotInfo,
            'wellness_name' => $wellnessName,
            'wellness_duration' => $wellnessDuration,
            'wellness_cost' => $wellnessCost,
            'offer_price' => $wellnessOffer,
            'wellness_description' => $wellnessDescription
        ]);
    }

    
    public function wellnessMembershipandFee(Request $request)
    {
        $patientId = $request->input('patient_id');
        $wellnessID = $request->input('wellness_id');
        
        $membership = $this->getpatientInfo($patientId);
        if ($membership) {
            $payableAmount = 0.00;
        } else {
            $Wellness = Mst_Wellness::find($wellnessID);
            $wellnessCost = $Wellness->wellness_cost;
            $wellnessOffer = $Wellness->offer_price;
            $bookingFee = ($wellnessOffer !== null && $wellnessOffer < $wellnessCost) ? $wellnessOffer : $wellnessCost;
        
            $payableAmount = $wellnessOffer ?? $wellnessCost;
        }

        $familyMembers = Trn_Patient_Family_Member::where('patient_id', $patientId)
                        ->orderBy('created_at', 'DESC')
                        ->get();

        return response()->json(['payable_amount' => $payableAmount, 'family_members' => $familyMembers]);
    }

    
    public function TherapyBooking(Request $request)
    {
        return view('booking.therapy.index', [
            'bookings' => Trn_Consultation_Booking::where('booking_type_id',86)->orderBy('created_at','DESC')->get(),
            'pageTitle' => 'Therapy Bookings'
        ]);
    }

    
    public function TherapyCreate(Request $request)
    {
        return view('booking.therapy.create', [
            'branches' => Mst_Branch::where('is_active','=',1)->orderBy('branch_name','ASC')->get(),
            'patients' => Mst_Patient::orderBy('patient_name','ASC')->get(),
            'paymentType' => Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id'),
            'pageTitle' => 'Add Therapy Booking'
        ]);
    }

    public function getTherapyList(Request $request)
    {
        $branchId = $request->input('branch_id');
        $therapy = Mst_Therapy::where('is_active', 1)
            ->select('id', 'therapy_name')
            ->get();
        return response()->json($therapy);
    }

    
    public function getTherapyBookingFee(Request $request)
    {
        $therapyID = $request->input('therapy_id');
        $bookingDate = $request->input('booking_date');      

        $Therapy = Mst_Therapy::find($therapyID);
        $therapyCost = $Therapy->therapy_cost;
        $bookingFee = $therapyCost;

        $therapyRooms = Mst_Therapy_Therapyrooms::where('therapy_id', $therapyID)
                    ->pluck('therapy_room_id');
        $timeslots = Mst_Therapy_Room_Slot::whereIn('therapy_room_id', $therapyRooms)
                    ->with(['slot'])
                    ->get();
        $timeslotInfo = [];
        foreach ($timeslots as $slots) {
            $timeslotInfo[] = [
                'therapy_room_name' => $slots->therapyRoom->room_name,
                'time_from' => $slots->slot->time_from,
                'time_to' => $slots->slot->time_to,
            ];
        }
        //wellness Info
        $therapyName = $Therapy->therapy_name;

        return response()->json([
            'booking_fee' => $bookingFee,
            'timeslots' => $timeslotInfo,
            'therapy_name' => $therapyName,
            'therapy_cost' => $therapyCost,
        ]);
    }

    public function TherapyRefundindex(Request $request)
    {
        return view('booking.therapy-refund.index', [
            'bookings' => Trn_Consultation_Booking::where('booking_type_id',86)->orderBy('created_at','DESC')->get(),
            'pageTitle' => 'Therapy Refunds'
        ]);
    }

    
    public function TherapyRefundCreate(Request $request)
    {
        return view('booking.therapy-refund.create', [
            'patients' => Mst_Patient::orderBy('patient_name','ASC')->get(),
            'paymentType' => Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id'),
            'pageTitle' => 'Add Therapy Refund'
        ]);
    }

    public function fetchRefundBookings(Request $request)
    {
        $patientId = $request->input('patient_id');

        $bookings = Trn_Consultation_Booking::where('patient_id', $patientId)
            ->where('booking_type_id', 86)
            ->where('is_paid', 1)
            ->where('booking_status_id', 88) //confirmed
            ->pluck('booking_reference_number', 'id');

        return response()->json($bookings);
    }

    public function fetchtherapyInfo(Request $request)
    {
        $bookingId = $request->input('booking_id');
        $booking = Trn_Consultation_Booking::with(['branch', 'bookingStatus'])
            ->findOrFail($bookingId);
        $therapies = Trn_Booking_Therapy_detail::with('therapy')
            ->where('booking_id', $bookingId)
            ->get();

        return response()->json(['booking' => $booking, 'therapies' => $therapies]);
    }








}
