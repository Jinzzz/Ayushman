<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Mst_Supplier;
use App\Models\Mst_Branch;

class MedicinePurchaseController extends Controller
{
    public function index(Request $request)
    {
        try {
            $pageTitle = "Medicine Purchase";
            // $account_ledgers = Mst_Account_Ledger::join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', 'mst_account_sub_head.id')
            // ->select('mst__account__ledgers.id', 'mst__account__ledgers.ledger_name', 'mst__account__ledgers.ledger_name', 'mst__account__ledgers.ledger_code', 'mst__account__ledgers.is_active', 'mst_account_sub_head.account_sub_group_name')
            // ->orderBy('mst__account__ledgers.created_at', 'desc')
            // ->get();medicine.purchase
        
                
            return view('medicine_purchase.index', compact('pageTitle'));
        } catch (QueryException $e) {
            return redirect()->route('account.ledger.index')->with('error', 'Something went wrong');
        }
    }

    public function create(Request $request)
    {
        try {
            $pageTitle = "Create Medicine Purchase";
            $suppliers = Mst_Supplier::where('is_active',1)->get();
            $branches = Mst_Branch::where('is_active',1)->get();
            return view('medicine_purchase.create', compact('pageTitle','suppliers','branches'));
        } catch (QueryException $e) {
            return redirect()->route('account.ledger.index')->with('error', 'Something went wrong');
        }
    }
}
