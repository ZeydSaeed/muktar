<?php

namespace App\Actions\Settings;

use App\Services\Settings\NeighborhoodSettingsService;

class UpdateNeighborhoodAction
{
    public function __construct(
        private NeighborhoodSettingsService $settings,
    ) {}

    public function execute(string $neighborhood): void
    {
        $this->settings->update($neighborhood);
    }
}
