<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SystemSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SettingController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        if (! $request->expectsJson()) {
            $common_data = (object) [
                'page_title' => __('Settings'),
                'title' => __('Settings'),
            ];

            return view('admin.settings', ['setting' => $this->withImageUrls(Setting::query()->orderBy('id')->first())])->with(compact('common_data'));
        }

        return response()->json(['data' => $this->withImageUrls(Setting::query()->orderBy('id')->first())]);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'integer', 'in:0,1'],
        ];
        foreach (['logo', 'footer_logo', 'meta_icon'] as $field) {
            $rules[$field] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'];
        }
        foreach (['facebook', 'twitter', 'instagram', 'linkedin'] as $field) {
            $rules[$field] = ['nullable', 'url:http,https', 'max:255'];
        }
        $data = $request->validate($rules);
        $uploadedPaths = [];
        try {
            foreach (['logo', 'footer_logo', 'meta_icon'] as $field) {
                unset($data[$field]);
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('settings', 'public');
                    if ($path === false) {
                        throw new \RuntimeException('Unable to store the settings image.');
                    }
                    $data[$field] = $path;
                    $uploadedPaths[] = $path;
                }
            }
            $setting = DB::transaction(function () use ($data): Setting {
                $setting = Setting::query()->orderBy('id')->lockForUpdate()->first() ?? new Setting;
                $data += $setting->only(['name', 'email', 'phone', 'address', 'logo', 'footer_logo', 'meta_icon', 'facebook', 'twitter', 'instagram', 'linkedin', 'status']);
                $setting->name = $data['name'];
                $setting->email = $data['email'];
                $setting->phone = $data['phone'];
                $setting->address = $data['address'];
                $setting->logo = $data['logo'];
                $setting->footer_logo = $data['footer_logo'];
                $setting->meta_icon = $data['meta_icon'];
                $setting->facebook = $data['facebook'];
                $setting->twitter = $data['twitter'];
                $setting->instagram = $data['instagram'];
                $setting->linkedin = $data['linkedin'];
                $setting->status = $data['status'];
                $setting->save();

                return $setting->refresh();
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);
            throw $exception;
        }

        return response()->json(['data' => $this->withImageUrls($setting)]);
    }

    private function withImageUrls(?Setting $setting): ?Setting
    {
        if ($setting) {
            foreach (['logo', 'footer_logo', 'meta_icon'] as $field) {
                $setting->{$field.'_url'} = $setting->{$field} ? Storage::disk('public')->url($setting->{$field}) : null;
            }
        }

        return $setting;
    }

    public function update(Request $request, SystemSetting $systemSetting): JsonResponse
    {
        $data = $request->validate(['value' => ['nullable', 'string', 'max:5000']]);
        DB::transaction(function () use ($systemSetting, $data): void {
            $data += $systemSetting->only(['value']);
            $systemSetting->value = $data['value'];
            $systemSetting->save();
        });

        return response()->json(['data' => $systemSetting]);
    }
}
