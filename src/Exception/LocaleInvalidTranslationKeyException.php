<?php

declare(strict_types=1);

namespace App\Localizing\Exception;

final class LocaleInvalidTranslationKeyException extends \InvalidArgumentException
{
    public static function forValue(string $value): self
    {
        return new self(sprintf('Invalid translation key "%s".', $value));
    }
}
