<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BloodDonorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'blood_group_id' => $this->blood_group_id,
            'phone' => $this->consent_to_contact && $request->user()?->is_admin ? $this->phone : null,
            'area' => $this->area,
            'address' => $this->address,
            'district_id' => $this->district_id,
            'upazila_id' => $this->upazila_id,
            'union_id' => $this->union_id,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'total_donations' => $this->total_donations,
            'last_donation_date' => $this->last_donation_date,
            'is_available' => $this->is_available,
            'consent_to_contact' => $this->consent_to_contact,
            'status' => $this->status,
            'bloodGroup' => $this->whenLoaded('bloodGroup'),
        ];
    }
}
