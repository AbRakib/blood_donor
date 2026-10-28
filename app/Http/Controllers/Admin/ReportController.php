<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $service): JsonResponse|View
    {
        if (! $request->expectsJson()) {
            $common_data = (object) [
                'page_title' => __('Reports'),
                'title' => __('Reports'),
            ];

            return view('admin.reports')->with(compact('common_data'));
        }

        return response()->json(['data' => $service->summary()]);
    }
}
