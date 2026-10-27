<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BloodRequestResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DashboardController extends Controller
{
    public function index(DashboardService $service): JsonResponse
    {
        return response()->json(['data' => $service->overview()]);
    }

    public function getStatistics(DashboardService $service): JsonResponse
    {
        return response()->json(['data' => $service->statistics()]);
    }

    public function getRecentRequests(DashboardService $service): AnonymousResourceCollection
    {
        return BloodRequestResource::collection($service->recentRequests());
    }
}
