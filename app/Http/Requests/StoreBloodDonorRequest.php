<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBloodDonorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'blood_group_id' => ['required', 'integer', Rule::exists('blood_groups', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'district_id' => ['nullable', 'required_with:upazila_id', 'integer', Rule::exists('districts', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'upazila_id' => ['nullable', 'required_with:union_id', 'integer', Rule::exists('upazilas', 'id')->where('district_id', $this->validationData()['district_id'] ?? null)->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'union_id' => ['nullable', 'integer', Rule::exists('unions', 'id')->where('upazila_id', $this->validationData()['upazila_id'] ?? null)->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
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
    }
}
