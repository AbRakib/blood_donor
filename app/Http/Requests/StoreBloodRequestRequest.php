<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBloodRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'blood_group_id' => ['required', 'integer', Rule::exists('blood_groups', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'district_id' => ['nullable', 'required_with:upazila_id', 'integer', Rule::exists('districts', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'upazila_id' => ['nullable', 'required_with:union_id', 'integer', Rule::exists('upazilas', 'id')->where('district_id', $this->validationData()['district_id'] ?? null)->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'union_id' => ['nullable', 'integer', Rule::exists('unions', 'id')->where('upazila_id', $this->validationData()['upazila_id'] ?? null)->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
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
    }
}
