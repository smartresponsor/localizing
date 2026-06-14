<?php

declare(strict_types=1);

namespace App\Localizing\ValueObject;

use App\Localizing\Exception\LocaleInvalidTranslationKeyException;

final readonly class LocaleTranslationKeyName implements \Stringable
{
    private string $value;

    public function __construct(string $value)
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
