<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['name', 'email', 'phone', 'address', 'logo', 'footer_logo', 'meta_icon', 'facebook', 'twitter', 'instagram', 'linkedin', 'status'])]
class Setting extends DomainModel
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    /** @param array<string, mixed> $data */
    public static function saveWebsiteSettings(array $data): Setting
    {
        return (new static)->getConnection()->transaction(function () use ($data): Setting {
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
    }
}
