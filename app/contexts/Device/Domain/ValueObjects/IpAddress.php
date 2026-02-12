<?php

namespace App\Contexts\Device\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class IpAddress
{
    public function __construct(
        private string $value
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty($this->value)) {
            throw new InvalidArgumentException('IP address cannot be empty');
        }

        if (!filter_var($this->value, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException('Invalid IP address format');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isIpv4(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    public function isIpv6(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
