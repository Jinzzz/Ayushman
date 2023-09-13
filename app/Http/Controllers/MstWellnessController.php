<?php

namespace App\Http\Controllers;

use App\Models\Mst_Branch;
use App\Models\Mst_Wellness;
use Illuminate\Http\Request;

class MstWellnessController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = "Wellness";
        $query = Mst_Wellness::query();

        // Apply filters if provided
        if ($request->has('wellness_name')) {
            $query->where('wellness_name', 'LIKE', "%{$request->wellness_name}%");
        }
    
    
        if ($request->filled('branch_id')) {
            $query->whereHas('branch', function ($q) use ($request) {
                $q->where('branch_name', 'like', '%' . $request->input('branch_id') . '%');
            });
        }
    
        $wellness = $query->orderBy('updated_at', 'desc')->get();
        return view('wellness.index',compact('pageTitle','wellness'));
    }

    public function create()
    {
        $pageTitle = "Create Wellness";
        $branch = Mst_Branch::pluck('branch_name','branch_id');
        return view('wellness.create',compact('pageTitle','branch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'wellness_name' => 'required',
            'wellness_description' => 'required',
            'wellness_inclusions' => 'required',
            'wellness_terms_conditions' => 'required',
            'branch' => 'required',
            'wellness_cost' => 'required|numeric',
            'wellness_duration' => 'required',
            'is_active' => 'required',
        ]);
        $is_active = $request->input('is_active') ? 1 : 0;
    
       
        $wellness = new Mst_Wellness();
        $wellness->wellness_name = $request->input('wellness_name');
        $wellness->wellness_description = $request->input('wellness_description');
        $wellness->wellness_inclusions = $request->input('wellness_inclusions');
        $wellness->wellness_terms_conditions = $request->input('wellness_terms_conditions');
        $wellness->branch_id = $request->input('branch');
        $wellness->wellness_cost = $request->input('wellness_cost');
        $wellness->wellness_duration = $request->input('wellness_duration');
        $wellness->remarks = $request->input('remarks');
        $wellness->is_active = $is_active;
        $wellness->save();
    
        return redirect()->route('wellness.index')->with('success','Wellness added successfully');
    }

    public function edit($wellness_id)
    {
        $pageTitle = "Edit Wellness";
        $wellness = Mst_Wellness::findOrFail($wellness_id);
        $branch = Mst_Branch::pluck('branch_name','branch_id');
        return view('wellness.edit',compact('pageTitle','wellness','branch'));
    }

    public function update(Request $request,$wellness_id)
    {
        $request->validate([
            'wellness_name' => 'required',
            'wellness_description' => 'required',
            'wellness_inclusions' => 'required',
            'wellness_terms_conditions' => 'required',
            'branch' => 'required',
            'wellness_cost' => 'required|numeric',
            'wellness_duration' => 'required',
           
        ]);
        $is_active = $request->input('is_active') ? 1 : 0;
    
       
        $wellness = Mst_Wellness::findOrFail($wellness_id);
        $wellness->wellness_name = $request->input('wellness_name');
        $wellness->wellness_description = $request->input('wellness_description');
        $wellness->wellness_inclusions = $request->input('wellness_inclusions');
        $wellness->wellness_terms_conditions = $request->input('wellness_terms_conditions');
        $wellness->branch_id = $request->input('branch');
        $wellness->wellness_cost = $request->input('wellness_cost');
        $wellness->wellness_duration = $request->input('wellness_duration');
        $wellness->remarks = $request->input('remarks');
        $wellness->is_active = $is_active;
        $wellness->save();
    
        return redirect()->route('wellness.index')->with('success','Wellness updated successfully');
    }

    public function show($id)
    {
        $pageTitle = "View wellness details";
        $show = Mst_Wellness::findOrFail($id);
        return view('wellness.show',compact('pageTitle','show'));
    }

    public function destroy($wellness_id)
    {
        $wellness = Mst_Wellness::findOrFail($wellness_id);
        $wellness->delete();

        return redirect()->route('wellness.index')->with('success','Wellness deleted successfully');
    }


    public function changeStatus(Request $request, $wellness_id)
    {
        $wellness = Mst_Wellness::findOrFail($wellness_id);

        $wellness->is_active = !$wellness->is_active;
        $wellness->save();

        return redirect()->back()->with('success','Status changed successfully');
    }
}
