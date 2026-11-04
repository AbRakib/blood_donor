<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BloodRequestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $this->validateBloodRequest($request);
        $data['expires_at'] = Carbon::parse($data['needed_at'])->endOfDay();

        BloodRequest::submit($data);

        return response()->json(['message' => 'Your blood request has been submitted for admin review.'], 201);
    }
}
