<?php

namespace App\Http\Controllers;

use App\Models\BloodDonation;
use App\Models\BloodDonor;
use App\Models\BloodRequest;
use App\Models\DonorContactRequest;
use App\Models\RequestDonor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

abstract class Controller
{
    protected function modelResponse(Model $model, callable $format): JsonResponse
    {
        return response()->json(['data' => $format($model)], $model->wasRecentlyCreated ? 201 : 200);
    }

    protected function collectionResponse(Collection|LengthAwarePaginator $items, callable $format): JsonResponse
    {
        if ($items instanceof LengthAwarePaginator) {
            $pagination = $items->through($format)->toArray();

            return response()->json([
                'data' => $pagination['data'],
                'links' => [
                    'first' => $pagination['first_page_url'],
                    'last' => $pagination['last_page_url'],
                    'prev' => $pagination['prev_page_url'],
                    'next' => $pagination['next_page_url'],
                ],
                'meta' => Arr::except($pagination, ['data', 'first_page_url', 'last_page_url', 'prev_page_url', 'next_page_url']),
            ]);
        }

        return response()->json(['data' => $items->map($format)->all()]);
    }

    /** @return array<string, mixed> */
    protected function formatBloodDonor(?BloodDonor $bloodDonor): array
    {
        if ($bloodDonor === null) {
            return [];
        }

        $data = [
            'id' => $bloodDonor->id,
            'name' => $bloodDonor->name,
            'blood_group_id' => $bloodDonor->blood_group_id,
            'phone' => $bloodDonor->consent_to_contact && request()->user()?->is_admin ? $bloodDonor->phone : null,
            'area' => $bloodDonor->area,
            'address' => $bloodDonor->address,
            'district_id' => $bloodDonor->district_id,
            'upazila_id' => $bloodDonor->upazila_id,
            'union_id' => $bloodDonor->union_id,
            'date_of_birth' => $bloodDonor->date_of_birth,
            'gender' => $bloodDonor->gender,
            'total_donations' => $bloodDonor->total_donations,
            'last_donation_date' => $bloodDonor->last_donation_date,
            'is_available' => $bloodDonor->is_available,
            'consent_to_contact' => $bloodDonor->consent_to_contact,
            'status' => $bloodDonor->status,
        ];

        if ($bloodDonor->relationLoaded('bloodGroup')) {
            $data['bloodGroup'] = $bloodDonor->bloodGroup;
        }

        return $data;
    }

    /** @return array<string, mixed> */
    protected function formatBloodRequest(?BloodRequest $bloodRequest): array
    {
        if ($bloodRequest === null) {
            return [];
        }

        $data = [
            'id' => $bloodRequest->id,
            'patient_name' => $bloodRequest->patient_name,
            'contact_name' => $bloodRequest->contact_name,
            'contact_phone' => $bloodRequest->contact_phone,
            'blood_group_id' => $bloodRequest->blood_group_id,
            'district_id' => $bloodRequest->district_id,
            'upazila_id' => $bloodRequest->upazila_id,
            'union_id' => $bloodRequest->union_id,
            'hospital_name' => $bloodRequest->hospital_name,
            'hospital_address' => $bloodRequest->hospital_address,
            'bags_needed' => $bloodRequest->bags_needed,
            'needed_at' => $bloodRequest->needed_at,
            'urgency' => $bloodRequest->urgency,
            'request_status' => $bloodRequest->request_status,
            'verified_at' => $bloodRequest->verified_at,
            'verified_by' => $bloodRequest->verified_by,
            'expires_at' => $bloodRequest->expires_at,
            'notes' => $bloodRequest->notes,
            'status' => $bloodRequest->status,
        ];

        if ($bloodRequest->relationLoaded('statusLogs')) {
            $data['status_logs'] = $bloodRequest->statusLogs;
        }

        if ($bloodRequest->relationLoaded('requestDonors')) {
            $data['request_donors'] = $bloodRequest->requestDonors->map(fn (RequestDonor $item): array => $this->formatRequestDonor($item))->all();
        }

        if ($bloodRequest->relationLoaded('donations')) {
            $data['donations'] = $bloodRequest->donations->map(fn (BloodDonation $item): array => $this->formatDonation($item))->all();
        }

        if ($bloodRequest->relationLoaded('bloodGroup')) {
            $data['bloodGroup'] = $bloodRequest->bloodGroup;
        }

        return $data;
    }

    /** @return array<string, mixed> */
    protected function formatDonation(?BloodDonation $donation): array
    {
        if ($donation === null) {
            return [];
        }

        $data = [
            'id' => $donation->id,
            'blood_donor_id' => $donation->blood_donor_id,
            'blood_request_id' => $donation->blood_request_id,
            'donated_at' => $donation->donated_at,
            'bags' => $donation->bags,
            'hospital_name' => $donation->hospital_name,
            'notes' => $donation->notes,
            'donation_status' => $donation->donation_status,
            'verified_at' => $donation->verified_at,
            'verified_by' => $donation->verified_by,
        ];

        if ($donation->relationLoaded('bloodDonor')) {
            $data['bloodDonor'] = $this->formatBloodDonor($donation->bloodDonor);
        }

        return $data;
    }

