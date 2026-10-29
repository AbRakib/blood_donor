<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BloodDonor;
use App\Models\DonorContactRequest;
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

    public function contact(Request $request): JsonResponse
    {
        $data = $request->validate([
            'requester_phone' => ['required', 'string', 'regex:/^(?:\+?88)?01[3-9][0-9]{8}$/'],
            'blood_donor_id' => ['required', 'integer', Rule::exists('blood_donors', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')->where('is_available', true)->where('consent_to_contact', true)],
            'requester_name' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        DonorContactRequest::submit($data);

        return response()->json(['message' => 'Your contact request has been submitted for admin review.'], 201);
    }
}
