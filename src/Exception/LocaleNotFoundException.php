<?php

declare(strict_types=1);

namespace App\Exception;

final class LocaleNotFoundException extends \RuntimeException
{
    public static function notFound(string $localeCode): self
    {
        return new self(sprintf('Locale "%s" cannot be found.', $localeCode));
    }

    /**
     * @param list<string> $availableLocaleCodes
     */
    public static function notAvailable(string $localeCode, array $availableLocaleCodes): self
    {
        return new self(sprintf(
            'Locale "%s" is not available. Available locales: "%s".',
            $localeCode,
            implode('", "', $availableLocaleCodes),
        ));
    }
}
