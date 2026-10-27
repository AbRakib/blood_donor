<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BloodGroupController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => BloodGroup::query()->active()->orderBy('sort_order')->get(['id', 'name', 'sort_order'])]);
    }

    public function update(Request $request, BloodGroup $bloodGroup): JsonResponse
    {
        $data = $request->validate(['status' => ['sometimes', 'integer', 'in:0,1'], 'sort_order' => ['sometimes', 'integer', 'min:0', 'max:255']]);
        DB::transaction(fn () => $bloodGroup->update($data));

        return response()->json(['data' => $bloodGroup]);
    }
}
