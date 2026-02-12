<?php

namespace App\Shared\Domain\ValueObjects;

use Ramsey\Uuid\Uuid as RamseyUuid;

final readonly class Uuid
{
    public function __construct(
        private string $value
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (!RamseyUuid::isValid($this->value)) {
            throw new \InvalidArgumentException('Invalid UUID format');
        }
    }

    public static function random(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
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
