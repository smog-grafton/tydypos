<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class AppSettings
{
    public function all(): array
    {
        $defaults = config('tydypos');

        if (! $this->settingsTableExists()) {
            return $defaults;
        }

        $settings = Setting::query()
            ->get()
            ->reduce(function (array $carry, Setting $setting): array {
                Arr::set($carry, "{$setting->group}.{$setting->key}", $setting->value);

                return $carry;
            }, []);

        return array_replace_recursive($defaults, $settings);
    }

    public function get(string $path, mixed $default = null): mixed
    {
        return data_get($this->all(), $path, $default);
    }

    protected function settingsTableExists(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (\Throwable) {
            return false;
        }
    }
}
