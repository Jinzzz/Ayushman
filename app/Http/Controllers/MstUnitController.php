<?php

namespace App\Http\Controllers;

use App\Models\Mst_Unit;
use Illuminate\Http\Request;

class MstUnitController extends Controller
{
    public function index()
    {
        $pageTitle = "Units";
        $units = Mst_Unit::latest()->get();
        return view('unit.index', compact('pageTitle', 'units'));
    }

    public function create()
    {
        $pageTitle = "Create Unit";
        return view('unit.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_name' => 'required',
            'is_active' => 'required',
        ]);

        $is_exists = Mst_Unit::where('unit_name', $request->input('unit_name'))->first();

        if ($is_exists) {
            return redirect()->route('unit.index')->with('error', 'This unit is already exists.');
        } else {
            $is_active = $request->input('is_active') ? 1 : 0;
            $units = new Mst_Unit();
            $units->unit_name = $request->input('unit_name');
            $units->unit_short_name = $request->input('unit_short_name');
            $units->is_active = $is_active;
            $units->save();
            return redirect()->route('unit.index')->with('success', 'Unit added successfully');
        }
    }

    public function edit($id)
    {
        $pageTitle = "Edit Unit";
        $units = Mst_Unit::findOrFail($id);
        return view('unit.edit', compact('pageTitle', 'units'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'unit_name' => 'required',

        ]);
        $is_exists = Mst_Unit::where('unit_name', $request->input('unit_name'))->first();
        if ($is_exists) {
            return redirect()->route('unit.index')->with('error', 'This unit is already exists.');
        } else {
            
        $is_active = $request->input('is_active') ? 1 : 0;
        $units =  Mst_Unit::findOrFail($id);
        $units->unit_name = $request->input('unit_name');
        $units->is_active = $is_active;
        $units->save();

        return redirect()->route('unit.index')->with('success', 'Unit updated successfully');
        }
    }

    public function destroy($id)
    {
        $units =  Mst_Unit::findOrFail($id);
        $units->delete();

        return redirect()->route('unit.index')->with('success', 'Unit deleted successfully');
    }

    public function changeStatus(Request $request, $id)
    {
        $units = Mst_Unit::findOrFail($id);

        $units->is_active = !$units->is_active;
        $units->save();

        return redirect()->back()->with('success', 'Status changed successfully');
    }
}
