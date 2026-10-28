<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BloodDonor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonorRegistrationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $this->validateBloodDonor($request);
        $data['status'] = 0;

        DB::transaction(function () use ($data): BloodDonor {
            $donor = new BloodDonor;
            $donor->name = $data['name'];
            $donor->blood_group_id = $data['blood_group_id'];
            $donor->phone = $data['phone'];
            $donor->area = $data['area'] ?? null;
            $donor->address = $data['address'] ?? null;
            $donor->is_available = $data['is_available'] ?? true;
            $donor->district_id = $data['district_id'] ?? null;
            $donor->upazila_id = $data['upazila_id'] ?? null;
            $donor->union_id = $data['union_id'] ?? null;
            $donor->date_of_birth = $data['date_of_birth'] ?? null;
            $donor->gender = $data['gender'] ?? null;
            $donor->consent_to_contact = true;
            $donor->status = $data['status'];
            $donor->save();

            return $donor->refresh();
        });

        return response()->json(['message' => 'Registration submitted. An admin will review your profile.'], 201);
    }
}
