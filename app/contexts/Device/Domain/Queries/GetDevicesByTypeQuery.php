<?php

namespace App\Contexts\Device\Domain\Queries;

use App\Contexts\Device\Domain\Enums\DeviceType;

final readonly class GetDevicesByTypeQuery
{
    public function __construct(
        public DeviceType $type,
    ) {}
}
