<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Trn_Feedback;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        return view('feedback.index', [
            'feedData' => Trn_Feedback::orderBy('created_at','DESC')->get(),
            'pageTitle' => 'Feedbacks'
        ]);
    }

    public function destroy($id)
  {
    try {
      $feedback = Trn_Feedback::findOrFail($id);
      $feedback->delete();
        return response()->json([
            'success' => true,
            'message' => 'Feedback deleted successfully',
        ]);
    } catch (QueryException $e) {
      return redirect()->route('customer.feedback.index')->with('error', 'Something went wrong.');
    }
  }

    public function changeStatus($id)
    {
      try {
        $feedback = Trn_Feedback::findOrFail($id);
  
        $feedback->is_active = !$feedback->is_active;
        $feedback->save();
  
         return 1;
         return redirect()->back()->with('success', 'Status changed successfully');
      } catch (QueryException $e) {
        return redirect()->route('customer.feedback.index')->with('error', 'Something went wrong.');
      }
    }


}
