<?php

namespace Tests\Fakes;

use App\Services\Desktop\MachineIdentity;

class FakeMachineIdentity implements MachineIdentity
{
    public function __construct(
        private string $processorId,
    ) {}

    public function processorId(): string
    {
        return $this->processorId;
    }
}
