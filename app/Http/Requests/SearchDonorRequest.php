<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchDonorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'blood_group_id' => ['nullable', 'integer', Rule::exists('blood_groups', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'district_id' => ['nullable', 'required_with:upazila_id', 'integer', Rule::exists('districts', 'id')->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'upazila_id' => ['nullable', 'required_with:union_id', 'integer', Rule::exists('upazilas', 'id')->where('district_id', $this->input('district_id'))->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'union_id' => ['nullable', 'integer', Rule::exists('unions', 'id')->where('upazila_id', $this->input('upazila_id'))->where('status', 1)->where('deleted', 0)->whereNull('deleted_at')],
            'q' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
