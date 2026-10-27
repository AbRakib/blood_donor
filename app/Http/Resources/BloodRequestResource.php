<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BloodRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_name' => $this->patient_name,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'blood_group_id' => $this->blood_group_id,
            'district_id' => $this->district_id,
            'upazila_id' => $this->upazila_id,
            'union_id' => $this->union_id,
            'hospital_name' => $this->hospital_name,
            'hospital_address' => $this->hospital_address,
            'bags_needed' => $this->bags_needed,
            'needed_at' => $this->needed_at,
            'urgency' => $this->urgency,
            'request_status' => $this->request_status,
            'verified_at' => $this->verified_at, 'verified_by' => $this->verified_by, 'expires_at' => $this->expires_at,
            'status_logs' => $this->whenLoaded('statusLogs'),
            'request_donors' => RequestDonorResource::collection($this->whenLoaded('requestDonors')),
            'donations' => DonationResource::collection($this->whenLoaded('donations')),
            'notes' => $this->notes,
            'status' => $this->status,
            'bloodGroup' => $this->whenLoaded('bloodGroup'),
        ];
    }
}
