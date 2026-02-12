<?php

namespace App\Contexts\MqttIngestion\Domain\ValueObjects;

final class Rssi
{
    private function __construct(
        private readonly int $value
    ) {}

    public static function from(int $value): self
    {
        return new self($value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function isStrong(): bool
    {
        return $this->value > -60;
    }

    public function isMedium(): bool
    {
        return $this->value >= -70 && $this->value <= -60;
    }

    public function isWeak(): bool
    {
        return $this->value < -70;
    }

    public function getQualityLabel(): string
    {
        return match(true) {
            $this->isStrong() => 'Excelente',
            $this->isMedium() => 'Bueno',
            default => 'Débil',
        };
    }
}
