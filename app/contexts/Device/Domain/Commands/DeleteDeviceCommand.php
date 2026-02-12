<?php

namespace App\Contexts\Device\Domain\Commands;

final readonly class DeleteDeviceCommand
{
    public function __construct(
        public string $deviceId,
    ) {}
}
