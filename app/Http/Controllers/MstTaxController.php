<?php

namespace App\Http\Controllers;
use App\Models\Mst_Tax;
use Illuminate\Http\Request;

class MstTaxController extends Controller
{
    public function index()
    {
        $pageTitle = "Taxes";
        $taxes = Mst_Tax::latest()->get();
        return view('tax.index',compact('pageTitle','taxes'));
    }

    public function create()
    {
        $pageTitle = "Create Tax";
        return view('tax.create',compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tax_title' => 'required',
            'split_value_1' => 'required',
            'split_value_2' => 'required',
            'is_active' => 'required',

        ]);
      
    $is_active = $request->input('is_active')? 1 :0;
       
        $taxes = new Mst_Tax();
        $taxes->tax_title = $request->input('tax_title');
        $taxes->split_value_1 = $request->input('split_value_1');
        $taxes->split_value_2 = $request->input('split_value_2');
        $taxes->is_active  = $is_active;
        $taxes->created_by = auth()->id();
        $taxes->save();
    
        return redirect()->route('tax.index')->with('success','Tax added successfully');
    }

    public function edit($id)
    {
        $pageTitle = "Edit Tax";
        $tax = Mst_Tax::findOrFail($id);
        return view('tax.edit',compact('pageTitle','tax'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'tax_title' => 'required',
            'split_value_1' => 'required',
            'split_value_2' => 'required',
        ]);
       
        $is_active = $request->input('is_active')? 1 :0;
       
        $taxes = Mst_Tax::findOrFail($id);
        $taxes->tax_title = $request->input('tax_title');
        $taxes->split_value_1 = $request->input('split_value_1');
        $taxes->split_value_2 = $request->input('split_value_2');
        $taxes->is_active  = $is_active;
        $taxes->save();
    
        return redirect()->route('tax.index')->with('success','Tax updated successfully');
    }

    public function destroy($id)
    {
        $taxes =  Mst_Tax::findOrFail($id);
        $taxes->delete();

        return redirect()->route('tax.index')->with('success','Tax deleted successfully');
    }

    
    public function changeStatus(Request $request, $id)
    {
    $taxes = Mst_Tax::findOrFail($id);

    $taxes->is_active = !$taxes->is_active;
    $taxes->save();

    return redirect()->back()->with('success','Status changed successfully');
    }

    

}
