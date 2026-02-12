<?php

namespace App\Contexts\Device\Domain\Queries;

final readonly class GetDeviceHierarchyQuery
{
    public function __construct(
        public string $deviceId,
    ) {}
}
