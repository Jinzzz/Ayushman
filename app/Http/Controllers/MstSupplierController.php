<?php

namespace App\Http\Controllers;
use App\Models\Mst_Supplier;
use Illuminate\Http\Request;

class MstSupplierController extends Controller
{
    public function index()
    {
        $pageTitle = "Suppliers";
        $suppliers = Mst_Supplier::latest('id')->get();
        return view('supplier.index',compact('pageTitle','suppliers'));
    }

    public function create()
    {
        return view('supplier.create');
    }

    public function store(Request $request)
    {
        $request->validate([
         'supplier_name'=> 'required',
         'supplier_contact'=> 'required|digits:10|numeric',
         'supplier_email'=> 'required|email',
         'supplier_address'=> 'required',
         'gstno'=>  'required',
         'remarks'=> 'required',
         'is_active' => 'required',
        ]);
        $is_active = $request->input('is_active') ? 1 : 0;

        $lastInsertedId = Mst_Supplier::insertGetId([
           
            'supplier_code' => rand(50, 100),
            'supplier_name' => $request->supplier_name,
            'supplier_contact' => $request->supplier_contact,
            'supplier_email' => $request->supplier_email,
            'supplier_address' => $request->supplier_address,
            'gstno' => $request->gstno,
            'remarks' => $request->remarks,
            'is_active' =>  $is_active ,
        ]);

        $leadingZeros = str_pad('', 3 - strlen($lastInsertedId), '0', STR_PAD_LEFT);
        $supplierCode = 'SUP' . $leadingZeros . $lastInsertedId;
    
        Mst_Supplier::where('id', $lastInsertedId)->update([
            'supplier_code' => $supplierCode
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier added successfully');
    }


    public function edit($id)
    {
        $pageTitle = "Edit Supplier";
        $supplier = Mst_Supplier::findOrFail($id);
        return view('supplier.edit',compact('pageTitle','supplier'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
         'supplier_name'=> 'required',
         'supplier_contact'=> 'required|digits:10|numeric',
         'supplier_email'=> 'required|email',
         'supplier_address'=> 'required',
         'gstno'=>  'required',
         'remarks'=> 'required',

        ]);
        $is_active = $request->input('is_active') ? 1 : 0;
    
        $update = Mst_Supplier::find($id);
        $update->update([
            'supplier_name' => $request->supplier_name,
            'supplier_contact' => $request->supplier_contact,
            'supplier_email' => $request->supplier_email,
            'supplier_address' => $request->supplier_address,
            'gstno' => $request->gstno,
            'remarks' => $request->remarks,
            'is_active' =>  $is_active ,
        ]);
       
        return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully');
    }
    public function destroy($id)
    {
        $supplier = Mst_Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier deleted successfully');
    }

    public function changeStatus($id)
    {
        $supplier = Mst_Supplier::findOrFail($id);
        $supplier->is_active = !$supplier->is_active;
        $supplier->save();
    
        return redirect()->back()->with('success', 'Status changed successfully');
    }
} 
