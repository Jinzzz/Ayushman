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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Trn_Consultation_Booking_Invoice;
use App\Models\Trn_Ledger_Posting;
use App\Models\Mst_Wellness;
use App\Models\Mst_Wellness_Therapyrooms;
use App\Models\Mst_Therapy_Room_Slot;
use App\Models\Mst_Membership;
use App\Models\Trn_Booking_Wellness_Detail;
use App\Models\Mst_Therapy_Therapyrooms;
use App\Models\Mst_Therapy;
use App\Models\Trn_Booking_Therapy_detail;
use App\Models\Trn_Booking_Therapy_Refund;
use App\Models\Mst_External_Doctor;
use App\Models\Trn_Therapy_Booking_Invoice_Payment;
use App\Models\Trn_Consultation_Booking_Invoice_Payment;
use App\Models\Trn_Wellness_Booking_Invoice_Payment;
use App\Models\Mst_User;
use Dompdf\Dompdf;
use View;
use Dompdf\Options;

class BookingController extends Controller
{

    public function ConsultationIndex(Request $request)
    {
        
        return view('booking.consultation.index', [
            'bookings' => Trn_Consultation_Booking::select('trn_consultation_bookings.*', 'mst_patients.patient_name', 'mst_patients.patient_code', 'mst_staffs.staff_name', 'mst_branches.branch_name', 'booking_status.master_value')
                ->join('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
                ->join('mst_staffs', 'mst_staffs.staff_id', '=', 'trn_consultation_bookings.doctor_id')
                ->join('mst_branches', 'mst_branches.branch_id', '=', 'trn_consultation_bookings.branch_id')
                ->join('mst_master_values as booking_status', 'booking_status.id', '=', 'trn_consultation_bookings.booking_status_id')
                ->where('trn_consultation_bookings.booking_type_id', 84)
                ->orderBy('trn_consultation_bookings.created_at', 'DESC')
                ->get(),
            'pageTitle' => 'Consultation Bookings'
        ]);
    }

    public function ConsultationCreate(Request $request)
    {
        
        $user_id = Auth::id();
        return view('booking.consultation.create', [
            'branches' => Mst_Branch::where('is_active', '=', 1)->orderBy('branch_name', 'ASC')->get(),
            'patients' => Mst_Patient::orderBy('patient_name', 'ASC')->get(),
            'paymentType' => Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id'),
            'genders' => Mst_Master_Value::where('master_id', 17)->pluck('master_value', 'id')->toArray(),
            'bloodgroups' => Mst_Master_Value::where('master_id', 19)->pluck('master_value', 'id')->toArray(),
            'relationships' => Mst_Master_Value::where('master_id', 18)->pluck('master_value', 'id')->toArray(),
            'maritialstatus' => Mst_Master_Value::where('master_id', 12)->pluck('master_value', 'id'),
            'user_id' => $user_id,
            'discount' => Mst_User::where('user_id',$user_id)->value('discount_percentage'),
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
            ->orderBy('time_from')
            ->orderBy('time_to')
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
        $start_date = isset($membership->start_date) ? Carbon::parse($membership->start_date)->toDateString() : null;
        $expiry_date = isset($membership->membership_expiry_date) ? Carbon::parse($membership->membership_expiry_date)->toDateString() : null;

        return response()->json([
            'membership' => [
                'package_title' => $membership->membershipPackage->package_title ?? null,
                'start_date' => $start_date,
                'expiry_date' => $expiry_date,
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
    public function storePatient(Request $request)
    {
        $request->validate([

            'patient_name' => 'required',
            'patient_mobile' => 'required|digits:10|numeric',
            'is_active' => 'required',
        ]);
        $is_active = $request->input('is_active') ? 1 : 0;
        $available_membership = $request->has('available_membership') ? 1 : 0;
        $generatedPassword = Str::random(8);

        $lastInsertedId = Mst_Patient::insertGetId([

            'patient_code' => rand(50, 100),
            'patient_name' => $request->patient_name,
            'patient_email' => $request->patient_email,
            'patient_mobile' => $request->patient_mobile,
            'patient_address' => $request->patient_address,
            'patient_gender' => $request->patient_gender,
            'patient_dob' => $request->patient_dob,
            'patient_blood_group_id' => $request->patient_blood_group_id,
            'emergency_contact_person' => $request->emergency_contact_person,
            'emergency_contact' => $request->emergency_contact,
            'maritial_status' => $request->marital_status,
            'patient_medical_history' => $request->patient_medical_history,
            'patient_current_medications' => $request->patient_current_medications,
            'patient_registration_type' => $request->patient_registration_type,
            'is_otp_verified' => 1,
            'is_approved' => 1,
            'password' => Hash::make($generatedPassword),
            'whatsapp_number' => $request->whatsapp_number,
            'available_membership' =>  $available_membership,
            'is_active' =>  $is_active,
            'created_by' => Auth::id(),
        ]);

        $leadingZeros = str_pad('', 3 - strlen($lastInsertedId), '0', STR_PAD_LEFT);
        $patientCode = 'PAT' . $leadingZeros . $lastInsertedId;

        Mst_Patient::where('id', $lastInsertedId)->update([
            'patient_code' => $patientCode
        ]);
        return redirect()->back()->with('success', 'Patient added successfully');
    }
    public function saveMember(Request $request)
    {
        $request->validate([
            'patient_id' => 'required',
            'family_member_name' => 'required',
            'family_member_phone' => 'required',
            'family_member_gender' => 'required',
      
  
        ]);

        try {
            $member = new Trn_Patient_Family_Member();
            $member->patient_id = $request['patient_id'];
            $member->family_member_name = $request['family_member_name'];
            $member->email_address = $request['family_member_email'];
            $member->mobile_number = $request['family_member_phone'];
            $member->gender_id = $request['family_member_gender'];
            $member->blood_group_id = $request['family_member_blood_group_id'];
            $member->date_of_birth = $request['family_member_dob'];
            $member->relationship_id = $request['family_member_relationship_id'];
            $member->address = $request['family_member_address'];
            $member->created_by = Auth::id();
            $member->is_active = 0;
            $member->verified = 0;
            $member->created_at = Carbon::now();
            $member->updated_at = Carbon::now();
            $member->save();

            return redirect()->back()->with('success', 'Patient added successfully');
        } catch (\Exception $e) {
            // Handle the error, log it, or return a response
            return back()->withInput()->withErrors(['error' => 'Failed to save member.']);
        }
    }

    public function patientBooking(Request $request)
    {
       
        $request->validate([
            'patient_id' => 'required',
            'staff_id'  => 'required',
            'branch_id' => 'required',
            'booking_date' => 'required',
            'timeslots' => 'required',
        ]);

        $booking = new Trn_Consultation_Booking();
        $booking->booking_reference_number = uniqid();
        $booking->booking_type_id = 84;
        $booking->patient_id = $request->patient_id;
        $booking->doctor_id = $request->staff_id;
        $booking->branch_id = $request->branch_id;
        $booking->booking_date = $request->booking_date;
        $booking->time_slot_id = $request->timeslots;
        $booking->booking_fee = $request->booking_fee;
        $booking->discount = $request->discount_total;
        $booking->payable_amount = $request->paid_amount;
        $booking->family_member_id = $request->family_id;
        $booking->booking_status_id = 87;
        $booking->is_billable = 0;
        $booking->save();

        $leadingZeros = str_pad('', 3 - strlen($booking->id), '0', STR_PAD_LEFT);
        $patientCode = 'CBRN' . $leadingZeros . $booking->id;

        Trn_Consultation_Booking::where('id', $booking->id)->update([
            'booking_reference_number' => $patientCode
        ]);
         if ($request->filled('family_id')) {
            Trn_Consultation_Booking::where('id', $booking->id)->update([
                'is_for_family_member' => 1
            ]); 
        }

        $user_id = Auth::id();
        $invoice = new Trn_Consultation_Booking_Invoice();
        $invoice->booking_id = $booking->id;
        $invoice->branch_id = $request->input('branch_id');
        $invoice->booking_date = $request->input('booking_date');
        $invoice->invoice_date = Carbon::now();
        $invoice->paid_amount = $request->input('booking_fee');
        $invoice->discount = $request->input('discount_total');
        $invoice->created_by = $user_id;
        $invoice->save();

        // Updating the booking_invoice_number field
        $invoice->booking_invoice_number = 'INV100' . $invoice->id;
        $invoice->bill_token = 'CN_TKN' . $invoice->id;
        $invoice->save();
        
        $payableAmounts = $request->input('payable_amount');
        $paymentModes = $request->input('payment_mode');
        $depositTos = $request->input('deposit_to');
        $referenceNos = $request->input('refernce_no');
        
        if ($request->input('payment_mode') !== null && count(array_filter($request->input('payment_mode'))) > 0 && $request->input('deposit_to') !== null && count(array_filter($request->input('deposit_to'))) > 0) {
        foreach ($payableAmounts as $key => $value) {
            $invoicePayment = new Trn_Consultation_Booking_Invoice_Payment();
            $invoicePayment->consultation_booking_invoice_id = $booking->id;
            $invoicePayment->paid_amount = $payableAmounts[$key] ?? null;
            $invoicePayment->payment_mode = $paymentModes[$key] ?? null;
            $invoicePayment->deposit_to = $depositTos[$key] ?? null;
            $invoicePayment->reference_no = $referenceNos[$key] ?? null;
            // Add other fields as needed
            $invoicePayment->save();
        }
        }


        // Check if payment_mode and deposit_to exist in the request and set them if true
        if ($request->input('payment_mode') !== null && count(array_filter($request->input('payment_mode'))) > 0 && $request->input('deposit_to') !== null && count(array_filter($request->input('deposit_to'))) > 0) {
            Trn_Consultation_Booking_Invoice::where('id', $invoice->id)
                ->update([
                    'paid_amount' => $request->input('booking_fee'),
                    'discount' => $request->input('discount_total'),
                    'amount' => $request->input('booking_fee'),
                    'is_paid' => 1,
                ]);
            Trn_Consultation_Booking::where('id', $booking->id)
                ->update([
                    'booking_status_id' => 88,
                    'is_billable' => 1,
                    'is_paid' => 1,
                 ]);
        }
       if ($request->noBill != 1) {
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'CN_TKN' . $invoice->id,
            'account_ledger_id' => 1,
            'entity_id' => $request->patient_id,
            'debit' => $request->input('booking_fee'),
            'credit' => 0,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $invoice->id,
            'narration' => 'Consultation Booking Invoice Payment'
        ]);

        // Consulting Revenue
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'CN_TKN' . $invoice->id,
            'account_ledger_id' => 85,
            'entity_id' => 0,
            'debit' => 0,
            'credit' => $request->input('booking_fee'),
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $invoice->id,
            'narration' => 'Consultation Booking Invoice Payment'
        ]);
        //Accounts Receivable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'CN_TKN' . $invoice->id,
            'account_ledger_id' => 1,
            'entity_id' => $request->patient_id,
            'debit' => 0,
            'credit' => $request->input('booking_fee'),
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $invoice->id,
            'narration' => 'Consultation Booking Invoice Payment'
        ]);
        //Cash or Bank Account
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'CN_TKN' . $invoice->id,
            'account_ledger_id' => 4,
            'entity_id' => $request->patient_id,
            'debit' => $request->input('booking_fee'),
            'credit' => 0,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $invoice->id,
            'narration' => 'Consultation Booking Invoice Payment'
        ]);
}
        return redirect()->route('bookings.consultation.index')->with('success', 'Consultation Booking Created Successfully');
    }

    public function showBooking($id)
    {
        $pageTitle = "View Consultation Booking";
        $patient = Trn_Consultation_Booking::select('trn_consultation_bookings.*', 'mst_patients.patient_name', 'mst_patients.patient_code', 'mst_staffs.staff_name', 'mst_branches.branch_name', 'booking_status.master_value')
            ->join('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
            ->join('mst_staffs', 'mst_staffs.staff_id', '=', 'trn_consultation_bookings.doctor_id')
            ->join('mst_branches', 'mst_branches.branch_id', '=', 'trn_consultation_bookings.branch_id')
            ->join('mst_master_values as booking_status', 'booking_status.id', '=', 'trn_consultation_bookings.booking_status_id')
            ->where('trn_consultation_bookings.id', $id)
            ->where('trn_consultation_bookings.booking_type_id', 84)
            ->orderBy('trn_consultation_bookings.created_at', 'DESC')
            ->firstOrFail();
          

        $invoice = Trn_Consultation_Booking_Invoice::where('booking_id', $id)->first();
        
    $paymentDetails = Trn_Consultation_Booking_Invoice_Payment::select('trn__consultation__booking__invoice__payments.*', 'mst_master_values.*', 'mst__account__ledgers.ledger_name')
    ->leftJoin('mst_master_values', 'mst_master_values.id', '=', 'trn__consultation__booking__invoice__payments.payment_mode')
    ->leftJoin('mst__account__ledgers', 'mst__account__ledgers.id', '=', 'trn__consultation__booking__invoice__payments.deposit_to')
    ->where('trn__consultation__booking__invoice__payments.consultation_booking_invoice_id', $id)
    ->get();


        if ($invoice) {
            return view('booking.consultation.show', compact('patient', 'invoice','pageTitle','paymentDetails'));
        } else {
            return view('booking.consultation.show', compact('patient','pageTitle'));
        }
    }


    public function deleteBooking($id)
    {

        $invoice = Trn_Consultation_Booking_Invoice::where('booking_id', $id)->first();
        if ($invoice) {
            Trn_Ledger_Posting::where('master_id', 'CN_TKN.' . $invoice->id)->delete();
            $invoice->delete();
        }
        Trn_Consultation_Booking::where('id', $id)->delete();
        return 1;
    }

    public function WellnessIndex(Request $request)
    {
        return view('booking.wellness.index', [
            'bookings' => Trn_Consultation_Booking::select('trn_consultation_bookings.*', 'mst_patients.patient_name','mst_branches.branch_name','booking_status.master_value')
                ->join('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
                ->join('mst_branches', 'mst_branches.branch_id', '=', 'trn_consultation_bookings.branch_id')
                ->join('mst_master_values as booking_status', 'booking_status.id', '=', 'trn_consultation_bookings.booking_status_id')
                ->where('trn_consultation_bookings.booking_type_id', 85)
                ->orderBy('trn_consultation_bookings.created_at', 'DESC')
                ->get(),
            'pageTitle' => 'Wellness Bookings'
        ]);
    }
    

    public function WellnessCreate(Request $request)
    {
        return view('booking.wellness.create', [
            'branches' => Mst_Branch::where('is_active', '=', 1)->orderBy('branch_name', 'ASC')->get(),
            'patients' => Mst_Patient::orderBy('patient_name', 'ASC')->get(),
            'paymentType' => Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id'),
            'genders' => Mst_Master_Value::where('master_id', 17)->pluck('master_value', 'id')->toArray(),
            'membership' =>  Mst_Membership::pluck('membership_name', 'id'),
            'bloodgroups' => Mst_Master_Value::where('master_id', 19)->pluck('master_value', 'id'),
            'relationships' => Mst_Master_Value::where('master_id', 18)->pluck('master_value', 'id')->toArray(),
            'maritialstatus' => Mst_Master_Value::where('master_id', 12)->pluck('master_value', 'id'),
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

        $currentWeekday = Carbon::parse($bookingDate)->format('l');
        $timeslots  = Mst_Therapy_Room_Slot::whereIn('therapy_room_id', $therapyRooms)
            ->whereHas('weekDay', function ($query) use ($currentWeekday) {
                $query->where('master_id', 3) // Assuming master_id 3 represents the weekday in the weekDay table
                    ->where('master_value', $currentWeekday);
            })
            ->with(['slot'])
            ->join('mst_timeslots', 'mst__therapy__room__slots.timeslot', '=', 'mst_timeslots.id')
            ->select('mst__therapy__room__slots.*', 'mst_timeslots.time_from', 'mst_timeslots.time_to')
            ->orderBy('mst_timeslots.time_from')
             ->orderBy('mst_timeslots.time_to')
            ->get();

        $timeslotInfo = [];
        foreach ($timeslots as $slots) {
            $timeslotInfo[] = [
                'therapy_room_name' => $slots->therapyRoom->room_name,
                 'time_from' => isset($slots->slot->time_from) ? $slots->slot->time_from : 0,
                 'time_to' => isset($slots->slot->time_to) ? $slots->slot->time_to : 0,
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

    public function getPatientMembershipDetails(Request $request)
    {
        $patientId = $request->input('patient_id');

        $familyMembers = Trn_Patient_Family_Member::where('patient_id', $patientId)
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json(['family_members' => $familyMembers]);
    }

    public function patientWellnessBooking(Request $request)
    {
        
        $request->validate([
            'patient_id' => 'required',
            'branch_id' => 'required',
            'booking_date' => 'required',
            'timeslots' => 'required',
        ]);

        $booking = new Trn_Consultation_Booking();
        $booking->booking_reference_number = uniqid();
        $booking->booking_type_id = 85;
        $booking->patient_id = $request->patient_id;
        $booking->doctor_id = $request->doctor_id;
        $booking->branch_id = $request->branch_id;
        $booking->booking_date = $request->booking_date;
        $booking->booking_fee = array_sum($request->input('booking_fee'));
        $booking->discount = $request->discount;
        $booking->payable_amount = $request->total_amount;
        $booking->booking_status_id = 87;
        $booking->save();
        
        $leadingZeros = str_pad('', 3 - strlen($booking->id), '0', STR_PAD_LEFT);
        $patientCode = 'WBRN' . $leadingZeros . $booking->id;

        Trn_Consultation_Booking::where('id', $booking->id)->update([
            'booking_reference_number' => $patientCode
        ]);
        $user_id = Auth::id();
        $invoice = new Trn_Consultation_Booking_Invoice();
        $invoice->booking_id = $booking->id;
        $invoice->branch_id = $request->input('branch_id');
        $invoice->booking_date = $request->input('booking_date');
        $booking->discount = $request->discount;
        $invoice->invoice_date = Carbon::now();
        $invoice->created_by = $user_id;
        $invoice->save();

        Trn_Consultation_Booking_Invoice::where('id', $invoice->id)->update([
                        'booking_invoice_number' =>'INV100' . $invoice->id,
                        'bill_token' =>'WL_TKN' . $invoice->id,
                    ]);

        foreach ($request['wellness_id'] as $index => $wellnessId) {
        Trn_Booking_Wellness_Detail::create([
                'booking_id' => $booking->id,
                'wellness_id' => $wellnessId,
                'wellness_fee' => $request['booking_fee'][$index],
                'booking_timeslot' => $request['timeslots'][$index],
            ]);
        }
        
            if ($request->input('payment_mode') !== null && count(array_filter($request->input('payment_mode'))) > 0 && $request->input('deposit_to') !== null && count(array_filter($request->input('deposit_to'))) > 0) {
            foreach ($request->input('payable_amount') as $key => $value) {
                $invoicePayment = new Trn_Wellness_Booking_Invoice_Payment();
                $invoicePayment->wellness_booking_invoice_id = $booking->id;
                $invoicePayment->paid_amount = $request->input('payable_amount')[$key];
                $invoicePayment->payment_mode = $request->input('payment_mode')[$key];
                $invoicePayment->deposit_to = $request->input('deposit_to')[$key];
                $invoicePayment->reference_no = $request->input('refernce_no')[$key];
                // Add other fields as needed
                $invoicePayment->save();
            }
        }


        // Check if payment_mode and deposit_to exist in the request and set them if true
      
        // if ($request->filled('payment_mode') && $request->filled('deposit_to')) {
            if ($request->input('payment_mode') !== null && count(array_filter($request->input('payment_mode'))) > 0 && $request->input('deposit_to') !== null && count(array_filter($request->input('deposit_to'))) > 0) {
            Trn_Consultation_Booking_Invoice::where('id', $invoice->id)
                ->update([
                    'paid_amount' => array_sum($request->input('booking_fee')),
                    'discount' => $request->input('discount'),
                    'amount' => array_sum($request->input('booking_fee')),
         
                    'is_paid' => 1,
                ]);
            
            Trn_Consultation_Booking::where('id', $booking->id)
                ->update(['booking_status_id' => $request->noBill == 1 ? 87 : 88,
                          'is_billable' => 1,
                          'is_paid' => 1,
                ]);
        }
  if ($request->noBill != 1) {
        //Accounts Payable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'WL_TKN' . $booking->id,
            'account_ledger_id' => 1,
            'entity_id' => $request->patient_id,
            'debit' => array_sum($request->input('booking_fee')),
            'credit' => 0,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Wellness Booking Invoice Payment'
        ]);

        //Wellness Revenue
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'WL_TKN' . $booking->id,
            'account_ledger_id' => 86,
            'entity_id' => 0,
            'debit' => 0,
            'credit' => array_sum($request->input('booking_fee')),
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Wellness Booking Invoice Payment'
        ]);
        //Accounts Receivable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'WL_TKN' . $booking->id,
            'account_ledger_id' => 1,
            'entity_id' => $request->patient_id,
            'debit' => 0,
            'credit' => array_sum($request->input('booking_fee')),
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Wellness Booking Invoice Payment'
        ]);
        //Cash or Bank Account
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'WL_TKN' . $booking->id,
            'account_ledger_id' => 4,
            'entity_id' => $request->patient_id,
            'debit' => array_sum($request->input('booking_fee')),
            'credit' => 0,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Wellness Booking Invoice Payment'
        ]);
  }


        return redirect()->route('bookings.wellness.index')->with('success', 'Wellness booking added  successfully.');
    }

    public function viewWellnessBooking($id)
    {
       $pageTitle = "View Wellness Booking";
        $patient = Trn_Consultation_Booking::select('trn_consultation_bookings.*', 'mst_patients.patient_name', 'mst_patients.patient_code','mst_branches.branch_name', 'booking_status.master_value')
            ->join('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
            ->join('mst_branches', 'mst_branches.branch_id', '=', 'trn_consultation_bookings.branch_id')
            ->join('mst_master_values as booking_status', 'booking_status.id', '=', 'trn_consultation_bookings.booking_status_id')
            ->where('trn_consultation_bookings.id', $id)
            ->orderBy('trn_consultation_bookings.created_at', 'DESC')
            ->firstOrFail();


        $invoice = Trn_Consultation_Booking_Invoice::where('booking_id', $id)->first();

        $wellnessDetails = Trn_Consultation_Booking::select('trn_consultation_bookings.id', 'trn__booking__wellness__details.*','mst_wellness.wellness_name')
        ->join('trn__booking__wellness__details', 'trn__booking__wellness__details.booking_id', '=', 'trn_consultation_bookings.id')
        ->join('mst_wellness', 'trn__booking__wellness__details.wellness_id', '=', 'mst_wellness.wellness_id')
        ->where('trn_consultation_bookings.id', $id)
        ->get();
                

                
        $paymentDetails = Trn_Wellness_Booking_Invoice_Payment::select('trn__wellness__booking__invoice__payments.*', 'mst_master_values.*', 'mst__account__ledgers.ledger_name')
        ->leftJoin('mst_master_values', 'mst_master_values.id', '=', 'trn__wellness__booking__invoice__payments.payment_mode')
        ->leftJoin('mst__account__ledgers', 'mst__account__ledgers.id', '=', 'trn__wellness__booking__invoice__payments.deposit_to')
        ->where('trn__wellness__booking__invoice__payments.wellness_booking_invoice_id', $id)
        ->get();
    
        if ($invoice) {
            return view('booking.wellness.view', compact('patient', 'invoice','wellnessDetails','pageTitle','paymentDetails'));
        } else {
            return view('booking.wellness.view', compact('patient','pageTitle'));
        }

    }

    public function deleteWellnessBooking($id)
    {

        $invoice = Trn_Consultation_Booking_Invoice::where('booking_id', $id)->first();
        if ($invoice) {
            Trn_Ledger_Posting::where('master_id', 'WL_TKN.' . $invoice->id)->delete();
            $invoice->delete();
        }

        Trn_Booking_Wellness_Detail::where('booking_id', $id)->delete();
        Trn_Consultation_Booking::where('id', $id)->delete();
        return 1;


    }
    
    public function TherapyBooking(Request $request)
    {
        return view('booking.therapy.index', [
            'bookings' => Trn_Consultation_Booking::select('trn_consultation_bookings.*', 'mst_patients.patient_name','mst_branches.branch_name','booking_status.master_value')
                ->join('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
                ->join('mst_branches', 'mst_branches.branch_id', '=', 'trn_consultation_bookings.branch_id')
                ->join('mst_master_values as booking_status', 'booking_status.id', '=', 'trn_consultation_bookings.booking_status_id')
                ->where('trn_consultation_bookings.booking_type_id', 86)
                ->orderBy('trn_consultation_bookings.created_at', 'DESC')
                ->get(),
            'pageTitle' => 'Therapy Bookings'
        ]);
    }

    
    public function TherapyCreate(Request $request)
    {
        return view('booking.therapy.create', [
            'branches' => Mst_Branch::where('is_active','=',1)->orderBy('branch_name','ASC')->get(),
            'patients' => Mst_Patient::orderBy('patient_name','ASC')->get(),
            'paymentType' => Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id'),
            'genders' => Mst_Master_Value::where('master_id', 17)->pluck('master_value', 'id')->toArray(),
            'membership' =>  Mst_Membership::pluck('membership_name', 'id'),
            'bloodgroups' => Mst_Master_Value::where('master_id', 19)->pluck('master_value', 'id'),
            'relationships' => Mst_Master_Value::where('master_id', 18)->pluck('master_value', 'id')->toArray(),
            'maritialstatus' => Mst_Master_Value::where('master_id', 12)->pluck('master_value', 'id'),
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
                    ->join('mst_timeslots', 'mst__therapy__room__slots.timeslot', '=', 'mst_timeslots.id')
                    ->select('mst__therapy__room__slots.*', 'mst_timeslots.time_from', 'mst_timeslots.time_to')
                    ->orderBy('mst_timeslots.time_from')
                    ->orderBy('mst_timeslots.time_to')
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
        $refunds=Trn_Booking_Therapy_Refund::with('patient','booking')->orderBy('created_at','DESC')->get();
        //d//d($refunds->loadMissing('patient'));
        return view('booking.therapy-refund.index', [
            'refunds' => $refunds,
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
    public function TherapyRefundStore(Request $request)
    {
       $request->validate([
        'patient_id' => 'required',
        'booking_id' => 'required',
        'refund_mode' => 'required',
        'refund_amount' => [
            'required',
            function ($attribute, $value, $fail) use ($request) {
                if ($request->refund_mode == 2 && $value > $request->paid_amount) {
                    $fail('The refund amount must be less than or equal to the paid amount.');
                }
            },
        ],
    ]);

       $therapy_refund= new Trn_Booking_Therapy_Refund();
       $therapy_refund->booking_id=$request->booking_id;
       $therapy_refund->booking_amount=$request->paid_amount;
       $therapy_refund->patient_id=$request->patient_id;
       $therapy_refund->refund_type=$request->refund_mode;
       $therapy_refund->refund_amount=$request->refund_amount;
       $therapy_refund->payment_mode=$request->payment_mode;
       $therapy_refund->deposit_to=$request->deposit_to;
       $therapy_refund->save();
       $booking=Trn_Consultation_Booking::where('id',$request->booking_id);
       $booking->update(['booking_status_id'=>90]);
        
        
         Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'TH_REFND' . $therapy_refund->id,
            'account_ledger_id' => $request->deposit_to,
            'entity_id' => $request->patient_id,
            'debit' => $request->refund_amount,
            'credit' => 0,
            'branch_id' => $booking->first()->branch_id,
            'transaction_id' =>  $therapy_refund->id,
            'narration' => 'Therapy Refund'
        ]);
       
        return redirect()->route('bookings.therapy-refund.index')->with('success', 'Therapy Refund has been completed successfully!');
       
       //dd('Under Development');
    }

    public function fetchRefundBookings(Request $request)
    {
        $patientId = $request->input('patient_id');

        $bookings = Trn_Consultation_Booking::where('patient_id', $patientId)
            ->where('booking_type_id', 86)
            ->where('is_paid', 1)
            ->where('booking_status_id','=', 88)
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
    
    public function getInternalDoctors() {
    $doctors = Mst_Staff::where('staff_type', 20)->where('is_active',1)->get();
    return response()->json($doctors);
    }

    public function getExternalDoctors() {
    $doctors = Mst_External_Doctor::all();
    return response()->json($doctors);
   }
     public function patientTherapyBooking(Request $request)
     {
          
        $request->validate([
            'patient_id' => 'required',
            'branch_id' => 'required',
            'booking_date' => 'required',
            'timeslots' => 'required',
        ]);

        $booking = new Trn_Consultation_Booking();
        $booking->booking_reference_number = uniqid();
        $booking->booking_type_id = 86;
        $booking->patient_id = $request->patient_id;
        $booking->doctor_id =$request->doctor_id;
        $booking->branch_id = $request->branch_id;
        $booking->booking_date = $request->booking_date;
        $booking->booking_fee = array_sum($request->input('booking_fee'));
        $booking->discount = $request->discount;
        $booking->payable_amount = $request->total_amount;
        $booking->booking_status_id = 87;
        $booking->is_billable = 0;
        $booking->save();
        
        $leadingZeros = str_pad('', 3 - strlen($booking->id), '0', STR_PAD_LEFT);
        $patientCode = 'TPRN' . $leadingZeros . $booking->id;

        Trn_Consultation_Booking::where('id', $booking->id)->update([
            'booking_reference_number' => $patientCode
        ]);
        $user_id = Auth::id();
        $invoice = new Trn_Consultation_Booking_Invoice();
        $invoice->booking_id = $booking->id;
        $invoice->branch_id = $request->input('branch_id');
        $invoice->booking_date = $request->input('booking_date');
        $invoice->invoice_date = Carbon::now();
        $invoice->created_by = $user_id;
        $invoice->save();

        Trn_Consultation_Booking_Invoice::where('id', $invoice->id)->update([
                        'booking_invoice_number' =>'INV100' . $invoice->id,
                        'bill_token' =>'TP_TKN' . $invoice->id,
                    ]);

        foreach ($request['therapy_id'] as $index => $therapyId) {
        Trn_Booking_Therapy_detail::create([
                'booking_id' => $booking->id,
                'therapy_id' => $therapyId,
                'therapy_fee' => $request['booking_fee'][$index],
                'booking_timeslot' => $request['timeslots'][$index],
            ]);
        }

        
        $payableAmounts = $request->input('payable_amount');
        $paymentModes = $request->input('payment_mode');
        $depositTos = $request->input('deposit_to');
        $referenceNos = $request->input('refernce_no');
        
        if ($request->input('payment_mode') !== null && count(array_filter($request->input('payment_mode'))) > 0 && $request->input('deposit_to') !== null && count(array_filter($request->input('deposit_to'))) > 0) {
        foreach ($payableAmounts as $key => $value) {
            $invoicePayment = new Trn_Therapy_Booking_Invoice_Payment();
            $invoicePayment->therapy_booking_invoice_id = $booking->id;
            $invoicePayment->paid_amount = $payableAmounts[$key] ?? null;
            $invoicePayment->payment_mode = $paymentModes[$key] ?? null;
            $invoicePayment->deposit_to = $depositTos[$key] ?? null;
            $invoicePayment->reference_no = $referenceNos[$key] ?? null;
            // Add other fields as needed
            $invoicePayment->save();
        }
        }
        
        // Check if payment_mode and deposit_to exist in the request and set them if true
        // if ($request->filled('payment_mode') && $request->filled('deposit_to')) {
            if ($request->input('payment_mode') !== null && count(array_filter($request->input('payment_mode'))) > 0 && $request->input('deposit_to') !== null && count(array_filter($request->input('deposit_to'))) > 0) {
            Trn_Consultation_Booking_Invoice::where('id', $invoice->id)
                ->update([
                    'paid_amount' => array_sum($request->input('booking_fee')),
                    'discount' => $request->input('discount'),
                    'amount' => array_sum($request->input('booking_fee')),
                    'is_paid' => 1,
                ]);
            Trn_Consultation_Booking::where('id', $booking->id)
                ->update(['booking_status_id' => 88,
                          'is_billable' => 1,
                          'is_paid' => 1,
                         ]);
        }
  if ($request->noBill != 1) {
        //Accounts Payable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'TP_TKN' . $booking->id,
            'account_ledger_id' => 1,
            'entity_id' => $request->patient_id,
            'debit' => array_sum($request->input('booking_fee')),
            'credit' => 0,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Therapy Booking Invoice Payment'
        ]);

        //Wellness Revenue
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'TP_TKN' . $booking->id,
            'account_ledger_id' => 87,
            'entity_id' => 0,
            'debit' => 0,
            'credit' => array_sum($request->input('booking_fee')),
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Therapy Booking Invoice Payment'
        ]);
        //Accounts Receivable
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'TP_TKN' . $booking->id,
            'account_ledger_id' => 1,
            'entity_id' => $request->patient_id,
            'debit' => 0,
            'credit' => array_sum($request->input('booking_fee')),
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Therapy Booking Invoice Payment'
        ]);
        //Cash or Bank Account
        Trn_Ledger_Posting::create([
            'posting_date' => Carbon::now(),
            'master_id' => 'TP_TKN' . $booking->id,
            'account_ledger_id' => 4,
            'entity_id' => $request->patient_id,
            'debit' => array_sum($request->input('booking_fee')),
            'credit' => 0,
            'branch_id' => $request->input('branch_id'),
            'transaction_id' =>  $booking->id,
            'narration' => 'Therapy Booking Invoice Payment'
        ]);
  }


        return redirect()->route('bookings.therapy.index')->with('success', 'Therapy Booking Completed successfully');
     }
     
         public function viewTherapyBooking($id)
    {
        $pageTitle = "View Therapy Booking";
        $patient = Trn_Consultation_Booking::select('trn_consultation_bookings.*', 'mst_patients.patient_name', 'mst_patients.patient_code','mst_branches.branch_name', 'booking_status.master_value','mst_staffs.staff_name')
            ->join('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
             ->join('mst_staffs', 'mst_staffs.staff_id', '=', 'trn_consultation_bookings.doctor_id')
            ->join('mst_branches', 'mst_branches.branch_id', '=', 'trn_consultation_bookings.branch_id')
            ->join('mst_master_values as booking_status', 'booking_status.id', '=', 'trn_consultation_bookings.booking_status_id')
            ->where('trn_consultation_bookings.id', $id)
            ->orderBy('trn_consultation_bookings.created_at', 'DESC')
            ->firstOrFail();
      

            $invoice = Trn_Consultation_Booking_Invoice::where('booking_id', $id)->first();
            
            $therapyDetails = Trn_Consultation_Booking::select('trn_consultation_bookings.id', 'trn__booking__therapy_details.*','mst_therapies.therapy_name')
                ->join('trn__booking__therapy_details', 'trn__booking__therapy_details.booking_id', '=', 'trn_consultation_bookings.id')
                ->join('mst_therapies', 'trn__booking__therapy_details.therapy_id', '=', 'mst_therapies.id')
                ->where('trn_consultation_bookings.id', $id)
                ->get();
                
        $paymentDetails = Trn_Therapy_Booking_Invoice_Payment::select('trn__therapy__booking__invoice__payments.*', 'mst_master_values.*', 'mst__account__ledgers.ledger_name')
            ->leftJoin('mst_master_values', 'mst_master_values.id', '=', 'trn__therapy__booking__invoice__payments.payment_mode')
            ->leftJoin('mst__account__ledgers', 'mst__account__ledgers.id', '=', 'trn__therapy__booking__invoice__payments.deposit_to')
            ->where('trn__therapy__booking__invoice__payments.therapy_booking_invoice_id', $id)
            ->get();
            
            if ($invoice) {
                return view('booking.therapy.therapyview', compact('patient', 'invoice', 'therapyDetails','pageTitle','paymentDetails'));
            } else {
                // Create an empty invoice object if no invoice is found
                $invoice = new Trn_Consultation_Booking_Invoice();
                return view('booking.therapy.therapyview', compact('patient', 'invoice', 'wellnessDetails','pageTitle'));
            }

    }
    
        public function deleteTherapyBooking($id)
    {

        $invoice = Trn_Consultation_Booking_Invoice::where('booking_id', $id)->first();
        if ($invoice) {
            Trn_Ledger_Posting::where('master_id', 'TP_TKN.' . $invoice->id)->delete();
            $invoice->delete();
        }

        Trn_Booking_Therapy_detail::where('booking_id', $id)->delete();
        Trn_Consultation_Booking::where('id', $id)->delete();
        return 1;


    }
    
        public function generatePDF($id)
    {
        $data = [];
        $bookings = Trn_Consultation_Booking::where('trn_consultation_bookings.id', $id)
                ->select('trn_consultation_bookings.*', 'mst_patients.patient_name', 'mst_patients.patient_code', 'mst_staffs.staff_name', 'mst_branches.branch_name')
                ->join('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
                ->join('mst_staffs', 'mst_staffs.staff_id', '=', 'trn_consultation_bookings.doctor_id')
                ->join('mst_branches', 'mst_branches.branch_id', '=', 'trn_consultation_bookings.branch_id')->first();
        $bookings_invoice = Trn_Consultation_Booking_Invoice::where('booking_id', $id)->first();
        $data['booking_reference_number'] = $bookings->booking_reference_number;

        $dompdf = new Dompdf();
        $view = View::make('booking.consultation.print_invoice', ['data' => $data, 'bookings_invoice' => $bookings_invoice, 'bookings' => $bookings ]);
        $html = $view->render();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true); // Enable PHP code within the HTML (optional)
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();
        $pdfFilename = 'invoice.pdf';
        file_put_contents($pdfFilename, $pdfContent);
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $pdfFilename . '"');
    }
    
            public function generateWellnessPDF($id)
    {
        $data = [];
        $bookings = Trn_Consultation_Booking::where('trn_consultation_bookings.id', $id)
                ->select('trn_consultation_bookings.*', 'mst_patients.patient_name', 'mst_patients.patient_code','mst_branches.branch_name')
                ->join('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
                ->join('mst_branches', 'mst_branches.branch_id', '=', 'trn_consultation_bookings.branch_id')->first();
        $bookings_invoice = Trn_Consultation_Booking_Invoice::where('booking_id', $id)->first();
        $wellnessDetails = Trn_Consultation_Booking::select('trn_consultation_bookings.id', 'trn__booking__wellness__details.*','mst_wellness.wellness_name')
        ->join('trn__booking__wellness__details', 'trn__booking__wellness__details.booking_id', '=', 'trn_consultation_bookings.id')
        ->join('mst_wellness', 'trn__booking__wellness__details.wellness_id', '=', 'mst_wellness.wellness_id')
        ->where('trn_consultation_bookings.id', $id)
        ->get();
          $sum = $wellnessDetails->sum('wellness_fee');
        $data['booking_reference_number'] = $bookings->booking_reference_number;

        $dompdf = new Dompdf();
        $view = View::make('booking.wellness.print_invoice', ['data' => $data, 'bookings_invoice' => $bookings_invoice, 'bookings' => $bookings, 'wellnessDetails' =>$wellnessDetails, 'sum' => $sum]);
        $html = $view->render();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true); // Enable PHP code within the HTML (optional)
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();
        $pdfFilename = 'invoice.pdf';
        file_put_contents($pdfFilename, $pdfContent);
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $pdfFilename . '"');
    }
    
            public function generateTherapyPDF($id)
    {
        $data = [];
        $bookings = Trn_Consultation_Booking::where('trn_consultation_bookings.id', $id)
                ->select('trn_consultation_bookings.*', 'mst_patients.patient_name', 'mst_patients.patient_code','mst_branches.branch_name')
                ->join('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
                ->join('mst_staffs', 'mst_staffs.staff_id', '=', 'trn_consultation_bookings.doctor_id')
                ->join('mst_branches', 'mst_branches.branch_id', '=', 'trn_consultation_bookings.branch_id')->first();
        $bookings_invoice = Trn_Consultation_Booking_Invoice::where('booking_id', $id)->first();
        $data['booking_reference_number'] = $bookings->booking_reference_number;
        $therapyDetails = Trn_Consultation_Booking::select('trn_consultation_bookings.id', 'trn__booking__therapy_details.*','mst_therapies.therapy_name')
                ->join('trn__booking__therapy_details', 'trn__booking__therapy_details.booking_id', '=', 'trn_consultation_bookings.id')
                ->join('mst_therapies', 'trn__booking__therapy_details.therapy_id', '=', 'mst_therapies.id')
                ->where('trn_consultation_bookings.id', $id)
                ->get();
        $sum = $therapyDetails->sum('therapy_fee');
        $dompdf = new Dompdf();
        $view = View::make('booking.therapy.print_invoice', ['data' => $data, 'bookings_invoice' => $bookings_invoice, 'bookings' => $bookings, 'therapyDetails' => $therapyDetails,'sum' => $sum]);
        $html = $view->render();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true); // Enable PHP code within the HTML (optional)
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();
        $pdfFilename = 'invoice.pdf';
        file_put_contents($pdfFilename, $pdfContent);
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $pdfFilename . '"');
    }


}
