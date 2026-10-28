<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create(): View
    {
        $common_data = (object) [
            'page_title' => __('Admin login'),
            'title' => __('Admin login'),
        ];

        return view('login')->with(compact('common_data'));
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::attempt(array_merge($credentials, ['is_admin' => true]))) {
            throw ValidationException::withMessages(['email' => __('The email or password is incorrect.')]);
        }
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $locale = app()->getLocale();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->put('locale', $locale);

        return redirect()->route('login')->with('status', __('You have successfully logged out.'));
    }
}
