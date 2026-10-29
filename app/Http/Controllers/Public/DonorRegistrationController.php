<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BloodDonor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonorRegistrationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $this->validateBloodDonor($request);
        $data['status'] = 0;

        BloodDonor::register($data);

        return response()->json(['message' => 'Registration submitted. An admin will review your profile.'], 201);
    }
}
