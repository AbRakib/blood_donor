<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => SystemSetting::query()->orderBy('key')->get()]);
    }

    public function update(Request $request, SystemSetting $systemSetting): JsonResponse
    {
        $data = $request->validate(['value' => ['nullable', 'string', 'max:5000']]);
        DB::transaction(fn () => $systemSetting->update($data));

        return response()->json(['data' => $systemSetting]);
    }
}
