<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicDonorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'name' => $this->name,
            'blood_group' => $this->bloodGroup?->name,
            'district' => $this->district?->name, 'upazila' => $this->upazila?->name,
            'union' => $this->union?->name, 'is_available' => $this->is_available,
        ];
    }
}
