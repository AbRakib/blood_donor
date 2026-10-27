<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BloodGroupController extends Controller
{
    public function index(): JsonResponse
    {
        $query = BloodGroup::query()->active()->orderBy('sort_order');
        $columns = ['id', 'name', 'sort_order'];

        if (Gate::allows('admin')) {
            $query->with('creator:id,name');
            $columns = [...$columns, 'created_at', 'created_by'];
        }

        return response()->json(['data' => $query->get($columns)]);
    }

    public function update(Request $request, BloodGroup $bloodGroup): JsonResponse
    {
        $data = $request->validate(['status' => ['sometimes', 'integer', 'in:0,1'], 'sort_order' => ['sometimes', 'integer', 'min:0', 'max:255']]);
        DB::transaction(fn () => $bloodGroup->update($data));

        return response()->json(['data' => $bloodGroup]);
    }
}
