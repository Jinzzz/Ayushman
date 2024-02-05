<?php

namespace App\Http\Controllers;
use App\Models\Mst_Patient;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Master_Value;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class BookingSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $patients = Trn_Consultation_Booking::leftJoin('mst_patients', 'mst_patients.id', '=', 'trn_consultation_bookings.patient_id')
            ->leftJoin('mst_master_values', 'mst_master_values.id', '=', 'trn_consultation_bookings.booking_type_id');
    
        if ($request->filled('booking_reference_number')) {
            $patients->where('booking_reference_number', $request->input('booking_reference_number'));
        }
    
        if ($request->filled('booking_type_id')) {
            $patients->where('booking_type_id', $request->input('booking_type_id'));
        }
    
        $patients = $patients->get(); 
    
        $Bookingtypes = Mst_Master_Value::whereIn('id', [84, 85, 86])->get();
    
        $pageTitle = "Patient Search";
    
        return view('booking_search.index', compact('patients', 'pageTitle', 'Bookingtypes'));
    }
    
    
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
