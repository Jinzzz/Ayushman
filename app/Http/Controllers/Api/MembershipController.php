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

class MembershipController extends Controller
{
    public function membershipPackages(){
        $data=array();
        try{
            $patient_id = Auth::id();
            $accountHolder = Mst_Patient::find($patient_id);
            
            if ($accountHolder->available_membership !== 0) {
                $membership_package_id = $accountHolder->available_membership;
            }
            
            $memberships = Mst_Membership_Package::where('is_active', 1)->get();
            $membership_packages = [];
            
            if ($memberships->isNotEmpty()) {
                foreach ($memberships as $membership) {
                    $benefits = Mst_Membership_Benefit::where('package_id', $membership->membership_package_id)
                        ->where('is_active', 1)
                        ->pluck('title');
            
                    $is_joined = isset($membership_package_id) && $membership->membership_package_id === $membership_package_id
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
            $membership_package_id = Mst_Patient::where('id',Auth::id())->value('available_membership');

                if($membership_package_id != 0){
                    $package_details= Mst_Membership_Package::where('membership_package_id', $membership_package_id)->where('is_active', 1)->first();
                    
                    $membership_package_details[] = [
                        'membership_package_id' => $package_details->membership_package_id,
                        'package_title' => $package_details->package_title,
                        'package_duration' => $package_details->package_duration." days",
                        'package_price' => $package_details->package_price,
                        'package_description' => $package_details->package_description,
                    ];

                    $benefits = Mst_Membership_Benefit::where('package_id', $membership_package_id)->where('is_active', 1)->pluck('title');
                    
                    $membership__package__wellnesses = Mst_Membership_Package_Wellness::join('mst_wellness', 'mst__membership__package__wellnesses.wellness_id', '=', 'mst_wellness.id')
                                ->where('mst__membership__package__wellnesses.package_id', $membership_package_id)
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
