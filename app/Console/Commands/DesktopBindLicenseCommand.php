<?php

namespace App\Console\Commands;

use App\Services\Desktop\LicenseService;
use Illuminate\Console\Command;

class DesktopBindLicenseCommand extends Command
{
    protected $signature = 'desktop:bind-license';

    protected $description = 'Bind the desktop license to this computer processor ID';

    public function handle(LicenseService $licenses): int
    {
        $processorId = $licenses->bind();
        $this->info($processorId);

        return self::SUCCESS;
    }
}
