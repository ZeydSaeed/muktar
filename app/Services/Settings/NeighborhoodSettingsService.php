<?php

namespace App\Services\Settings;

use App\Models\AppSetting;

class NeighborhoodSettingsService
{
    public const KEY = 'neighborhood';

    public function current(): string
    {
        $stored = AppSetting::query()->where('key', self::KEY)->value('value');
        $name = is_string($stored) ? trim($stored) : '';

        return $name !== '' ? $name : (string) config('mukhtar.neighborhood');
    }

    public function update(string $neighborhood): void
    {
        AppSetting::query()->updateOrCreate(
            ['key' => self::KEY],
            ['value' => $neighborhood],
        );
    }
}
