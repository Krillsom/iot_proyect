<?php

namespace App\Shared\Domain\Exceptions;

use Exception;

class DomainException extends Exception
{
    public static function notFound(string $entity, string $id): self
    {
        return new self("$entity with ID '$id' not found");
    }

    public static function alreadyExists(string $entity, string $field, string $value): self
    {
        return new self("$entity with $field '$value' already exists");
    }

    public static function invalidOperation(string $message): self
    {
        return new self($message);
    }
}
