<?php

namespace App\Contexts\Device\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class DeviceId
{
    public function __construct(
        private string $value
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty($this->value)) {
            throw new InvalidArgumentException('Device ID cannot be empty');
        }

        // Validar formato UUID
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $this->value)) {
            throw new InvalidArgumentException('Device ID must be a valid UUID');
        }
    }

    public function value(): string
    {
        return $this->value;
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
