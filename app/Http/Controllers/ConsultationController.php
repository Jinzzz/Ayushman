<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Staff;
use App\Models\Mst_Medicine;
use App\Models\Mst_Therapy;
use App\Models\Trn_Prescription;
use App\Models\Trn_Prescription_Details;
use App\Models\Trn_Booking_Therapy_detail;

class ConsultationController extends Controller
{
    
    public function ConsultIndex(Request $request)
    {
        $userType = Auth::user()->user_type_id;
        if($userType == 20) //a doctor
        {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            if ($staff) {
            $booking = Trn_Consultation_Booking::with('patient','familyMember','branch','bookingStatus')->where('doctor_id',$staff->staff_id)->where('booking_type_id',84)->where('booking_status_id',88)->orderBy('created_at','DESC')->get();
            }
        }else{
            $booking = Trn_Consultation_Booking::with('patient','familyMember','branch','bookingStatus')->where('booking_type_id',84)->where('booking_status_id',88)->orderBy('created_at','DESC')->get(); //confirmed bookings only.
        }
        return view('doctor.consultation.index', [
            'bookings' => $booking,
            'pageTitle' => 'Consultation Bookings'
        ]);
    }

    public function PrescriptionAdd($id, Request $request)
    {
        $bookingInfo = Trn_Consultation_Booking::findOrFail($id);
        return view('doctor.consultation.prescription', [
            'pageTitle' => 'Add Prescriptions',
            'medicines' => Mst_Medicine::where('is_active',1)->orderBy('created_at','DESC')->get(),
            'therapies' => Mst_Therapy::where('is_active', 1)
            ->select('id', 'therapy_name')
            ->get(),
            'bookingInfo' => $bookingInfo,
        ]);
    }
     public function prescriptionStore(Request $request)
    { 
        try {
            //dd($request->input('therapy_id'));
            $validator = Validator::make($request->all(), [
                'diagnosis' => 'required',
                'advice' => 'required',
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
    
            DB::beginTransaction();
    
            $prescription = new Trn_Prescription();
            $prescription->Booking_id = $request->booking_id;
            $prescription->doctor_id = Auth::user()->staff_id;
            $prescription->diagnosis = $request->diagnosis;
            $prescription->advice = $request->advice;
            $prescription->duration =  0;
            $prescription->save();
    
            $prescriptionId = DB::getPdo()->lastInsertId();
            $medicineIds = $request->input('medicine_id');
            $medicineDosages = $request->input('dosage');
            $medicineDurations = $request->input('duration');
    
            //array_shift($medicineDosages);
            //array_shift($medicineDurations);
            
            //dd($medicineIds,$medicineDosages,$medicineDurations);
    
            foreach ($medicineIds as $key => $medicineId) {
                $detail = new Trn_Prescription_Details();
                $detail->priscription_id = $prescriptionId;
                $detail->medicine_id = $medicineId;
                $detail->duration = $medicineDurations[$key];
                $detail->medicine_dosage = $medicineDosages[$key];
                $detail->remarks = '';
                $detail->save();
            }
            $booking=Trn_Consultation_Booking::find($request->booking_id);
            $booking->booking_status_id=89;
            $booking->update();
            
            $therapyIds = $request->input('therapy_id');
            $bookingFees = $request->input('booking_fee');
            $timeSlots = $request->input('timeslots');
            
            // Check if all arrays have the same count
            if (count($therapyIds) === count($bookingFees) && count($bookingFees) === count($timeSlots)) {
                foreach ($therapyIds as $key => $therapyId) {
                    // Check if any required field is empty for current row
                    if (empty($therapyId) || empty($bookingFees[$key]) || empty($timeSlots[$key])) {
                        // If any required field is empty, skip adding data for this row
                        continue;
                    }
            
                    $detail = new Trn_Booking_Therapy_detail();
                    $detail->therapy_id = $therapyId;
                    $detail->booking_id = $request->booking_id;
                    $detail->therapy_fee = $bookingFees[$key];
                    $detail->booking_timeslot = $timeSlots[$key];
                    $detail->save();
                }
            } 

            DB::commit();
            return redirect()->route('consultation.index')->with('success', 'Medicine prescribed successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'An error occurred while processing the request.');
        }
    }
    
    public function PatientHistory($id, Request $request)
    {
        $bookingInfo = Trn_Consultation_Booking::with('patient','familyMember','branch','bookingStatus')->findOrFail($id);
        if($bookingInfo->is_for_family_member !== null && $bookingInfo->is_for_family_member > 0)
        {
            
            $booked_for=$bookingInfo->familyMember['id'];
            $bookingPreviousIds = Trn_Consultation_Booking::where('family_member_id',$booked_for)->where('booking_type_id',84)->where('booking_status_id',89)->pluck('id');
        }
        else
        {
            $booked_for=$bookingInfo->patient['id'];
            $bookingPreviousIds = Trn_Consultation_Booking::where('patient_id',$booked_for)->where('booking_type_id',84)->where('booking_status_id',89)->pluck('id');
        }
        
        $patient_histories=Trn_Prescription::with('Staff','BookingDetails','BookingDetails.bookingStatus','BookingDetails.timeSlot','BookingDetails.therapyBookings','PrescriptionDetails','PrescriptionDetails.medicine')->whereIn('Booking_id', $bookingPreviousIds)->orderBy('created_at','DESC')->get();
        //dd($patient_histories);
        return view('doctor.consultation.patient-history', [
            'pageTitle' => 'Patient History',
            'patient_histories'=>$patient_histories
        ]);
    }
     public function ConsultHistory(Request $request)
    {
        $userType = Auth::user()->user_type_id;
        if($userType == 20) //a doctor
        {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            if ($staff) {
            $booking = Trn_Consultation_Booking::with('patient','familyMember','branch','bookingStatus')
            ->where('doctor_id',$staff->staff_id)
            ->where('booking_type_id',84)
            ->whereIn('booking_status_id',[89,90])
            ->orderBy('created_at','DESC')
            ->get();
            }
        }else{
            $booking = Trn_Consultation_Booking::with('patient','familyMember','branch','bookingStatus')
            ->where('booking_type_id',84)
            ->whereIn('booking_status_id',[89,90])
            ->orderBy('created_at','DESC')
            ->get(); //confirmed bookings only.
        }
        return view('doctor.consultation.consultation-history', [
            'bookings' => $booking,
            'pageTitle' => 'Consultation History'
        ]);
    }
    public function viewConsultation($id, Request $request)
    {
        //dd(1);
        $history=Trn_Prescription::with('Staff','BookingDetails','BookingDetails.bookingStatus','BookingDetails.timeSlot','BookingDetails.therapyBookings','PrescriptionDetails','PrescriptionDetails.medicine')->where('Booking_id', $id)->orderBy('created_at','DESC')->first();
        //dd($patient_histories);
        return view('doctor.consultation.view-consultation', [
            'pageTitle' => 'View Consultation',
            'history'=>$history
        ]);
        
    }

}
