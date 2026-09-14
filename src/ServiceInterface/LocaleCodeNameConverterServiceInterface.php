<?php

declare(strict_types=1);

namespace App\Localizing\ServiceInterface;

/**
 * Converts locale identifiers and localized display names in both directions.
 */
interface LocaleCodeNameConverterServiceInterface
{
    /**
     * Resolves a localized display name back to its locale code.
     */
    public function convertNameToCode(string $name, ?string $displayLocaleCode = null): string;

    /**
     * Resolves a locale code to a display name in the requested language.
     */
    public function convertCodeToName(string $code, ?string $displayLocaleCode = null): string;
}
