<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $common_data = (object) [
            'page_title' => __('Manda Blood Donation Program'),
            'title' => __('Manda Blood Donation Program'),
        ];

        return view('home')->with(compact('common_data'));
    }
}
