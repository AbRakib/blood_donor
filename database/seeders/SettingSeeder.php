<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = Setting::query()->first();
        if ($setting === null) {
            $setting = new Setting;
            $setting->status = 1;
            $setting->save();
        }
    }
}
