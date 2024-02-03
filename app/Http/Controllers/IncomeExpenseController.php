<?php

namespace App\Http\Controllers;
use Illuminate\Database\QueryException;
use App\Models\Sys_Account_Group;
use App\Models\Mst_Account_Sub_Head;
use App\Models\Mst_Account_Ledger;
use App\Models\Trn_income_expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Mst_Master_Value;

class IncomeExpenseController extends Controller
{
    public function index(Request $request)
    {
        return view('income-expense.index', [
            'incexpdata' => Trn_income_expense::orderBy('created_at','DESC')->get(),
            'pageTitle' => 'Income-Expense'
        ]);
    }
    
    public function create(Request $request)
    {
        return view('income-expense.create', [
            'ledgerList' => Mst_Account_Ledger::orderBy('ledger_name','ASC')->get(),
            'payment_type' => Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id'),
            'pageTitle' => 'Add Miscellaneous Income-Expense'
        ]);
    }

}
