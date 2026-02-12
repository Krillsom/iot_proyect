<?php

namespace App\Contexts\Device\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class MacAddress
{
    public function __construct(
        private string $value
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty($this->value)) {
            throw new InvalidArgumentException('MAC address cannot be empty');
        }

        // Validar formato MAC: XX:XX:XX:XX:XX:XX o XX-XX-XX-XX-XX-XX
        $pattern = '/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/';
        if (!preg_match($pattern, $this->value)) {
            throw new InvalidArgumentException('Invalid MAC address format');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function normalized(): string
    {
        // Normalizar a formato con dos puntos
        return strtoupper(str_replace('-', ':', $this->value));
    }

    public function equals(self $other): bool
    {
        return $this->normalized() === $other->normalized();
    }

    public function __toString(): string
    {
        return $this->normalized();
    }
}
