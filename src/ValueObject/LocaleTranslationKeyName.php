<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Exception\LocaleInvalidTranslationKeyException;

final readonly class LocaleTranslationKeyName implements \Stringable
{
    public function __construct(private string $value)
    {
        $value = trim($value);
        if ('' === $value || !preg_match('/^[a-z][a-z0-9_]*(?:\.[a-z0-9_]+)*$/', $value)) {
            throw LocaleInvalidTranslationKeyException::forValue($value);
        }

        $this->value = $value;
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
