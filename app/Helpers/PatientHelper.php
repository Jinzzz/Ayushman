<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use App\Models\Mst_Patient;
use Illuminate\Support\Facades\Hash;

class PatientHelper
{
    public static function store(Request $request)
    {
        $lastInsertedId = Mst_Patient::insertGetId([
            'patient_name'      => $request->patient_name,
            'patient_email'     => $request->patient_email,
            'patient_address'   => $request->patient_address,
            'patient_gender'    => $request->patient_gender,
            'patient_dob'       => $request->patient_dob,
            'username'          => $request->username,
            'patient_mobile'    => $request->patient_mobile,
            'password'          => Hash::make($request->password),
            'is_active'         => 1,
            'patient_code'         => rand(50, 100),
            ]);

            $leadingZeros = str_pad('', 3 - strlen($lastInsertedId), '0', STR_PAD_LEFT);
            $newPatientCode = 'PAT' . $leadingZeros . $lastInsertedId;

            Mst_Patient::where('id', $lastInsertedId)->update([
            'patient_code' => $newPatientCode
            ]);
        return 1;
    }
}
