<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function index(ReportService $service): JsonResponse
    {
        return response()->json(['data' => $service->summary()]);
    }
}
