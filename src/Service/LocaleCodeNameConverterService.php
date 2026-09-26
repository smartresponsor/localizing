<?php

declare(strict_types=1);

namespace App\Localizing\Service;

use App\Localizing\ServiceInterface\LocaleCodeNameConverterServiceInterface;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Locales;

/**
 * Uses Symfony Intl locale data to translate between locale codes and display names.
 */
final class LocaleCodeNameConverterService implements LocaleCodeNameConverterServiceInterface
{
    /**
     * Resolves a localized display name to its locale code using Symfony Intl data.
     */
    public function convertNameToCode(string $name, ?string $displayLocaleCode = null): string
    {
        $names = Locales::getNames($displayLocaleCode ?? 'en');
        $code = array_search($name, $names, true);

        if (!is_string($code)) {
            throw new \InvalidArgumentException(sprintf('Cannot find locale code for display name "%s".', $name));
        }

        return $code;
    }

    /**
     * Resolves a locale code to a display name in the requested display locale.
     */
    public function convertCodeToName(string $code, ?string $displayLocaleCode = null): string
    {
        try {
            return Locales::getName($code, $displayLocaleCode ?? 'en');
        } catch (MissingResourceException $exception) {
            throw new \InvalidArgumentException(sprintf('Cannot find display name for locale code "%s".', $code), 0, $exception);
        }
    }
}
