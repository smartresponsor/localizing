<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Exception\LocaleInvalidCodeException;

final readonly class LocaleCode implements \Stringable
{
    public function __construct(private string $value)
    {
        $value = trim($value);
        if ('' === $value || !preg_match('/^[a-z]{2,3}(?:[-_][A-Z]{2})?$/', $value)) {
            throw LocaleInvalidCodeException::forValue($value);
        }

        $this->value = str_replace('_', '-', $value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function language(): string
    {
        return strtolower(substr($this->value, 0, 2));
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
