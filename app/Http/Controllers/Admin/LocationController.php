<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Union;
use App\Models\Upazila;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate(['district_id' => ['nullable', 'integer', 'exists:districts,id'], 'upazila_id' => ['nullable', 'integer', 'exists:upazilas,id']]);
        if (! empty($data['upazila_id'])) {
            $query = Union::query()->active()->whereHas('upazila', fn ($query) => $query->active()->whereHas('district', fn ($query) => $query->active()))->where('upazila_id', $data['upazila_id']);
        } elseif (! empty($data['district_id'])) {
            $query = Upazila::query()->active()->whereHas('district', fn ($query) => $query->active())->where('district_id', $data['district_id']);
        } else {
            $query = District::query()->active();
        }

        return response()->json(['data' => $query->orderBy('name')->get(['id', 'name', 'name_bn'])]);
    }

    public function districts(): JsonResponse
    {
        return response()->json(['data' => District::query()->active()->orderBy('name')->get(['id', 'name', 'name_bn'])]);
    }

    public function upazilas(District $district): JsonResponse
    {
        abort_unless($district->status === 1, 404);

        return response()->json(['data' => $district->upazilas()->active()->orderBy('name')->get(['id', 'name', 'name_bn'])]);
    }

    public function unions(Upazila $upazila): JsonResponse
    {
        abort_unless($upazila->status === 1 && $upazila->district?->status === 1, 404);

        return response()->json(['data' => $upazila->unions()->active()->orderBy('name')->get(['id', 'name', 'name_bn'])]);
    }

    public function store(Request $request, string $type): JsonResponse
    {
        $modelClass = match ($type) {
            'districts' => District::class, 'upazilas' => Upazila::class, 'unions' => Union::class,     default => abort(404)
        };
        $rules = ['name' => ['required', 'string', 'max:255'], 'name_bn' => ['nullable', 'string', 'max:255']];
        if ($type === 'districts') {
            $rules['code'] = ['nullable', 'string', 'max:20', Rule::unique('districts', 'code')];
        } else {
            $parent = $type === 'upazilas' ? 'district' : 'upazila';
            $table = $type === 'upazilas' ? 'districts' : 'upazilas';
            $rules[$parent.'_id'] = ['required', 'integer', Rule::exists($table, 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')];
            $rules['name'][] = Rule::unique($type, 'name')->where($parent.'_id', $request->input($parent.'_id'));
        }
        $data = $request->validate($rules);
        $location = $modelClass::query()->create($data);

        return response()->json(['data' => $location], 201);
    }
}
