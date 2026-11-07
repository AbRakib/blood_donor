<?php

namespace App\Http\Controllers;

use App\Models\BloodDonor;
use App\Models\Setting;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $common_data = (object) [
            'page_title' => __('Manda Blood Donation Program'),
            'title' => __('Manda Blood Donation Program'),
        ];

        $companyName = trim((string) Setting::query()->orderBy('id')->value('name'));
        $companyName = $companyName !== '' ? $companyName : 'Help Service';

        $topDonors = BloodDonor::query()
            ->matchingFilters()
            ->with('bloodGroup')
            ->where('total_donations', '>', 0)
            ->orderByDesc('total_donations')
            ->orderBy('name')
            ->orderBy('id')
            ->limit(10)
            ->get();

        if ($topDonors->isEmpty()) {
            $topDonors = BloodDonor::query()
                ->matchingFilters()
                ->with('bloodGroup')
                ->inRandomOrder()
                ->limit(10)
                ->get();
        }

        return view('home')->with(compact('common_data', 'companyName', 'topDonors'));
    }
}
