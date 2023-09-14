<?php

namespace App\Http\Controllers;

use App\Models\Mst_Branch;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Therapy_Room;
use Illuminate\Http\Request;

class MstTherapyRoomController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = "Therapy Rooms";
        // $therapyrooms = Mst_Therapy_Room::with('roomType','branch');
        $branch = Mst_Branch::pluck('branch_name', 'branch_id');
        $query = Mst_Therapy_Room::query();

        if ($request->has('branch_id')) {
            $query->where('branch_id', 'LIKE', "%{$request->branch_id}%");
        }
        $therapyrooms = $query->with('roomType', 'branch')->orderBy('updated_at', 'desc')->get();
        return view('therapyrooms.index', compact('pageTitle', 'therapyrooms', 'branch'));
    }

    public function create()
    {
        $pageTitle = "Create Therapy Room";
        $branch = Mst_Branch::pluck('branch_name', 'branch_id');
        $roomtype = Mst_Master_Value::where('master_id', 10)->pluck('master_value', 'id');
        return view('therapyrooms.create', compact('pageTitle', 'branch', 'roomtype'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch' => 'required',
            'room_name' => 'required',
            'is_active' => 'required',
        ]);

        $is_active = Mst_Therapy_Room::where('room_name', $request->input('room_name'))->where('branch_id', $request->input('branch'))->first();
        if ($is_active) {
            return redirect()->route('therapyrooms.index')->with('error', 'This therapy room already exists.');
        } else {
            $is_active = $request->input('is_active') ? 1 : 0;


            $therapyroom = new Mst_Therapy_Room();
            $therapyroom->branch_id = $request->input('branch');
            $therapyroom->room_name = $request->input('room_name');
            $therapyroom->room_type = 1;
            $therapyroom->room_capacity = 1;
            $therapyroom->is_active = $is_active;
            $therapyroom->created_by = auth()->id();
            $therapyroom->save();

            return redirect()->route('therapyrooms.index')->with('success', 'Therapy room added successfully');
        }
    }

    public function edit($id)
    {
        $pageTitle = "Edit Therapy Room";
        $branch = Mst_Branch::pluck('branch_name', 'branch_id');
        $roomtype = Mst_Master_Value::where('master_id', 10)->pluck('master_value', 'id');
        $therapyroom = Mst_Therapy_Room::findOrFail($id);
        return view('therapyrooms.edit', compact('pageTitle', 'branch', 'therapyroom', 'roomtype'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id' => 'required',
            'room_name' => 'required',
        ]);

        // Check if a therapy room with the same name exists within the same branch, excluding the current room ID
        $is_exists = Mst_Therapy_Room::where('room_name', $request->input('room_name'))
            ->where('branch_id', $request->input('branch_id'))
            ->first();
// print_r($is_exists);die();
        if ($is_exists) {
            // Handle the case where a room with the same name and branch already exists
            // You can display an error message or merge the records if desired
            return redirect()->route('therapyrooms.index')->with('error', 'This therapy room name is already taken within the same branch.');
        } else {
            $is_active = $request->input('is_active') ? 1 : 0;

            $therapyroom = Mst_Therapy_Room::findOrFail($id);
            $therapyroom->branch_id = $request->input('branch_id');
            $therapyroom->room_name = $request->input('room_name');
            $therapyroom->room_type = 1;
            $therapyroom->room_capacity = 1;
            $therapyroom->is_active = $is_active;
            $therapyroom->save();

            return redirect()->route('therapyrooms.index')->with('success', 'Therapy room updated successfully');
        }
    }

    public function destroy($id)
    {
        $therapyroom =  Mst_Therapy_Room::findOrFail($id);
        $therapyroom->delete();

        return redirect()->route('therapyrooms.index')->with('success', 'Therapy room deleted successfully');
    }

    public function changeStatus(Request $request, $id)
    {
        $therapyroom = Mst_Therapy_Room::findOrFail($id);

        $therapyroom->is_active = !$therapyroom->is_active;
        $therapyroom->save();

        return redirect()->back()->with('success', 'Status changed successfully');
    }
}
