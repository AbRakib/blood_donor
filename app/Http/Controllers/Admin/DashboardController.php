<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodDonation;
use App\Models\BloodRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $pages = [
            'dashboard' => 'dashboard',
            'donors' => 'admin.blood-donors.index',
            'requests' => 'admin.blood-requests.index',
            'groups' => 'admin.blood-groups.index',
            'contacts' => 'admin.contact-requests.index',
            'donations' => 'admin.donations.index',
            'reports' => 'admin.reports.index',
            'settings' => 'admin.settings.index',
        ];
        $page = $request->query('page');

        if (is_string($page) && isset($pages[$page])) {
            return redirect()->route($pages[$page]);
        }

        $common_data = (object) [
            'page_title' => __('Dashboard'),
            'title' => __('Dashboard'),
        ];

        return view('dashboard')->with(compact('common_data'));
    }

    public function getOverview(): JsonResponse
    {
        return response()->json(['data' => $this->overview()]);
    }

    public function getStatistics(): JsonResponse
    {
        return response()->json(['data' => $this->reportSummary()]);
    }

    public function getRecentRequests(): JsonResponse
    {
        return $this->collectionResponse($this->recentRequests(), $this->formatBloodRequest(...));
    }

    /** @return array<string, mixed> */
    private function overview(): array
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

        return array_merge($this->reportSummary(), ['monthly_donations' => $months]);
    }

    /** @return Collection<int, BloodRequest> */
    private function recentRequests(): Collection
    {
        return BloodRequest::query()
            ->active()
            ->with('bloodGroup')
            ->orderByDesc('id')
            ->limit(10)
            ->get();
    }
}
