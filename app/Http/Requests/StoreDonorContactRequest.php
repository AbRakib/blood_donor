<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonorContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'requester_phone' => ['required', 'string', 'regex:/^(?:\+?88)?01[3-9][0-9]{8}$/'],
            'blood_donor_id' => ['required', 'integer', Rule::exists('blood_donors', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')->where('is_available', true)->where('consent_to_contact', true)],
            'requester_name' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ];
    }
}
