<?php

namespace App\Contexts\Device\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class GeoLocation
{
    public function __construct(
        private float $latitude,
        private float $longitude
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->latitude < -90 || $this->latitude > 90) {
            throw new InvalidArgumentException('Latitude must be between -90 and 90 degrees');
        }

        if ($this->longitude < -180 || $this->longitude > 180) {
            throw new InvalidArgumentException('Longitude must be between -180 and 180 degrees');
        }
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    public function toString(): string
    {
        return "{$this->latitude},{$this->longitude}";
    }

    public function equals(self $other): bool
    {
        return $this->latitude === $other->latitude 
            && $this->longitude === $other->longitude;
    }
}
