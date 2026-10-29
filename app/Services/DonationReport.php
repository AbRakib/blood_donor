<?php

namespace App\Services;

use App\Models\BloodDonation;
use App\Models\BloodDonor;
use App\Models\BloodGroup;
use App\Models\BloodRequest;
use Illuminate\Database\Eloquent\Builder;

class DonationReport
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
            'donations' => BloodDonation::query()->active()->verified()->count(),
            'bags_donated' => (int) BloodDonation::query()->active()->verified()->sum('bags'),
            'blood_groups' => BloodGroup::query()->active()->withCount(['donors' => fn (Builder $query): Builder => $query->active()])->orderBy('sort_order')->get(),
        ];
    }

    /** @return array<string, mixed> */
    public function overview(): array
    {
        $months = [];

        for ($offset = 5; $offset >= 0; $offset--) {
            $start = now()->startOfMonth()->subMonths($offset);
            $months[] = [
                'month' => $start->format('Y-m'),
                'donations' => BloodDonation::query()
                    ->active()
                    ->verified()
                    ->whereBetween('donated_at', [
                        $start->toDateString(),
                        $start->copy()->endOfMonth()->toDateString(),
                    ])
                    ->count(),
            ];
        }

        return array_merge($this->summary(), ['monthly_donations' => $months]);
    }
}
