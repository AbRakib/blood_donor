<?php

namespace App\Http\Requests;

class UpdateBloodDonorRequest extends StoreBloodDonorRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('blood_donor')) ?? false;
    }

    public function validationData(): array
    {
        return array_merge($this->route('blood_donor')->only(['district_id', 'upazila_id', 'union_id']), parent::validationData());
    }

    public function rules(): array
    {
        $rules = parent::rules();
        foreach ($rules as $key => $value) {
            $rules[$key] = array_merge(['sometimes'], $value);
        }
        $rules['consent_to_contact'] = ['sometimes', 'boolean'];
        $rules['status'] = ['sometimes', 'integer', 'in:0,1'];
        $rules['last_donation_date'] = ['prohibited'];
        $rules['total_donations'] = ['prohibited'];

        return $rules;
    }
}
