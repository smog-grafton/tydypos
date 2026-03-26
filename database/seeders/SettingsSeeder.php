<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'branding', 'key' => 'app_name', 'value' => 'Tydy POS', 'type' => 'string'],
            ['group' => 'branding', 'key' => 'tagline', 'value' => 'Fast cashier workflows with dependable receipts.', 'type' => 'string'],
            ['group' => 'branding', 'key' => 'company_name', 'value' => 'Tydy POS', 'type' => 'string'],
            ['group' => 'branding', 'key' => 'support_email', 'value' => 'support@tydypos.test', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'primary', 'value' => '#2563EB', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'primary_dark', 'value' => '#1D4ED8', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'accent', 'value' => '#F59E0B', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'success', 'value' => '#16A34A', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'danger', 'value' => '#DC2626', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'background', 'value' => '#F8FAFC', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'surface', 'value' => '#FFFFFF', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'text_primary', 'value' => '#0F172A', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'text_muted', 'value' => '#64748B', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'border', 'value' => '#E2E8F0', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting,
            );
        }
    }
}
