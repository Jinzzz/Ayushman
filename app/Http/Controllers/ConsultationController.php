<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Staff;
use App\Models\Mst_Medicine;
use App\Models\Mst_Therapy;

class ConsultationController extends Controller
{
    
    public function ConsultIndex(Request $request)
    {
        $userType = Auth::user()->user_type_id;
        if($userType == 20) //a doctor
        {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            if ($staff) {
            $booking = Trn_Consultation_Booking::where('doctor_id',$staff)->where('booking_status_id',88)->orderBy('created_at','DESC')->get();
            }
        }else{
            $booking = Trn_Consultation_Booking::where('booking_status_id',88)->orderBy('created_at','DESC')->get(); //confirmed bookings only.
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
}
