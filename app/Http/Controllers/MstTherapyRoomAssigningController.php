<?php

namespace App\Http\Controllers;

use App\Models\Mst_Branch;
use App\Models\Mst_Staff;
use App\Models\Mst_Therapy_Room;
use App\Models\Mst_Therapy_Room_Assigning;
use Illuminate\Http\Request;

class MstTherapyRoomAssigningController extends Controller
{
    public function index()
    {
        $pageTitle = "Therapy Room Assigning";
        $roomAssigning = Mst_Therapy_Room_Assigning::with('therapyroomName','branch','staff')->latest()->get();
        return view('therapyroomAssigning.index',compact('pageTitle','roomAssigning'));
    }

    public function create(Request $request)
    {
        $pageTitle = "Assign Therapy Room";
        $therapyroom = Mst_Therapy_Room::pluck('room_name','id');
        $branch = Mst_Branch::pluck('branch_name','branch_id');
        $staff = Mst_Staff::where('staff_type',19)->pluck('staff_name','staff_id');
      
        return view('therapyroomAssigning.create',compact('pageTitle','therapyroom','branch','staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'therapy_room' => 'required',
            'branch' => 'required',
            'staff' => 'required',
            'is_active' => 'required',
        ]);

        $is_active = $request->input('is_active') ? 1:0;

        $store = new Mst_Therapy_Room_Assigning();
        $store->therapy_room_id = $request->input('therapy_room');
        $store->branch_id = $request->input('branch');
        $store->staff_id = $request->input('staff');
        $store->is_active = $is_active;
        $store->save();
      
        return redirect()->route('therapyroomassigning.index')->with('success','Therapy Room Assigned Successfully');
    }

    public function edit($id)
    {
       
        $pageTitle = "Edit Assigned Room";
        
        $therapyroom = Mst_Therapy_Room::pluck('room_name','id');
        
        $branch = Mst_Branch::pluck('branch_name','branch_id');
        
        $staff = Mst_Staff::pluck('staff_name','staff_id');
        
        $roomassigning = Mst_Therapy_Room_Assigning::findOrFail($id);
     
        return view('therapyroomAssigning.edit',compact('pageTitle','therapyroom','branch','staff','roomassigning'));
    }

    public function update(Request $request , $id)
    {
        $is_active = $request->input('is_active')? 1 : 0;

        $update = Mst_Therapy_Room_Assigning::findOrFail($id);
        $update->therapy_room_id = $request->input('therapy_room_id');
        $update->branch_id = $request->input('branch_id');
        $update->staff_id = $request->input('staff_id');
        $update->is_active = $is_active;
        $update->save();

        return redirect()->route('therapyroomassigning.index')->with('success','updated successfully');
    }

    public function destroy($id)
    {
        $destroy = Mst_Therapy_Room_Assigning::findOrFail($id);
        $destroy->delete();

        return redirect()->route('therapyroomassigning.index')->with('success','deleted successfully');
    }

    public function changeStatus($id)
    {
       $status =  Mst_Therapy_Room_Assigning::findOrFail($id);

       $status->is_active = !$status->is_active;
       $status->save();

       return redirect()->back()->with('success','Status changed successfully'); 
    }

    public function getTherapyRooms($branchId)
    {
        $therapyRooms = Mst_Therapy_Room::where('branch_id', $branchId)->pluck('room_name', 'id');
    
        return response()->json($therapyRooms);
    }
    

}
