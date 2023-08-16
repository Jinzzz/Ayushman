<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helpers\PatientHelper;
use App\Models\Mst_Patient;
use App\Models\Mst_Membership_Package;
use App\Models\Mst_Membership_Benefit;
use App\Models\Mst_Membership_Package_Wellness;
use App\Models\Mst_Patient_Membership_Booking;
use App\Models\Trn_Patient_Wellness_Sessions;
use App\Models\Mst_Wellness;

class MembershipController extends Controller
{
    public function membershipPackages(){
        $data=array();
        try{
            $patient_id = Auth::id();
            $accountHolder = Mst_Patient::find($patient_id);
            $joined_membership_package_id = "";
            if ($accountHolder->available_membership !== 0) {
                $membership_details = Mst_Patient_Membership_Booking::where('patient_id', $patient_id)
                ->latest()
                ->first();

                $joined_membership_package_id = $membership_details->membership_package_id;
            }
            
            $memberships = Mst_Membership_Package::where('is_active', 1)->get();
            $membership_packages = [];
            
            if ($memberships->isNotEmpty()) {
                foreach ($memberships as $membership) {
                    $benefits = Mst_Membership_Benefit::where('package_id', $membership->membership_package_id)
                        ->where('is_active', 1)
                        ->pluck('title');
            
                    $is_joined = isset($joined_membership_package_id) && $membership->membership_package_id === $joined_membership_package_id
                        ? 1
                        : 0;
            
                    $membership_packages[] = [
                        'membership_package_id' => $membership->membership_package_id,
                        'package_title' => $membership->package_title,
                        'package_duration' => $membership->package_duration." days",
                        'package_price' => $membership->package_price,
                        'is_joined' => $is_joined,
                        'benefits' => $benefits,
                    ];
                }
            
                $data['status'] = 1;
                $data['message'] = "Data Fetched";
                $data['data'] = $membership_packages;
            } else {
                $data['status'] = 0;
                $data['message'] = "Currently, no memberships are available";
            }
            
            return response($data);
            
        }
        catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }

    public function membershipPackageDetails(Request $request){
        $data=array();
        try
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'membership_package_id' => ['required'],
                ],
                [
                    'membership_package_id.required' => 'Membership package required',
                ]
            );

            if (!$validator->fails()) 
            {
                if(isset($request->membership_package_id)){
                    $package_details= Mst_Membership_Package::where('membership_package_id', $request->membership_package_id)->where('is_active', 1)->first();
                   
                    $membership_package_details[] = [
                        'membership_package_id' => $package_details->membership_package_id,
                        'package_title' => $package_details->package_title,
                        'package_duration' => $package_details->package_duration." days",
                        'package_price' => $package_details->package_price,
                        'package_description' => $package_details->package_description,
                    ];
                    $benefits = Mst_Membership_Benefit::where('package_id', $request->membership_package_id)->where('is_active', 1)->pluck('title');
                    
                    $membership__package__wellnesses = Mst_Membership_Package_Wellness::join('mst_wellness', 'mst__membership__package__wellnesses.wellness_id', '=', 'mst_wellness.id')
                                ->where('mst__membership__package__wellnesses.package_id', $request->membership_package_id)
                                ->where('mst__membership__package__wellnesses.is_active', 1)
                                ->select('mst__membership__package__wellnesses.package_wellness_id', 'mst__membership__package__wellnesses.maximum_usage_limit', 'mst_wellness.id as wellness_id', 'mst_wellness.wellness_name','mst_wellness.wellness_inclusions','mst_wellness.wellness_terms_conditions')
                                ->get();


                    $data['status'] = 1;
                    $data['message'] = "Data Fetched";
                    $data['package_details'] = $membership_package_details;
                    $data['package__wellnesses'] = $membership__package__wellnesses;
                    $data['package_benefits'] = $benefits;
                    return response()->json($data);
                }
                else{
                    $data['status'] = 0;
                    $data['message'] = "Please fill mandatory fields";
                    return response()->json($data);
                }
            }
            else{
                $data['status'] = 0;
                $data['errors'] = $validator->errors();
                $data['message'] = "Validation errors";
                return response($data);
            }
        }
        catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
            
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }

    public function currentMembershipDetails(Request $request){
        $data=array();
        try
        {
            $data = [];
            $membership_package_id = Mst_Patient::where('id', Auth::id())->value('available_membership');
    
            if ($membership_package_id != 0) {
                $package_details = Mst_Membership_Package::where('membership_package_id', $membership_package_id)
                    ->where('is_active', 1)
                    ->first();
    
                $latest_membership_booking = Mst_Patient_Membership_Booking::where('membership_package_id', $membership_package_id)
                    ->latest()
                    ->first();
    
                $targetDate = Carbon::parse($latest_membership_booking->membership_expiry_date);
                $membership_booking_date = $latest_membership_booking->created_at->format('d-m-Y');
                $membership_expiry_date = $targetDate->format('d-m-Y');
                $days_left = Carbon::now()->diffInDays($targetDate) . " days";
    
                $patient_membership_details = [
                    'package_title' => $package_details->package_title,
                    'membership_booking_date' => $membership_booking_date,
                    'membership_expiry_date' => $membership_expiry_date,
                    'days_left' => $days_left,
                ];
    
                $membership_wellnesses = Mst_Membership_Package_Wellness::where('mst__membership__package__wellnesses.package_id', $membership_package_id)
                    ->where('mst__membership__package__wellnesses.is_active', 1)
                    ->get(['wellness_id', 'maximum_usage_limit'])
                    ->toArray();
    
                $completedSessions = [];
                $remainingSessions = [];
    
                foreach ($membership_wellnesses as $membership_wellness) {
                    $countUsed = Trn_Patient_Wellness_Sessions::where('membership_patient_id', $latest_membership_booking->membership_patient_id)
                        ->where('wellness_id', $membership_wellness['wellness_id'])
                        ->count();
    
                    $wellness = Mst_Wellness::find($membership_wellness['wellness_id']);
                    $wellnessName = $wellness->wellness_name;
    
                    if ($countUsed < $membership_wellness['maximum_usage_limit']) {
                        $remainingSessions[] = $wellnessName;
                    } else {
                        $completedSessions[] = $wellnessName;
                    }
                }

                if ($patient_membership_details !== null && $completedSessions !== null && $remainingSessions !== null) {
                    $data = [
                        'status' => 1,
                        'message' => "Data Fetched",
                        'patient_membership_details' => $patient_membership_details,
                        'completed_sessions' => $completedSessions,
                        'remaining_sessions' => $remainingSessions,
                    ];
                    return response()->json($data);
                } else {
                    $data['status'] = 0;
                    $data['message'] = "Data Incomplete";
                    return response()->json($data);
                }
    
               
            } else {
                $data['status'] = 0;
                $data['message'] = "Currently not a member of any membership packages";
                return response()->json($data);
            }
    
        }
        catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
            
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }
    }
}
