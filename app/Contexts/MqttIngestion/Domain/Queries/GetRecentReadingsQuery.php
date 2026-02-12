<?php

namespace App\Contexts\MqttIngestion\Domain\Queries;

final readonly class GetRecentReadingsQuery
{
    public function __construct(
        public int $limit = 20
    ) {}
}
