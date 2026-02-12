<?php

namespace App\Contexts\Device\Domain\Commands;

use App\Contexts\Device\Domain\Enums\DeviceStatus;

final readonly class UpdateDeviceStatusCommand
{
    public function __construct(
        public string $deviceId,
        public DeviceStatus $status,
    ) {}
}
