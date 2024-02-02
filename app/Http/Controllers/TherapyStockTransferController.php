<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstStockTransferTherapy;
use App\Models\Mst_Therapy;
use App\Models\Mst_Medicine;
use App\Models\Trn_Medicine_Stock;
use Illuminate\Support\Facades\Validator;
use Auth;

class TherapyStockTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageTitle = "Therapy Stock Transfer";
    
        // Fetch all therapies and medicines
        $therapys = Mst_Therapy::where('is_active', 1)->get();
        $medicines = Mst_Medicine::where('is_active', 1)->get();
    
        // Initialize the stock query
        $stocks = MstStockTransferTherapy::select(
            'mst_stock_transfer_therapy.*', 
            'mst_therapies.therapy_name',      
            'mst_medicines.medicine_name',
            'trn_medicine_stocks.current_stock'
        )
        ->join('mst_therapies', 'mst_stock_transfer_therapy.therapy_id', '=', 'mst_therapies.id')
        ->join('mst_medicines', 'mst_stock_transfer_therapy.medicine_id', '=', 'mst_medicines.id')
        ->join('trn_medicine_stocks', function($join) {
            $join->on('mst_stock_transfer_therapy.medicine_id', '=', 'trn_medicine_stocks.medicine_id')
                ->on('mst_stock_transfer_therapy.batch_id', '=', 'trn_medicine_stocks.batch_no');
        })
        ->orderByDesc('mst_stock_transfer_therapy.updated_at');
    
        // Apply filters
        if ($request->filled('medicine_name')) {
            $stocks->where('mst_medicines.medicine_name', 'LIKE', "%{$request->medicine_name}%");
        }
        if ($request->filled('therapy_name')) {
            $stocks->where('mst_therapies.therapy_name', 'LIKE', "%{$request->therapy_name}%");
        }
    
        if ($request->filled('transfer_date')) {
            $stocks->whereDate('mst_stock_transfer_therapy.transfer_date', $request->transfer_date);
        }
    
        // Get the results
        $stocks = $stocks->get();
     
        return view('therapy-stock-transfers.index', compact('pageTitle', 'therapys', 'medicines', 'stocks'));
    }
    
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = "Therapy Stock Transfer";
        $therapys = Mst_Therapy::where('is_active', 1)->get();
        $medicines = Mst_Medicine::where('is_active', 1)->get();
        
        return view('therapy-stock-transfers.create', compact('pageTitle', 'therapys','medicines'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'therapy_id' => 'required',
            'medicine_id' => 'required',
            'batch_id' => 'required',
            'transfer_quantity' => 'required|numeric|min:1',
            'transfer_date' => 'required|date',
        ]);

        $userId = Auth::id();
        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if ($request->transfer_quantity >= $request->current_stock) {
            return redirect()->back()->with('error', 'Transfer quantity cannot be greater than or equal to the current stock')->withInput();
        }
        
        $transfer = new MstStockTransferTherapy([
            'therapy_id' => $request->therapy_id,
            'medicine_id' => $request->medicine_id,
            'batch_id' => $request->batch_id,
            'transfer_quantity' => $request->transfer_quantity,
            'transfer_date' => $request->transfer_date,
            'created_by'  => $userId,
        ]);
        $transfer->save();
            // Update current stock
        Trn_Medicine_Stock::where('medicine_id', $request->medicine_id)
                          ->where('batch_no', $request->batch_id)
                          ->update(['current_stock' => $request->current_stock - $request->transfer_quantity]);

        return redirect()->route('therapy-stock-transfers.index')->with('success', 'Therapy Stock Transfer added successfully');
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

    public function getMedicineBatch($id)
    {
        $batchNumbers = Trn_Medicine_Stock::where('medicine_id',$id)
                       ->pluck('batch_no');
        return response()->json(['batch_numbers' => $batchNumbers]);
    }

    public function getCurrentMedicineStock($medicineId, $batchNo)
    {
        $currentStock = Trn_Medicine_Stock::where('medicine_id', $medicineId)
                        ->where('batch_no', $batchNo)
                        ->value('current_stock');

        return response()->json(['current_stock' => $currentStock]);
   
    }
}
