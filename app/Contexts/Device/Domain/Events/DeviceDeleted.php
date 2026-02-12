<?php

namespace App\Contexts\Device\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $deviceId
    ) {}
}
