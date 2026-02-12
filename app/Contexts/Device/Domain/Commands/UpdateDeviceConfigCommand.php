<?php

namespace App\Contexts\Device\Domain\Commands;

final readonly class UpdateDeviceConfigCommand
{
    public function __construct(
        public string $deviceId,
        public ?string $name,
        public ?string $ipAddress,
        public ?array $metadata,
    ) {}
}
