<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateBloodRequestRequest extends StoreBloodRequestRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('blood_request')) ?? false;
    }

    public function validationData(): array
    {
        return array_merge($this->route('blood_request')->only(['district_id', 'upazila_id', 'union_id']), parent::validationData());
    }

    public function rules(): array
    {
        $rules = parent::rules();
        foreach ($rules as $key => $value) {
            $rules[$key] = array_merge(['sometimes'], $value);
        }
        $rules['expires_at'] = ['sometimes', 'nullable', 'date', 'after_or_equal:'.($this->input('needed_at') ?? $this->route('blood_request')->needed_at->toDateTimeString())];
        $rules['request_status'] = ['sometimes', Rule::in(['pending', 'verified', 'in_progress', 'fulfilled', 'cancelled', 'expired'])];

        return $rules;
    }
}
