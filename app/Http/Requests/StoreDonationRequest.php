<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'blood_donor_id' => ['required', 'integer', Rule::exists('blood_donors', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'blood_request_id' => ['nullable', 'integer', Rule::exists('blood_requests', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'donated_at' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'bags' => ['required', 'integer', 'in:1'],
            'hospital_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
