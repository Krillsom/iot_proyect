<?php

namespace App\Contexts\MqttIngestion\Domain\ValueObjects;

use App\Shared\Domain\Exceptions\DomainException;

final class Topic
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
        if (empty($this->value)) {
            throw new DomainException("MQTT topic cannot be empty");
        }

        if (!str_starts_with($this->value, '/')) {
            throw new DomainException("MQTT topic must start with /");
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function extractGatewayIdentifier(): ?string
    {
        // Extrae identificador de gateway desde topic como /sur/g2/status
        if (preg_match('/\/([^\/]+)\/status/', $this->value, $matches)) {
            return strtoupper($matches[1]);
        }
        return null;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
