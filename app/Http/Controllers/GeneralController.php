<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Branch;

class GeneralController extends Controller
{
    public function generalIndex(Request $request)
    {
        $branch = Mst_Branch::where('branch_code','=',$request->branch_code)->select('branch_id','branch_name','branch_code','branch_address','branch_contact_number')->first();

        if($branch)
        {
            $consultation = Trn_Consultation_Booking::where('branch_id','=',$branch->branch_id)
            ->where('booking_type_id','=',84) //consultation from master values table
            ->whereDate('created_at', '=', Carbon::now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

            $wellness = Trn_Consultation_Booking::where('branch_id','=',$branch->branch_id)
            ->where('booking_type_id','=',85) //wellness from master values table
            ->whereDate('created_at', '=', Carbon::now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

            $therapy = Trn_Consultation_Booking::where('branch_id','=',$branch->branch_id)
            ->where('booking_type_id','=',86) //therapy from master values table
            ->whereDate('created_at', '=', Carbon::now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();
        }

        return view('general.index', [
            'branch' => $branch,
            'pageTitle' => 'Ayushman - General'
        ]);
    }
}
