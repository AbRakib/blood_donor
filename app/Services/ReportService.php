<?php

namespace App\Services;

use App\Models\BloodDonation;
use App\Models\BloodDonor;
use App\Models\BloodGroup;
use App\Models\BloodRequest;

class ReportService
{
    /** @return array<string, mixed> */
    public function summary(): array
    {
        return [
            'donors' => BloodDonor::query()->active()->count(),
            'available_donors' => BloodDonor::query()->active()->where('is_available', true)->count(),
            'open_requests' => BloodRequest::query()->active()->whereIn('request_status', ['pending', 'verified', 'in_progress'])->count(),
            'pending_requests' => BloodRequest::query()->active()->where('request_status', 'pending')->count(),
            'urgent_requests' => BloodRequest::query()->active()->whereIn('request_status', ['pending', 'verified', 'in_progress'])->whereIn('urgency', ['urgent', 'emergency', 'critical'])->count(),
            'donations' => BloodDonation::query()->active()->where('donation_status', 'verified')->count(),
            'bags_donated' => (int) BloodDonation::query()->active()->where('donation_status', 'verified')->sum('bags'),
            'blood_groups' => BloodGroup::query()->active()->withCount(['donors' => fn ($query) => $query->active()])->orderBy('sort_order')->get(),
        ];
    }
}
