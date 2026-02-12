<?php

namespace App\Contexts\Device\Domain\Queries;

final readonly class GetDeviceQuery
{
    public function __construct(
        public string $deviceId,
    ) {}
}
