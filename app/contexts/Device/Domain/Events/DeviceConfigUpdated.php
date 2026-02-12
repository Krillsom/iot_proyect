<?php

namespace App\Contexts\Device\Domain\Events;

use App\Contexts\Device\Domain\Device;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceConfigUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Device $device
    ) {}
}
