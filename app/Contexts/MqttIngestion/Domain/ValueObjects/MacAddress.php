<?php

namespace App\Contexts\MqttIngestion\Domain\ValueObjects;

use App\Shared\Domain\Exceptions\DomainException;

final class MacAddress
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate();
    }

    public static function from(string $value): self
    {
        return new self($value);
    }

    private function validate(): void
    {
        // Formato flexible para MAC addresses IoT
        if (strlen($this->value) < 6 || strlen($this->value) > 20) {
            throw new DomainException("Invalid MAC address format: {$this->value}");
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
