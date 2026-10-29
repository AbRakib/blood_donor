<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodDonor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BloodDonorController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        if (! $request->expectsJson()) {
            $common_data = (object) [
                'page_title' => __('Blood donors'),
                'title' => __('Blood donors'),
            ];

            return view('admin.donors')->with(compact('common_data'));
        }

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:255'],
            'blood_group_id' => ['nullable', 'integer'],
            'availability' => ['nullable', 'in:available,busy'],
        ]);

        $query = BloodDonor::with('bloodGroup')->filteredForAdmin($filters);

        return $this->collectionResponse($query->orderByDesc('id')->paginate(20), $this->formatBloodDonor(...));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'blood_group_id' => ['required', 'integer', Rule::exists('blood_groups', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'district_id' => ['nullable', 'required_with:upazila_id', 'integer', Rule::exists('districts', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'upazila_id' => ['nullable', 'required_with:union_id', 'integer', Rule::exists('upazilas', 'id')->where('district_id', $request->input('district_id'))->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'union_id' => ['nullable', 'integer', Rule::exists('unions', 'id')->where('upazila_id', $request->input('upazila_id'))->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(?:\+?88)?01[3-9][0-9]{8}$/'],
            'area' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'date_of_birth' => ['nullable', 'date_format:Y-m-d', 'before:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'last_donation_date' => ['prohibited'],
            'total_donations' => ['prohibited'],
            'is_available' => ['sometimes', 'boolean'],
            'consent_to_contact' => ['required', 'accepted'],
        ]);

        $donor = BloodDonor::register($data);

        $donor->load('bloodGroup');

        return response()->json(['data' => $this->formatBloodDonor($donor)], 201);
    }

    public function show(int $bloodDonor): JsonResponse
    {
        $donor = BloodDonor::findOrFail($bloodDonor);
        $donor->load('bloodGroup');
        $data = $this->formatBloodDonor($donor);

        return response()->json(['data' => $data]);
    }

    public function update(Request $request, int $bloodDonor): JsonResponse
    {
        $donor = BloodDonor::findOrFail($bloodDonor);
        $input = array_merge($donor->only(['district_id', 'upazila_id', 'union_id']), $request->all());
        $data = Validator::make($input, [
            'blood_group_id' => ['sometimes', 'required', 'integer', Rule::exists('blood_groups', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'district_id' => ['sometimes', 'nullable', 'required_with:upazila_id', 'integer', Rule::exists('districts', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'upazila_id' => ['sometimes', 'nullable', 'required_with:union_id', 'integer', Rule::exists('upazilas', 'id')->where('district_id', $input['district_id'] ?? null)->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'union_id' => ['sometimes', 'nullable', 'integer', Rule::exists('unions', 'id')->where('upazila_id', $input['upazila_id'] ?? null)->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', 'regex:/^(?:\+?88)?01[3-9][0-9]{8}$/'],
            'area' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'date_of_birth' => ['sometimes', 'nullable', 'date_format:Y-m-d', 'before:today'],
            'gender' => ['sometimes', 'nullable', Rule::in(['male', 'female', 'other'])],
            'last_donation_date' => ['sometimes', 'prohibited'],
            'total_donations' => ['sometimes', 'prohibited'],
            'is_available' => ['sometimes', 'boolean'],
            'consent_to_contact' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'integer', 'in:0,1'],
        ])->validate();
        $data = array_intersect_key($data, $request->all());

        $donor = $donor->updateProfile($data);

        $donor->refresh();
        $donor->load('bloodGroup');
        $data = $this->formatBloodDonor($donor);

        return response()->json(['data' => $data]);
    }

    public function destroy(BloodDonor $bloodDonor): Response
    {
        $bloodDonor->deleteOrFail();

        return response()->noContent();
    }

    public function changeAvailability(Request $request, int $bloodDonor): JsonResponse
    {
        $data = $request->validate(['is_available' => ['required', 'boolean']]);
        $donor = BloodDonor::findOrFail($bloodDonor);

        $donor = $donor->updateProfile($data);

        $donor->refresh();
        $donor->load('bloodGroup');
        $data = $this->formatBloodDonor($donor);

        return response()->json(['data' => $data]);
    }
}
