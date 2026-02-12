<?php

namespace App\Contexts\Device\Domain\Events;

use App\Contexts\Device\Domain\Device;
use App\Contexts\Device\Domain\Enums\DeviceStatus;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Device $device,
        public DeviceStatus $previousStatus,
        public DeviceStatus $newStatus
    ) {}
}
