<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BloodDonor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonorSearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'blood_group_id' => ['nullable', 'integer', Rule::exists('blood_groups', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'district_id' => ['nullable', 'required_with:upazila_id', 'integer', Rule::exists('districts', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'upazila_id' => ['nullable', 'required_with:union_id', 'integer', Rule::exists('upazilas', 'id')->where('district_id', $request->input('district_id'))->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'union_id' => ['nullable', 'integer', Rule::exists('unions', 'id')->where('upazila_id', $request->input('upazila_id'))->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'q' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        return $this->collectionResponse(BloodDonor::query()->matchingFilters($filters)->availableForContact()->with(['bloodGroup', 'district', 'upazila', 'union'])->orderBy('id')->paginate(12), $this->formatPublicDonor(...));
    }
}
