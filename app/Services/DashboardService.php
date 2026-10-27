<?php

namespace App\Services;

use App\Models\BloodDonation;
use App\Models\BloodRequest;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    public function __construct(private ReportService $reports) {}

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
                    ->where('donation_status', 'verified')
                    ->whereBetween('donated_at', [
                        $start->toDateString(),
                        $start->copy()->endOfMonth()->toDateString(),
                    ])
                    ->count(),
            ];
        }

        return array_merge($this->reports->summary(), ['monthly_donations' => $months]);
    }

    /** @return array<string, mixed> */
    public function statistics(): array
    {
        return $this->reports->summary();
    }

    /** @return Collection<int, BloodRequest> */
    public function recentRequests(): Collection
    {
        return BloodRequest::query()
            ->active()
            ->with('bloodGroup')
            ->orderByDesc('id')
            ->limit(10)
            ->get();
    }
}
