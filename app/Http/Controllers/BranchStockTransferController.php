<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Trn_branch_stock_transfer;
use App\Models\Mst_Pharmacy;
use App\Models\Mst_Medicine;
use App\Models\Trn_Medicine_Stock;

class BranchStockTransferController extends Controller
{
    public function index(Request $request)
    {
        return view('branch-stock-transfer.index', [
            'processDatas' => Trn_branch_stock_transfer::orderBy('created_at','DESC')->get(),
            'pageTitle' => 'Stock Transfer to Pharmacies'
        ]);
    }

    public function create(Request $request)
    {
        return view('branch-stock-transfer.create', [
            'pharmacies' => Mst_Pharmacy::where('status',1)->orderBy('created_at','DESC')->get(),
            'medicines' => Mst_Medicine::where('is_active',1)->orderBy('created_at','DESC')->get(),
            'pageTitle' => 'Add Stock Transfer to Pharmacy'
        ]);
    }

    public function getBatchDetails(Request $request)
    {
        $medicineId = $request->input('medicine_id');
        $pharmacyId = $request->input('pharmacy_id');
        $stockDetails = Trn_Medicine_Stock::where('medicine_id', $medicineId)
            ->where('pharmacy_id', $pharmacyId)
            ->get(['stock_id', 'batch_no', 'mfd', 'expd', 'current_stock']);
        return response()->json($stockDetails);
    }
}
