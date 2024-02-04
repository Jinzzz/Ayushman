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

    
    public function create(Request $request)
    {
        return view('feedback.create', [
            'pageTitle' => 'New Feedback'
        ]);
    }

    
    public function saveFeedback(Request $request)
    {
        $request->validate([
            'booking_id' => 'required',
            'consultancy_rating' => 'required',
            'visit_rating' => 'required',
            'service_rating' => 'required',
            'appointment_rating' => 'required',
        ], [
            'booking_id.required' => 'Booking ID is required',
            'consultancy_rating.required' => 'Please Choose a rating',
            'visit_rating.required' => 'Please Choose a rating',
            'service_rating.required' => 'Please Choose a rating',
            'appointment_rating.required' => 'Please Choose a rating',
            
        ]);

        DB::beginTransaction();
        try {
          $consultancy_rating = $request->consultancy_rating;
          $visit_rating = $request->visit_rating;
          $service_rating = $request->service_rating;
          $appointment_rating = $request->appointment_rating;
          $total_ratings = 4; 
          $total_sum = $consultancy_rating + $visit_rating + $service_rating + $appointment_rating;
          $average_rating = ($total_sum/($total_ratings * 5)) * 5;

            $booking = Trn_Feedback::create([
                'booking_id' => $request->booking_id,
                'consultancy_rating' =>  $request->consultancy_rating,
                'visit_rating'    =>  $request->visit_rating,
                'service_rating'    =>  $request->service_rating,
                'appointment_rating'    =>  $request->appointment_rating,
                'average_rating'    =>  $average_rating,
                'feedback'          => $request->feedback,
                'is_active'         => 1,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('error', 'Something Went Wrong');
            return redirect()->back();
        }
        DB::commit();
        return redirect()->back()->with('success-message', 'Feedback Submitted successfully.');
    }



}
