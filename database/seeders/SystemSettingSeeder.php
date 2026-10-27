<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['site_name' => 'মান্দা রক্তদান', 'contact_email' => ''] as $key => $value) {
            SystemSetting::withoutGlobalScopes()->firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
