<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestDonorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'blood_request_id' => $this->blood_request_id, 'blood_donor_id' => $this->blood_donor_id,
            'response_status' => $this->response_status, 'contacted_at' => $this->contacted_at, 'responded_at' => $this->responded_at,
            'notes' => $this->notes, 'blood_donor' => new BloodDonorResource($this->whenLoaded('bloodDonor'))];
    }
}
