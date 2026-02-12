<?php

namespace App\Contexts\Device\Domain\Queries;

use App\Contexts\Device\Domain\Enums\DeviceStatus;

final readonly class GetDevicesByStatusQuery
{
    public function __construct(
        public DeviceStatus $status,
    ) {}
}
