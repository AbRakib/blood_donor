<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBloodDonorRequest;
use App\Services\DonorService;
use Illuminate\Http\JsonResponse;

class DonorRegistrationController extends Controller
{
    public function store(StoreBloodDonorRequest $request, DonorService $donors): JsonResponse
    {
        $data = $request->validated();
        $data['status'] = 0;
        $donors->create($data);

        return response()->json(['message' => 'Registration submitted. An admin will review your profile.'], 201);
    }
}