    /** @return array<string, mixed> */
    protected function formatDonorContactRequest(?DonorContactRequest $contactRequest): array
    {
        if ($contactRequest === null) {
            return [];
        }

        $data = [
            'id' => $contactRequest->id,
            'blood_donor_id' => $contactRequest->blood_donor_id,
            'blood_request_id' => $contactRequest->blood_request_id,
            'requester_name' => $contactRequest->requester_name,
            'requester_phone' => $contactRequest->requester_phone,
            'message' => $contactRequest->message,
            'contact_status' => $contactRequest->contact_status,
            'handled_at' => $contactRequest->handled_at,
        ];

        if ($contactRequest->relationLoaded('bloodDonor')) {
            $data['blood_donor'] = $this->formatBloodDonor($contactRequest->bloodDonor);
        }

        return $data;
    }

    /** @return array<string, mixed> */
    protected function formatRequestDonor(?RequestDonor $requestDonor): array
    {
        if ($requestDonor === null) {
            return [];
        }

        $data = [
            'id' => $requestDonor->id,
            'blood_request_id' => $requestDonor->blood_request_id,
            'blood_donor_id' => $requestDonor->blood_donor_id,
            'response_status' => $requestDonor->response_status,
            'contacted_at' => $requestDonor->contacted_at,
            'responded_at' => $requestDonor->responded_at,
            'notes' => $requestDonor->notes,
        ];

        if ($requestDonor->relationLoaded('bloodDonor')) {
            $data['blood_donor'] = $this->formatBloodDonor($requestDonor->bloodDonor);
        }

        return $data;
    }

    /** @return array<string, mixed> */
    protected function formatPublicDonor(?BloodDonor $bloodDonor): array
    {
        if ($bloodDonor === null) {
            return [];
        }

        $data = [
            'id' => $bloodDonor->id,
            'name' => $bloodDonor->name,
            'blood_group' => $bloodDonor->bloodGroup?->name,
            'district' => $bloodDonor->district?->name,
            'upazila' => $bloodDonor->upazila?->name,
            'union' => $bloodDonor->union?->name,
            'is_available' => $bloodDonor->is_available,
        ];

        return $data;
    }

    /** @return array<string, mixed> */
    protected function validateBloodDonor(Request $request, ?BloodDonor $bloodDonor = null): array
    {
        $data = array_merge($bloodDonor?->only(['district_id', 'upazila_id', 'union_id']) ?? [], $request->all());
        $rules = [
            'blood_group_id' => ['required', 'integer', Rule::exists('blood_groups', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'district_id' => ['nullable', 'required_with:upazila_id', 'integer', Rule::exists('districts', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'upazila_id' => ['nullable', 'required_with:union_id', 'integer', Rule::exists('upazilas', 'id')->where('district_id', $data['district_id'] ?? null)->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'union_id' => ['nullable', 'integer', Rule::exists('unions', 'id')->where('upazila_id', $data['upazila_id'] ?? null)->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
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
        ];

        if ($bloodDonor !== null) {
            foreach ($rules as $field => $fieldRules) {
                $rules[$field] = array_merge(['sometimes'], $fieldRules);
            }
            $rules['consent_to_contact'] = ['sometimes', 'boolean'];
            $rules['status'] = ['sometimes', 'integer', 'in:0,1'];
        }

        return Validator::make($data, $rules)->validate();
    }

    /** @return array<string, mixed> */
    protected function validateBloodRequest(Request $request, ?BloodRequest $bloodRequest = null): array
    {
        $data = array_merge($bloodRequest?->only(['district_id', 'upazila_id', 'union_id']) ?? [], $request->all());
        $rules = [
            'blood_group_id' => ['required', 'integer', Rule::exists('blood_groups', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'district_id' => ['nullable', 'required_with:upazila_id', 'integer', Rule::exists('districts', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'upazila_id' => ['nullable', 'required_with:union_id', 'integer', Rule::exists('upazilas', 'id')->where('district_id', $data['district_id'] ?? null)->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'union_id' => ['nullable', 'integer', Rule::exists('unions', 'id')->where('upazila_id', $data['upazila_id'] ?? null)->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'contact_phone' => ['required', 'string', 'regex:/^(?:\+?88)?01[3-9][0-9]{8}$/'],
            'patient_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'hospital_name' => ['required', 'string', 'max:255'],
            'hospital_address' => ['nullable', 'string', 'max:2000'],
            'bags_needed' => ['required', 'integer', 'min:1', 'max:20'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:needed_at'],
            'needed_at' => ['required', 'date', 'after:now'],
            'urgency' => ['sometimes', Rule::in(['normal', 'urgent', 'emergency', 'critical'])],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];

        if ($bloodRequest !== null) {
            foreach ($rules as $field => $fieldRules) {
                $rules[$field] = array_merge(['sometimes'], $fieldRules);
            }
            $rules['expires_at'] = ['sometimes', 'nullable', 'date', 'after_or_equal:'.($request->input('needed_at') ?? $bloodRequest->needed_at->toDateTimeString())];
            $rules['request_status'] = ['sometimes', Rule::in(['pending', 'verified', 'in_progress', 'fulfilled', 'cancelled', 'expired'])];
        }

        return Validator::make($data, $rules)->validate();
    }
}
