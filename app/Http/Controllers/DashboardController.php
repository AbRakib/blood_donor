<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
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
}
