<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mst_Patient;
use App\Models\Mst_Membership_Package;
use App\Models\Mst_Membership_Benefit;

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
            
                    // $joined = isset($membership_package_id) && $membership->membership_package_id === $membership_package_id
                    //     ? 1
                    //     : 0;
            
                    $membership_packages[] = [
                        'membership_package_id' => $membership->membership_package_id,
                        'package_title' => $membership->package_title,
                        'package_duration' => $membership->package_duration." days",
                        'package_price' => $membership->package_price,
                        // 'joined' => $joined,
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
}
