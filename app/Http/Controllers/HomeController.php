<?php

namespace App\Http\Controllers;

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

        return view('home')->with(compact('common_data', 'companyName'));
    }
}
