<?php

declare(strict_types=1);

namespace App\Exception;

final class LocaleInvalidCodeException extends \InvalidArgumentException
{
    public static function forValue(string $value): self
    {
        return new self(sprintf('Invalid locale code "%s".', $value));
    }
}
