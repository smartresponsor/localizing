<?php

declare(strict_types=1);

namespace App\Localizing\ValueObject;

use App\Localizing\Exception\LocaleInvalidCodeException;

final readonly class LocaleCode implements \Stringable
{
    private string $value;

    public function __construct(string $value)
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
