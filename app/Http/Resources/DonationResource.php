<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'blood_donor_id' => $this->blood_donor_id,
            'blood_request_id' => $this->blood_request_id,
            'donated_at' => $this->donated_at,
            'bags' => $this->bags,
            'hospital_name' => $this->hospital_name,
            'notes' => $this->notes,
            'donation_status' => $this->donation_status, 'verified_at' => $this->verified_at, 'verified_by' => $this->verified_by,
            'bloodDonor' => new BloodDonorResource($this->whenLoaded('bloodDonor')),
        ];
    }
}
