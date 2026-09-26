<?php

namespace App\Console\Commands;

use App\Services\Desktop\MachineIdentity;
use Illuminate\Console\Command;

class DesktopFingerprintCommand extends Command
{
    protected $signature = 'desktop:fingerprint';

    protected $description = 'Print the processor ID used to lock a desktop license';

    public function handle(MachineIdentity $machine): int
    {
        $this->line($machine->processorId());

        return self::SUCCESS;
    }
}
